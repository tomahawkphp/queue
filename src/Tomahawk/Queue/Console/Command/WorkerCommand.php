<?php

namespace Tomahawk\Queue\Console\Command;

use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Tomahawk\Queue\Storage\RedisStorage;
use Tomahawk\Queue\Worker;

/**
 * Class WorkerCommand
 *
 * @package Tomahawk\Queue\Console\Command
 */
class WorkerCommand extends Command
{
    /**
     * @var bool
     */
    protected $running = true;

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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //$this->registerSigHandlers();

        $client = new Client();
        $symfonyStyle = new SymfonyStyle($input, $output);
        $output->setDecorated(true);

        $redisStorage = new RedisStorage($client);

        $this->queues = explode(',', $input->getArgument('queues'));


        $worker = new Worker($redisStorage, $this->queues);


        $worker->work();



        $symfonyStyle->success('Done processing');

    }

    protected function daemonize()
    {
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

        //pcntl_signal(SIGTERM, array($this, 'shutDownNow'));
        //pcntl_signal(SIGINT, array($this, 'shutDownNow'));
        //pcntl_signal(SIGQUIT, array($this, 'shutdown'));
        //pcntl_signal(SIGUSR1, array($this, 'killChild'));
        //pcntl_signal(SIGUSR2, array($this, 'pauseProcessing'));
        //pcntl_signal(SIGCONT, array($this, 'unPauseProcessing'));
        //$this->logger->log(Psr\Log\LogLevel::DEBUG, 'Registered signals');
    }
}