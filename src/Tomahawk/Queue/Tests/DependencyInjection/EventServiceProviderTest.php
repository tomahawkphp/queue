<?php

namespace Tomahawk\Queue\Tests\DependencyInjection;

use Pimple\Container;
use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\DependencyInjection\EventServiceProvider;
use Tomahawk\Queue\Util\Configuration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventServiceProviderTest
 *
 * @package Tomahawk\Queue\Tests\DependencyInjection
 */
class EventServiceProviderTest extends AbstractTestCase
{
    public function testServiceProvider()
    {
        $container = new Container();

        $serviceProvider = new EventServiceProvider();
        $serviceProvider->register($container);

        $this->assertTrue(isset($container[EventDispatcherInterface::class]));
    }
}
