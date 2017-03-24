<?php

namespace Tomahawk\Queue\Process;

/**
 * Class ProcessHelper
 *
 * @package Tomahawk\Queue\Process
 */
class ProcessHelper
{
    /**
     * Exec
     *
     * @param $command
     * @param array $out
     * @param $return
     * @return string
     */
    public function exec($command, &$out, &$return)
    {
        return exec($command, $out, $return);
    }

    /**
     * Get process group id
     *
     * @param $pid
     * @return int|false
     */
    public function posixGetPGID($pid)
    {
        return posix_getpgid($pid);
    }
}
