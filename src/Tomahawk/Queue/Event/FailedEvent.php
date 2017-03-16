<?php

namespace Tomahawk\Queue\Event;

use Symfony\Component\EventDispatcher\Event;
use Tomahawk\Queue\Job\AbstractJob;

/**
 * Class FailedEvent
 *
 * @package Tomahawk\Queue\Event
 */
class FailedEvent extends AbstractJobEvent
{
    /**
     * @var \Exception
     */
    protected $exception;

    public function __construct(AbstractJob $job, \Exception $exception = null)
    {
        $this->exception = $exception;
        parent::__construct($job);
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }
}
