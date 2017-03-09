<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Worker;

/**
 * Class ListenCommand
 *
 * @package Tomahawk\Queue\Console\Command
 */
class ListenCommand extends ContainerAwareCommand
{
    /**
     * @var bool
     */
    protected $running = true;

    /**
     * @var array
     */
    protected $queues = [];

    /**
     * @var string
     */
    protected $command = '';

    protected function configure()
    {
        $this
            ->setName('listen')
            ->setDescription('description')
            ->addArgument('queues', null, InputArgument::REQUIRED, 'Name of queues comma separated')
            ->setHelp('help')
        ;
    }

    /**
     * @param InputInterface $  input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->buildCommandTemplate();
        $command = sprintf($command, $input->getArgument('queues'));

        $process = new Process($command);

        $process->start(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $process->getPid();

        //$process->

        file_put_contents(getcwd() . '/tomahawk.pid', $process->getPid());
        return 0;
        /*$process->start(function ($type, $line) use ($output) {
            $output->write($line);
        });*/

        //exit(0);
    }

    protected function daemonize()
    {
        // @TODO - Get this working
        //$process = new Process();
        //$process->getPid();
    }

    private function shutDownNow()
    {
        echo 'Shutting down';
    }

    private function shutdown()
    {
        echo 'Shutting down';
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

        // @TODO - We don't need all of these

        //pcntl_signal(SIGTERM, array($this, 'shutDownNow'));
        //pcntl_signal(SIGINT, array($this, 'shutDownNow'));
        //pcntl_signal(SIGQUIT, array($this, 'shutdown'));
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
