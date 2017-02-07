<?php

namespace Tomahawk\Queue\Job;

/**
 * Class AbstractJob
 *
 * @package Tomahawk\Queue
 */
abstract class AbstractJob
{
    /**
     * Job arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Name of queue this job processed
     *
     * @var string
     */
    protected $queueName;

    public function __construct($queueName, array $arguments = [])
    {
        $this->arguments = $arguments;
        $this->queueName = $queueName;
    }

    /**
     * @return mixed
     */
    abstract public function process();

    /**
     * Get queue name
     *
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }
}
