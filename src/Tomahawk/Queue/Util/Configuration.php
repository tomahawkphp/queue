<?php

namespace Tomahawk\Queue\Util;

/**
 * Class Configuration
 *
 * @package Tomahawk\Queue\Util
 */
class Configuration
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string|null
     */
    public function getBootstrap()
    {
        return isset($this->config['bootstrap']) ? $this->config['bootstrap'] : null;
    }

    /**
     * @return string|null
     */
    public function getStorage()
    {
        return isset($this->config['storage']) ? $this->config['storage'] : null;
    }

    /**
     * @return array
     */
    public function getWorkers()
    {
        return isset($this->config['workers']) ? $this->config['workers'] : [];
    }
}
