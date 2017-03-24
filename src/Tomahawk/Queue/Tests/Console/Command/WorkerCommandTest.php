<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\Command;

use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Console\Application as ConsoleApplication;
use Tomahawk\Queue\Console\Command\WorkerCommand;
use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Process\ProcessHelper;
use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Util\FileSystem;

class WorkerCommandTest extends AbstractTestCase
{
    public function testCommand()
    {
        //$this->setUpApplication();

        $command = new WorkerCommand();

        $commandTester = $this->getCommandTester($command);

        /*$commandTester->execute([
            'command' => $command->getName(),
            'pidkey' => 'emails',
        ]);*/
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
        $processHelper = $this->createMock(ProcessHelper::class);
        $processHelper->expects($this->once())
            ->method('kill')
        ;

        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->expects($this->once())
            ->method('readFile')
            ->willReturn(1)
        ;

        $fileSystem->expects($this->once())
            ->method('exists')
            ->willReturn(true)
        ;

        $fileSystem->expects($this->once())
            ->method('unlink')
        ;

        $configuration = new Configuration([]);
        $container = new Container();
        $container[FileSystem::class] = $fileSystem;
        $container[ProcessHelper::class] = $processHelper;

        Application::setConfiguration($configuration);
        Application::setContainer($container);
    }
}
