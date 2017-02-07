<?php

namespace Tomahawk\Queue\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Tomahawk\Queue\Util\Configuration;

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
        $configuration = new Configuration($this->directory);
        $configuration->load();
    }
}
