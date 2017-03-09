<?php

namespace Tomahawk\Queue\DependencyInjection;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        if (class_exists('Symfony\Component\EventDispatcher\EventDispatcherInterface')) {

            $pimple[EventDispatcherInterface::class] = function() {
                $eventDispatcher = new EventDispatcher();

                return $eventDispatcher;
            };
        }
    }
}
