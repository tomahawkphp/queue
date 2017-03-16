<?php

namespace Tomahawk\Queue\Tests\Storage;

use Tomahawk\Queue\Event\ProcessedEvent;
use Tomahawk\Queue\Job\AbstractJob;
use Tomahawk\Queue\Tests\AbstractTestCase;

class ProcessedEventTest extends AbstractTestCase
{
    public function testEvent()
    {
        $job = $this->createMock(AbstractJob::class);

        $event = new ProcessedEvent($job);
        $this->assertSame($job, $event->getJob());
    }
}
