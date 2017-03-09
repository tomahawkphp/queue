<?php

namespace Tomahawk\Queue\Console;

use Pimple\Container;
use Tomahawk\Queue\Application as MainApplication;
use Symfony\Component\Console\Application as BaseApplication;
use Tomahawk\Queue\DependencyInjection\QueueServiceProvider;
use Tomahawk\Queue\Util\ConfigurationLoader;

/**
 * Class Application
 *
 * @package Tomahawk\Queue\Console
 */
class Application extends BaseApplication
{
    const NAME = 'Tomahawk Queue';

    const VERSION = '0.1.0';

    /**
     * @var string
     */
    private $directory;

    public function __construct($directory)
    {
        parent::__construct(self::NAME, self::VERSION);
        $this->directory = $directory;
    }

    public function configure()
    {
        // Load configuration - tomahawk.xml
        $configurationLoader = new ConfigurationLoader($this->directory);
        $configuration = $configurationLoader->load();

        // Set container on application
        MainApplication::setContainer(new Container());
        MainApplication::setConfiguration($configuration);

        // Register service providers
        MainApplication::getContainer()
            ->register(new QueueServiceProvider($configuration))
        ;
    }
}
