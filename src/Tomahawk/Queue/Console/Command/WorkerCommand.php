<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessUtils;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Worker;

/**
 * Class WorkerCommand
 *
 * @package Tomahawk\Queue\Console\Command
 */
class WorkerCommand extends ContainerAwareCommand
{
    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var bool
     */
    protected $running = true;

    /**
     * @var bool
     */
    protected $shutdown = false;

    /***
     * @var bool
     */
    protected $paused = false;

    /**
     * @var array
     */
    protected $queues =[];

    /**
     * @var string
     */
    protected $command = '';

    protected function configure()
    {
        $this
            ->setName('work')
            ->setDescription('description')
            ->addOption('daemon', null, InputOption::VALUE_NONE, 'Run worker as a daemon')
            ->addArgument('queues', null, InputArgument::REQUIRED, 'Name of queues comma separated')
            ->setHelp('help')
        ;
    }

    /**
     * @param InputInterface $  input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Ideally we want to be able to run multiple workers for other queues
        // for forking and creating the pid file needs some work
        $pid = pcntl_fork();

        $folder = getcwd() . '/pid/';

        if ( ! file_exists($folder)) {
            mkdir($folder, 0755);
        }

        $pidFile = $folder . 'tomahawk_'.microtime(true).'.pid';

        if ($pid == -1) {
            syslog(1, 'Unable to start worker as a daemon');
            $output->writeln('Unable to start worker as a daemon');
            return 0;
        }
        else if ($pid) {
            file_put_contents($pidFile, $pid);
            $output->writeln('Worker started as a daemon');
            return 0;
        }


        $this->registerSigHandlers();
        $container = $this->getContainer();

        $symfonyStyle = new SymfonyStyle($input, $output);
        $output->setDecorated(true);

        $this->queues = explode(',', $input->getArgument('queues'));

        $storage = $container[StorageInterface::class];

        $this->worker = new Worker($storage, $this->queues);
        $this->worker->work();

        return 0;
    }

    protected function daemonize()
    {
        // @TODO - Get this working
        //$process = new Process();
        //$process->getPid();
    }

    public function shutDownNow()
    {
        echo 'Shutting down NOW';
        exit(0);
    }

    public function shutdown()
    {
        echo 'Shutting down';
        exit(0);
    }

    /**
     * Register signal handlers that a worker should respond to.
     *
     * TERM: Shutdown immediately and stop processing jobs.
     * INT: Shutdown immediately and stop processing jobs.
     * QUIT: Shutdown after the current job finishes processing.
     * USR1: Kill the forked child immediately and continue processing jobs.
     */
    private function registerSigHandlers()
    {
        if ( ! function_exists('pcntl_signal')) {
            return;
        }

        // @TODO - Do we need all of these?

        pcntl_signal(SIGTERM, array($this, 'shutDownNow'));
        pcntl_signal(SIGINT, array($this, 'shutDownNow'));
        pcntl_signal(SIGQUIT, array($this, 'shutdown'));
        //pcntl_signal(SIGUSR1, array($this, 'killChild'));
        //pcntl_signal(SIGUSR2, array($this, 'pauseProcessing'));
        //pcntl_signal(SIGCONT, array($this, 'unPauseProcessing'));
        //$this->logger->log(Psr\Log\LogLevel::DEBUG, 'Registered signals');
    }

    /**
     * Build the environment specific worker command.
     *
     * @return string
     */
    protected function buildCommandTemplate()
    {
        $command = 'work %s';
        return "{$this->phpBinary()} {$this->binary()} {$command}";
    }

    /**
     * Get the binary.
     *
     * @return string
     */
    protected function binary()
    {
        return defined('TOMAHAWK_QUEUE_BINARY')
            ? ProcessUtils::escapeArgument(TOMAHAWK_QUEUE_BINARY)
            : 'tomahawk-queue';
    }

    /**
     * Get the PHP binary.
     *
     * @return string
     */
    protected function phpBinary()
    {
        return ProcessUtils::escapeArgument(
            (new PhpExecutableFinder)->find(false)
        );
    }
}
