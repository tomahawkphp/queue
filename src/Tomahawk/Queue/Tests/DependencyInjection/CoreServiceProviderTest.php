<?php

namespace Tomahawk\Queue\Tests\DependencyInjection;

use Pimple\Container;
use Psr\Log\LoggerInterface;
use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Process\ProcessFactory;
use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\DependencyInjection\CoreServiceProvider;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Util\FileSystem;

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

        $serviceProvider = new CoreServiceProvider($configuration);
        $serviceProvider->register($container);

        $this->assertTrue(isset($container[StorageInterface::class]));
        $this->assertTrue(isset($container[LoggerInterface::class]));
        $this->assertTrue(isset($container[Manager::class]));
        $this->assertTrue(isset($container[FileSystem::class]));
        $this->assertTrue(isset($container[ProcessFactory::class]));
    }
}
