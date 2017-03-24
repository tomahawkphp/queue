<?php

namespace Tomahawk\Bundle\FrameworkBundle\Tests\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Queue\Console\Application;
use Tomahawk\Queue\Console\Command\LoadCommand;
use Tomahawk\Queue\Tests\AbstractTestCase;

class LoadCommandTest extends AbstractTestCase
{
    public function testCommand()
    {
        $command = new LoadCommand();

        //$commandTester = $this->getCommandTester($command, $routeCollection);

        //$commandTester->execute(array('command' => $command->getName()));
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @param $routeCollection
     * @return CommandTester
     */
    protected function getCommandTester(Command $command, $routeCollection)
    {
        /*$app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);

        $container = $application->getKernel()->getContainer();

        $container->set('route_collection', $routeCollection);

        $application->add($command);

        $command = $application->find('routing:view');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        return $commandTester;*/
    }
}
