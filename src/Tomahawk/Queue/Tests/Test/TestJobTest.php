<?php

namespace Tomahawk\Queue\Tests\Test;

use Tomahawk\Queue\Test\TestJob;
use Tomahawk\Queue\Tests\AbstractTestCase;

class TestJobTest extends AbstractTestCase
{
    public function testJob()
    {
        $job = new TestJob('Emails');

        $job->process();

        $this->assertEquals('Emails', $job->getQueueName());
    }
}
