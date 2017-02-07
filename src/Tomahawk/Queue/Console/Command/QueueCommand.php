<?php

namespace Tomahawk\Queue\Console\Command;

use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Tomahawk\Queue\Test\TestJob;

/**
 * Class QueueCommand
 * 
 * @package Tomahawk\Queue\Console\Command
 */
class QueueCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('queue')
            ->setDescription('description')
            ->setHelp('help')
        ;
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        //$this->get
        $data = [
            'job' => TestJob::class,
        ];

        $client = new Client();
        $client->rpush('tomahawk:queue:queuename', json_encode($data));

        $output->setDecorated(true);

        $symfonyStyle = new SymfonyStyle($input, $output);

        $symfonyStyle->success('Hello');

        //$process = new Process();
        //$process->getPid();
    }
}