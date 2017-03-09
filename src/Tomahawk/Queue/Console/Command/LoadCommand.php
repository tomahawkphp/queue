<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessUtils;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Util\Configuration;

/**
 * Class LoadCommand
 *
 * @package Tomahawk\Queue\Console\Command
 */
class LoadCommand extends ContainerAwareCommand
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
            ->setName('load')
            ->setDescription('Load and start all workers defined in configuration file (tomahawk.xml) as daemons')
            ->setHelp('help')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        // Load all workers
        $workers = $this->getConfiguration()->getWorkers();

        $workerRows = [];

        $symfonyStyle->title('Loading Workers');

        foreach ($workers as $worker) {
            $command = $this->buildCommandTemplate();
            $command = sprintf($command, $worker['queues']);

            exec($command, $out, $return);

            $workerRows[] = [
                'name'   => $worker['name'],
                'status' => 0 === $return ? 'Started' : 'Failed',
            ];
            break;
        }

        $table = new Table($output);
        $table->setHeaders([
            'Worker',
            'Status'
        ]);

        $table->addRows($workerRows);
        $table->render();

        exit(0);
    }

    /**
     * Build the environment specific worker command.
     *
     * @return string
     */
    protected function buildCommandTemplate()
    {
        $command = 'work %s';
        return "{$this->phpBinary()} {$this->binary()} {$command} > /dev/null &";
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