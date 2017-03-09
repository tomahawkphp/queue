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
        //file_put_contents(__DIR__ .'/../../../../log/test.log', 'From Test Job' . PHP_EOL, FILE_APPEND);
        //exit(0);
    }
}
