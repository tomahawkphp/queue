<?php

namespace Tomahawk\Queue\Test;

use Tomahawk\Queue\Job\AbstractJob;

/**
 * Class TestJob
 *
 * @package Tomahawk\Queue\Test
 */
class TestJob extends AbstractJob
{
    /**
     * @return mixed
     */
    public function process()
    {
        echo 'From Test Job' . PHP_EOL;
    }
}
