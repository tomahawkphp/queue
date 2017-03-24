<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tomahawk\Queue\Manager;

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
            ->addArgument('arguments', InputArgument::OPTIONAL, 'Arguments JSON encoded')
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
        $symfonyStyle = new SymfonyStyle($input, $output);

        $queueName = $input->getArgument('queue');
        $jobClass = $input->getArgument('job_class');
        if ($arguments = $input->getArgument('arguments')) {
            $arguments = json_decode($arguments, true);
        }

        $this->getManager()->queue($queueName, $jobClass, $arguments);

        $symfonyStyle->success(sprintf('Adding job to queue %s', $queueName));
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
