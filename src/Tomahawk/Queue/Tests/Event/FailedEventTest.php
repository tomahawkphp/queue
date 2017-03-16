<?php

namespace Tomahawk\Queue\Tests\Storage;

use Tomahawk\Queue\Event\FailedEvent;
use Tomahawk\Queue\Job\AbstractJob;
use Tomahawk\Queue\Tests\AbstractTestCase;

class FailedEventTest extends AbstractTestCase
{
    public function testEvent()
    {
        $exception = new \RuntimeException();
        $job = $this->createMock(AbstractJob::class);

        $event = new FailedEvent($job, $exception);
        $this->assertSame($exception, $event->getException());
        $this->assertSame($job, $event->getJob());
    }
}
