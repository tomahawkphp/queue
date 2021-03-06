<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Process\ProcessFactory;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Util\FileSystem;
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
            ->setName('worker:work')
            ->setDescription('Start worker for given queues')
            ->addOption('daemon', null, InputOption::VALUE_NONE, 'Run worker as a daemon')
            ->addOption('memory-limit', null, InputOption::VALUE_OPTIONAL, 'Memory limit', 128)
            ->addOption('interval', null, InputOption::VALUE_OPTIONAL, 'Interval in milliseconds', 5000)
            ->addArgument('queues', null, InputArgument::REQUIRED, 'Name of queues comma separated')
            ->addArgument('pidfile', null, InputArgument::REQUIRED, 'pidfile')
            ->setHelp('help')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = Application::getContainer();
        $storageDirectory = Application::getConfiguration()->getStorage();
        $symfonyStyle = new SymfonyStyle($input, $output);
        $output->setDecorated(true);
        $asDaemon = false;
        $interval = $input->getOption('interval');
        $memoryLimit = $input->getOption('memory-limit');

        if ($input->getOption('daemon')) {

            $asDaemon = true;

            /** @var ProcessFactory $processFactory */
            $processFactory = $container[ProcessFactory::class];
            $process = $processFactory->make();
            // Ideally we want to be able to run multiple workers for other queues
            // for forking and creating the pid file needs some work
            $process->fork();
            $pid = $process->getProcessId();

            /** @var FileSystem $fileSystem */
            $fileSystem = $container[FileSystem::class];

            $folder = $storageDirectory . '/var/run';

            if ( ! $fileSystem->exists($folder)) {
                $fileSystem->mkdir($folder, 0755, true);
            }

            $pidFile = $folder . '/'. $input->getArgument('pidfile') . '.pid';

            if (-1 === $pid) {
                syslog(1, 'Unable to start worker as a daemon');
                $output->writeln('Unable to start worker as a daemon');
                return 0;
            }
            else if ($pid) {
                $fileSystem->writeFile($pidFile, $pid);
                $symfonyStyle->success('Worker started as a daemon');
                exit(0);
            }
        }

        $container = $this->getContainer();

        $this->queues = explode(',', $input->getArgument('queues'));

        $storage = $container[StorageInterface::class];
        $processFactory = $container[ProcessFactory::class];
        $eventDispatcher = null;
        if (interface_exists('Symfony\Component\EventDispatcher\EventDispatcherInterface')) {
            $eventDispatcher = $container[EventDispatcherInterface::class];
        }

        $this->worker = new Worker($storage, $processFactory, $this->queues, $eventDispatcher);
        $this->worker->work($interval, $memoryLimit, $asDaemon);

        if ( ! $input->getOption('daemon')) {
            $symfonyStyle->success('Worker started');
        }

        return 0;
    }
}
