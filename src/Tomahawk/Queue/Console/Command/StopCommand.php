<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Util\FileSystem;

/**
 * Class StopCommand
 *
 * @package Tomahawk\Queue\Console\Command
 */
class StopCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stop')
            ->addArgument('pidkey', null, InputArgument::REQUIRED, 'Pid key of worker')
            ->setDescription('Stop running worker')
            ->setHelp('help')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $container = $this->getContainer();

        /** @var FileSystem $fileSystem */
        $fileSystem = $container[FileSystem::class];

        $configuration = Application::getConfiguration();
        $storageDirectory = $configuration->getStorage();
        $directory = $storageDirectory . '/var/run/';

        $pidkey = $input->getArgument('pidkey') . '.pid';

        $filePath = $directory . $pidkey;

        if ($fileSystem->exists($filePath)) {

            $pid = $fileSystem->readFile($filePath);

            $symfonyStyle->success('Stopping worker');
            $process = new Process('kill -9 ' . $pid);
            $process->run();

            $fileSystem->unlink($filePath);

            $symfonyStyle->success('Worker stopped');

        }

        exit(0);
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    protected function getConfiguration()
    {
        return Application::getConfiguration();
    }
}
