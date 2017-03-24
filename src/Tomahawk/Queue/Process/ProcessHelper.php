<?php

namespace Tomahawk\Queue\Process;
use Symfony\Component\Process\Process;

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

    /**
     * @param $pid
     * @return int
     */
    public function kill($pid)
    {
        $process = new Process('kill -9 ' . $pid);
        return $process->run();
    }
}
