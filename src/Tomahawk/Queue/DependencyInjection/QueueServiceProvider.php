<?php

namespace Tomahawk\Queue\DependencyInjection;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Storage\RedisStorage;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Storage\StorageInterface;

class QueueServiceProvider implements ServiceProviderInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

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
        // Default storage
        $pimple[StorageInterface::class] = function (Container $c) {
            return new RedisStorage();
        };

        $pimple[Manager::class] = function (Container $c) {
            return new Manager($c[StorageInterface::class]);
        };
    }
}
