<?php

namespace Tomahawk\Queue\Console\Command;

use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Test\TestJob;

/**
 * Class QueueCommand
 * 
 * @package Tomahawk\Queue\Console\Command
 */
class QueueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('queue')
            ->setDescription('description')
            ->addArgument('queue', InputArgument::REQUIRED, 'Name of queue')
            ->addArgument('job_class', InputArgument::REQUIRED, 'Job class')
            ->addArgument('arguments', InputArgument::OPTIONAL, 'Arguments', [])
            ->setHelp('help')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $queueName = $input->getArgument('queue');
        $jobClass = $input->getArgument('job_class');
        //$arguments = $input->getArgument('arguments');
        $arguments = [];

        $this->getManager()->queue($queueName, $jobClass, $arguments);

        //$this->get
        /*$data = [
            'job' => TestJob::class,
        ];

        $client = new Client();
        $client->rpush('tomahawk:queue:queuename', json_encode($data));

        $output->setDecorated(true);

        $symfonyStyle = new SymfonyStyle($input, $output);

        $symfonyStyle->success('Hello');*/

        //$process = new Process();
        //$process->getPid();
    }

    /**
     * Get queue manager
     *
     * @return Manager
     */
    protected function getManager()
    {
        return $this->getContainer()[Manager::class];
    }
}