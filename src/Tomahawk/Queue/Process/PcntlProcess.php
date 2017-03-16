<?php

namespace Tomahawk\Queue\Process;

/**
 * Class PcntlProcess
 *
 * @package Tomahawk\Queue\Process
 */
class PcntlProcess
{
    /**
     * @var int
     */
    private $processId;

    /**
     * Get process id
     *
     * @return int
     */
    public function getProcessId() : int
    {
        return $this->processId;
    }

    /**
     * Wait for process
     *
     * @param string $status
     * @return int
     */
    public function wait(string $status) : int
    {
        pcntl_wait($status);
        $exitStatus = pcntl_wexitstatus($status);

        return $exitStatus;
    }

    /**
     * Fork process
     *
     * @return int
     */
    public function fork()
    {
        $this->processId = pcntl_fork();

        return $this->processId;
    }
}
