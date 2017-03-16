<?php

namespace Tomahawk\Queue\Tests\Storage;

use Tomahawk\Queue\Manager;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Tests\AbstractTestCase;

class ManagerTest extends AbstractTestCase
{
    public function testQueue()
    {
        $storage = $this->getMockBuilder(StorageInterface::class)
            ->setMethods(['push', 'pop'])
            ->getMock()
        ;

        $storage->expects($this->once())
            ->method('push');

        $manager = new Manager($storage);
        $manager->queue('emails', 'Job', []);

    }
}
