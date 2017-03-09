<?php

namespace Tomahawk\Queue\Console\Command;

use Symfony\Component\Console\Command\Command;
use Tomahawk\Queue\Application;

/**
 * Class ContainerAwareCommand
 *
 * @package Tomahawk\Queue\Console\Command
 */
abstract class ContainerAwareCommand extends Command
{
    /**
     * @return \Pimple\Container
     */
    protected function getContainer()
    {
        return Application::getContainer();
    }
}
