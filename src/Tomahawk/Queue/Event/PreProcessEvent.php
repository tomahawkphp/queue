<?php

namespace Tomahawk\Queue\Event;

/**
 * Class PreProcessEvent
 *
 * @package Tomahawk\Queue\Event
 */
class PreProcessEvent extends AbstractJobEvent
{
    /**
     * @var bool
     */
    protected $cancelled = false;

    /**
     * Cancel
     */
    public function cancel()
    {
        $this->cancelled = true;
    }

    /**
     * @return bool
     */
    public function isCancelled() : bool
    {
        return $this->cancelled;
    }
}
