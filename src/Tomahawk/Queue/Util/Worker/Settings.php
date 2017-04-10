<?php

namespace Tomahawk\Queue\Util\Worker;

/**
 * Class Settings
 *
 * @package Tomahawk\Queue\Util\Worker
 */
class Settings
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $pidKey;

    /**
     * @var array
     */
    protected $queues;

    /**
     * @var bool
     */
    protected $autoload;

    public function __construct(string $name, string $pidKey, array $queues, bool $autoload = true)
    {
        $this->name = $name;
        $this->pidKey = $pidKey;
        $this->queues = $queues;
        $this->autoload = $autoload;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPidKey(): string
    {
        return $this->pidKey;
    }

    /**
     * @return array
     */
    public function getQueues(): array
    {
        return $this->queues;
    }

    /**
     * @return boolean
     */
    public function isAutoload(): bool
    {
        return $this->autoload;
    }
}
