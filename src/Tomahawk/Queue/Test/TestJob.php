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
        //throw new \Exception('kk');
        //file_put_contents(__DIR__ .'/../../../../storage/log/test.log', 'From Test Job' . PHP_EOL, FILE_APPEND);
        //exit(0);
    }
}
