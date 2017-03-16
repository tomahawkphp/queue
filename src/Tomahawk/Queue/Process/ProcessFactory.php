<?php

namespace Tomahawk\Queue\Process;

/**
 * Class ProcessFactory
 *
 * @package Tomahawk\Queue\Process
 */
class ProcessFactory
{
    /**
     * Make a new PcntlProcess
     *
     * @return PcntlProcess
     */
    public function make()
    {
        return new PcntlProcess();
    }
}
