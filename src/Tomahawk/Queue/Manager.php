<?php

namespace Tomahawk\Queue;

use Tomahawk\Queue\Storage\StorageInterface;

/**
 * Class Manager
 *
 * @package Tomahawk\Queue
 */
class Manager
{
    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Queue up a job
     *
     * @param $queueName
     * @param $jobClass
     * @param array $arguments
     */
    public function queue($queueName, $jobClass, array $arguments)
    {
        $this->storage->push($queueName, $jobClass, $arguments);
    }
}
