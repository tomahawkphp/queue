<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessUtils;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Process\ProcessFactory;
use Tomahawk\Queue\Process\ProcessHelper;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Util\FileSystem;

/**
 * Class ListCommand
 *
 * @package Tomahawk\Queue\Console\Command
 */
class ListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('worker:list')
            ->setDescription('List all running workers')
            ->setHelp('help')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $container = $this->getContainer();
        $configuration = $this->getConfiguration();

        /** @var FileSystem $fileSystem */
        $fileSystem = $container[FileSystem::class];

        $directory = $configuration->getStorage() . '/var/run';

        $workerRows = [];

        $symfonyStyle->title('Currently Running Workers');

        $pidFiles = $fileSystem->getPidFiles($directory);

        /** @var ProcessHelper $processHelper */
        $processHelper = $container[ProcessHelper::class];

        foreach ($pidFiles as $pidFile) {

            if ($fileSystem->exists($pidFile)) {

                $key = $fileSystem->pathinfo($pidFile, PATHINFO_FILENAME);

                $worker = $configuration->findWorker($key);

                $pid = $fileSystem->readFile($pidFile);

                if (false === $processHelper->posixGetPGID($pid)) {
                    $fileSystem->unlink($pidFile);
                    continue;
                }

                $workerRows[] = [
                    'name' => isset($worker['name']) ? $worker['name'] : 'Unknown',
                    'pid' => $key,
                    'queues' => isset($worker['queues']) ? $worker['queues'] : 'Unknown',
                ];
            }

        }

        if ( ! $workerRows ){
            $symfonyStyle->note('There are no running workers');
            return 0;
        }

        $table = new Table($output);
        $table->setHeaders([
            'Worker',
            'Pid Key',
            'Queues',
        ]);

        $table->addRows($workerRows);
        $table->render();
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
