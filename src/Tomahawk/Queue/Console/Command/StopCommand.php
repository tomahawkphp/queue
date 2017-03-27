<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Process\ProcessHelper;
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

        /** @var ProcessHelper $processHelper */
        $processHelper = $container[ProcessHelper::class];

        $configuration = Application::getConfiguration();
        $storageDirectory = $configuration->getStorage();
        $directory = $storageDirectory . '/var/run/';

        $pidkey = $input->getArgument('pidkey') . '.pid';

        $filePath = $directory . $pidkey;

        if ($fileSystem->exists($filePath)) {

            $pid = $fileSystem->readFile($filePath);

            $symfonyStyle->writeln('Stopping worker');

            $processHelper->kill($pid);

            $fileSystem->unlink($filePath);

            $symfonyStyle->success('Worker stopped');
        }

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
