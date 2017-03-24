<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\Command;

use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Console\Application as ConsoleApplication;
use Tomahawk\Queue\Console\Command\ListCommand;
use Tomahawk\Queue\Process\ProcessHelper;
use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Util\FileSystem;

class ListCommandTest extends AbstractTestCase
{
    public function testCommand()
    {
        $this->setUpApplication();

        $command = new ListCommand();

        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }

    public function testCommandWithExistingAndNoRunning()
    {
        $this->setUpApplication(true, false);

        $command = new ListCommand();

        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }

    public function testCommandWithNoExisting()
    {
        $this->setUpApplication(false, false);

        $command = new ListCommand();

        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }

    public function testCommandWithNoRunning()
    {
        $this->setUpApplication(true, false);

        $command = new ListCommand();

        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
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

    protected function setUpApplication($existing = true, $running = true)
    {
        $processHelper = $this->createMock(ProcessHelper::class);

        if ($existing && $running) {
            $processHelper->expects($this->once())
                ->method('posixGetPGID')
                ->willReturn(1);
        }
        else if ($existing && ! $running) {
            $processHelper->expects($this->once())
                ->method('posixGetPGID')
                ->willReturn(false);
        }

        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->expects($this->once())
            ->method('getPidFiles')
            ->willReturn([
                'emails.pid'
            ]);

        $fileSystem->expects($this->once())
            ->method('exists')
            ->willReturn($existing);

        if ($existing && ! $running) {
            $fileSystem->expects($this->once())
                ->method('unlink')
            ;
        }

        $configuration = new Configuration([]);
        $container = new Container();
        $container[FileSystem::class] = $fileSystem;
        $container[ProcessHelper::class] = $processHelper;

        Application::setConfiguration($configuration);
        Application::setContainer($container);
    }
}
