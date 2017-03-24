<?php

namespace Tomahawk\Queue\DependencyInjection;

use Monolog\Handler\StreamHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Process\ProcessFactory;
use Tomahawk\Queue\Process\ProcessHelper;
use Tomahawk\Queue\Storage\RedisStorage;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Util\FileSystem;

class CoreServiceProvider implements ServiceProviderInterface
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
        $configuration = $this->configuration;

        // Default storage
        $pimple[StorageInterface::class] = function (Container $c) {
            return new RedisStorage();
        };

        $pimple[Manager::class] = function (Container $c) {
            return new Manager($c[StorageInterface::class]);
        };

        $pimple[FileSystem::class] = function (Container $c) {
            return new FileSystem();
        };

        $pimple[ProcessFactory::class] = function (Container $c) {
            return new ProcessFactory();
        };

        $pimple[ProcessHelper::class] = function (Container $c) {
            return new ProcessHelper();
        };

        // Logger
        $pimple[LoggerInterface::class] = function (Container $c) use ($configuration) {
            $logger = new Logger('queue');
            $logPath = $configuration->getStorage() . '/log';
            if ( ! file_exists($logPath)) {
                @mkdir($logPath, 0775, true);
            }
            $streamHandler = new StreamHandler($logPath);
            $logger->pushHandler($streamHandler);
            return $logger;
        };
    }
}
