<?php

namespace Tomahawk\Queue\Storage;

use Tomahawk\Queue\Job\AbstractJob;

/**
 * Class RedisStorage
 *
 * @package Tomahawk\Queue\Storage
 */
interface StorageInterface
{
    /**
     * Pop a job off the queue if one exists
     *
     * @param $queue
     * @return AbstractJob|null
     */
    public function pop($queue);

    /**
     * Push a job onto the queue
     *
     * @param $queue
     * @param string $jobClass
     * @param array $arguments
     * @return bool
     */
    public function push($queue, $jobClass, array $arguments);
}
