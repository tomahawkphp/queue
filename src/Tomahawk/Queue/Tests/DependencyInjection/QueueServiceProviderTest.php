<?php

namespace Tomahawk\Queue\Tests\DependencyInjection;

use Pimple\Container;
use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\DependencyInjection\QueueServiceProvider;
use Tomahawk\Queue\Util\Configuration;

/**
 * Class QueueServiceProviderTest
 *
 * @package Tomahawk\Queue\Tests\DependencyInjection
 */
class QueueServiceProviderTest extends AbstractTestCase
{
    public function testServiceProvider()
    {
        $container = new Container();
        $configuration = $this->createMock(Configuration::class);

        $serviceProvider = new QueueServiceProvider($configuration);
        $serviceProvider->register($container);

        $this->assertTrue(isset($container[StorageInterface::class]));
        $this->assertTrue(isset($container[Manager::class]));
    }
}
