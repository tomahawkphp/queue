<?php

namespace Tomahawk\Queue\Tests\Storage;

use Tomahawk\Queue\Event\PreProcessEvent;
use Tomahawk\Queue\Job\AbstractJob;
use Tomahawk\Queue\Tests\AbstractTestCase;

class PreProcessEventTest extends AbstractTestCase
{
    public function testEvent()
    {
        $job = $this->createMock(AbstractJob::class);

        $event = new PreProcessEvent($job);
        $this->assertFalse($event->isCancelled());
        $event->cancel();
        $this->assertTrue($event->isCancelled());
        $this->assertSame($job, $event->getJob());
    }
}
