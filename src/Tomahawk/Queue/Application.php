<?php

namespace Tomahawk\Queue;

use Pimple\Container;
use Tomahawk\Queue\Util\Configuration;

/**
 * Class Application
 *
 * A simple static application to hold the dependency injection container
 *
 * @package Tomahawk\Queue
 */
class Application
{
    /**
     * @var Configuration
     */
    protected static $configuration;

    /**
     * @var Container
     */
    protected static $container;

    /**
     * Set container
     *
     * @param Container $container
     */
    public static function setContainer(Container $container)
    {
        static::$container = $container;
    }

    /**
     * Get container
     *
     * @return Container
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public static function getConfiguration()
    {
        return self::$configuration;
    }

    /**
     * Set configuration
     *
     * @param Configuration $configuration
     */
    public static function setConfiguration($configuration)
    {
        self::$configuration = $configuration;
    }
}
