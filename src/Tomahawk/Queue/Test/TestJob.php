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
        //die;
        //echo 'Woot2';
        //throw new \Exception('kk');
        file_put_contents(__DIR__ .'/../../../../storage/log/test.log', 'From Test Job 2' . PHP_EOL, FILE_APPEND);
        //exit(0);
    }
}
