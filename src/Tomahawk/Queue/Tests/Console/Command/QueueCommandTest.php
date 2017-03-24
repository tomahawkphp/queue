<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\Command;

use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Console\Application as ConsoleApplication;
use Tomahawk\Queue\Console\Command\QueueCommand;
use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Util\Configuration;

class QueueCommandTest extends AbstractTestCase
{
    public function testCommand()
    {
        $this->setUpApplication();

        $command = new QueueCommand();

        $commandTester = $this->getCommandTester($command);

        $commandTester->execute([
            'command' => $command->getName(),
            'queue' => 'emails',
            'job_class' => 'Job',
            'arguments' => json_encode(['id' => 1])
        ]);
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @return CommandTester
     */
    protected function getCommandTester(Command $command)
    {
        $application = new ConsoleApplication(__DIR__);
        $application->setAutoExit(false);
        $application->add($command);

        $command = $application->find($command->getName());
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    protected function setUpApplication()
    {
        $manager = $this->createMock(Manager::class);
        $manager->expects($this->once())
            ->method('queue')
        ;

        $configuration = new Configuration([]);
        $container = new Container();
        $container[Manager::class] = $manager;

        Application::setConfiguration($configuration);
        Application::setContainer($container);
    }
}
