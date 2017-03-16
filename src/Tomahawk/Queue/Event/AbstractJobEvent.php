<?php

namespace Tomahawk\Queue\Event;

use Symfony\Component\EventDispatcher\Event;
use Tomahawk\Queue\Job\AbstractJob;

/**
 * Class AbstractJobEvent
 *
 * @package Tomahawk\Queue\Event
 */
abstract class AbstractJobEvent extends Event
{
    /**
     * @var AbstractJob
     */
    protected $job;

    public function __construct(AbstractJob $job)
    {
        $this->job = $job;
    }

    /**
     * @return AbstractJob
     */
    public function getJob() : AbstractJob
    {
        return $this->job;
    }
}
