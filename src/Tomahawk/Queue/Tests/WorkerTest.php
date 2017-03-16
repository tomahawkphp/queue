<?php

namespace Tomahawk\Queue\Tests\Storage;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Queue\JobEvents;
use Tomahawk\Queue\Process\PcntlProcess;
use Tomahawk\Queue\Process\ProcessFactory;
use Tomahawk\Queue\Test\TestJob;
use Tomahawk\Queue\Worker;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Tests\AbstractTestCase;

class WorkerTest extends AbstractTestCase
{
    public function testWorkerPaused()
    {
        $storage = $this->createMock(StorageInterface::class);
        $processFactory = $this->createMock(ProcessFactory::class);

        $worker = new Worker(
            $storage,
            $processFactory,
            ['emails']
        );

        $this->assertFalse($worker->isPaused());
        $worker->pause();
        $this->assertTrue($worker->isPaused());

        $worker->unpause();
        $this->assertFalse($worker->isPaused());
        $worker->shutdown();
    }

    public function testWorkerPausedWhileRunning()
    {
        $storage = $this->createMock(StorageInterface::class);
        $processFactory = $this->createMock(ProcessFactory::class);

        $worker = new Worker(
            $storage,
            $processFactory,
            ['emails']
        );

        $this->assertFalse($worker->isPaused());
        $worker->pause();
        $this->assertTrue($worker->isPaused());

        //$worker->work(-1);
    }

    public function testWorkerDoesNothingWhenNoJob()
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('pop')
            ->will($this->returnValue(null))
        ;

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventDispatcher->expects($this->never())
            ->method('dispatch')
        ;

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->expects($this->never())
            ->method('make');

        $worker = new Worker(
            $storage,
            $processFactory,
            ['emails'],
            $eventDispatcher
        );

        $worker->work(-1);
    }

    public function testWorkerWhenJobThrowsException()
    {
        $job = $this->createMock(TestJob::class);
        $job->expects($this->once())
            ->method('process')
            ->will($this->throwException(new \RuntimeException()));
        ;

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('pop')
            ->will($this->returnValue(
                $job
            ))
        ;

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
        ;

        /*$eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                JobEvents::FAILED,
                $this->isInstanceOf(Event::class)
            )
        ;*/

        $process = $this->createMock(PcntlProcess::class);
        $process->expects($this->once())
            ->method('fork')
            ->will($this->returnValue(0));

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->expects($this->once())
            ->method('make')
            ->will($this->returnValue(
                $process
            ));

        $worker = new Worker(
            $storage,
            $processFactory,
            ['emails'],
            $eventDispatcher
        );

        $worker->work(-1);
    }

    public function testWorkerThrowsExceptionAndTriggersFailedEventWhenForked()
    {
        $job = $this->createMock(TestJob::class);
        $job->expects($this->never())
            ->method('process')
        ;

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->atLeastOnce())
            ->method('pop')
            ->will($this->returnValue(
                $job
            ))
        ;

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                JobEvents::FAILED,
                $this->isInstanceOf(Event::class)
            )
        ;

        $process = $this->createMock(PcntlProcess::class);
        $process->expects($this->atLeastOnce())
            ->method('fork')
            ->will($this->returnValue(-1));

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->expects($this->atLeastOnce())
            ->method('make')
            ->will($this->returnValue(
                $process
            ));

        $worker = new Worker(
            $storage,
            $processFactory,
            ['emails'],
            $eventDispatcher
        );

        $worker->work(-1);
        $worker->shutdown();

    }

    public function testWorkerWhenForked()
    {
        $job = $this->createMock(TestJob::class);
        $job->expects($this->never())
            ->method('process');

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->atLeastOnce())
            ->method('pop')
            ->will($this->returnValue(
                $job
            ));

        $process = $this->createMock(PcntlProcess::class);
        $process->expects($this->atLeastOnce())
            ->method('fork')
            ->will($this->returnValue(1));

        $process->expects($this->atLeastOnce())
            ->method('wait')
            ->will($this->returnValue(0));

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->expects($this->atLeastOnce())
            ->method('make')
            ->will($this->returnValue(
                $process
            ));

        $worker = new Worker(
            $storage,
            $processFactory,
            ['emails']
        );

        $worker->work(-1);
        $worker->shutdown();

    }

    public function testWorker()
    {
        $job = $this->createMock(TestJob::class);
        $job->expects($this->atLeastOnce())
            ->method('process');

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->atLeastOnce())
            ->method('pop')
            ->will($this->returnValue(
                $job
            ));

        $process = $this->createMock(PcntlProcess::class);
        $process->expects($this->atLeastOnce())
            ->method('fork')
            ->will($this->returnValue(0));

        $process->expects($this->atLeastOnce())
            ->method('exit');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->expects($this->atLeastOnce())
            ->method('make')
            ->will($this->returnValue(
                $process
            ));

        $worker = new Worker(
            $storage,
            $processFactory,
            ['emails']
        );

        $worker->work(-1);
        $worker->shutdown();
        $this->assertTrue($worker->isShuttingDown());

    }
}
