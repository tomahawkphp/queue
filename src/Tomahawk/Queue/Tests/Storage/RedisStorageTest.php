<?php

namespace Tomahawk\Queue\Tests\Storage;

use Predis\Client;
use Tomahawk\Queue\Storage\RedisStorage;
use Tomahawk\Queue\Test\TestJob;
use Tomahawk\Queue\Tests\AbstractTestCase;

class RedisStorageTest extends AbstractTestCase
{
    public function testPopReturnsNull()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['lpop'])
            ->getMock();

        $client->expects($this->once())
            ->method('lpop')
            ->will($this->returnValue(null))
        ;

        $storage = new RedisStorage($client);
        $this->assertNull($storage->pop('emails'));
    }

    public function testPopReturnsNullOnInvalid()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['lpop'])
            ->getMock();

        $client->expects($this->once())
            ->method('lpop')
            ->will($this->returnValue('invalid'))
        ;

        $storage = new RedisStorage($client);
        $this->assertNull($storage->pop('emails'));
    }

    /**
     * @expectedException \Tomahawk\Queue\Exception\JobNotFoundException
     */
    public function testPopRetursAJobAndThrowsException()
    {
        $jobData = [
            'job' => 'Job',
        ];

        $job = json_encode($jobData);
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['lpop'])
            ->getMock();

        $client->expects($this->once())
            ->method('lpop')
            ->will($this->returnValue($job));

        $storage = new RedisStorage($client);
        $storage->pop('emails');
    }

    public function testPopReturnsAJob()
    {
        $jobData = [
            'job' => TestJob::class,
        ];

        $job = json_encode($jobData);
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['lpop'])
            ->getMock();

        $client->expects($this->once())
            ->method('lpop')
            ->will($this->returnValue($job));

        $storage = new RedisStorage($client);
        $this->assertInstanceOf(TestJob::class, $storage->pop('emails'));
    }

    public function testPush()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['lpush'])
            ->getMock();

        $client->expects($this->once())
            ->method('lpush')
            ->will($this->returnValue(true));
        ;

        $storage = new RedisStorage($client);
        $this->assertTrue($storage->push('emails', TestJob::class));
    }
}
