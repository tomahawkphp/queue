<?php

declare(ticks=1);

namespace Tomahawk\Queue;

use Symfony\Component\EventDispatcher\Event;
use Tomahawk\Queue\Event\FailedEvent;
use Tomahawk\Queue\Event\PreProcessEvent;
use Tomahawk\Queue\Event\ProcessedEvent;
use Tomahawk\Queue\Exception\MemoryExceededException;
use Tomahawk\Queue\Job\AbstractJob;
use Tomahawk\Queue\Process\ProcessFactory;
use Tomahawk\Queue\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Worker
 *
 * @package Tomahawk\Queue
 */
class Worker
{
    /**
     * @var int
     */
    protected $child;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $queues;

    /**
     * @var bool
     */
    protected $shutdown = false;

    /**
     * @var bool
     */
    protected $paused = false;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    public function __construct(
        StorageInterface $storage,
        ProcessFactory $processFactory,
        array $queues,
        EventDispatcherInterface $eventDispatcher = null
    )
    {
        $this->storage = $storage;
        $this->processFactory = $processFactory;
        $this->queues = $queues;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $interval
     * @param int $memoryLimit
     * @param bool $asDaemon
     */
    public function work($interval = 5000, $memoryLimit = 128, $asDaemon = false)
    {
        //@codeCoverageIgnoreStart
        if ($asDaemon) {
            $this->registerSigHandlers();
        }
        //@codeCoverageIgnoreEnd

        while (true) {

            if ($this->shutdown) {
                break;
            }

            //@codeCoverageIgnoreStart
            if ($this->paused) {
                continue;
            }
            //@codeCoverageIgnoreEnd

            // @TODO - Check if memory limit is reached
            if ($this->hasMemoryExceeded($memoryLimit)) {
                throw new MemoryExceededException();
            }

            $job = $this->getNextJob();

            if ($job) {

                try {

                    $process = $this->processFactory->make();
                    $pid = $process->fork();

                    if (-1 === $pid) {
                        throw new \RuntimeException('Could not fork process');
                    }

                    if (0 === $pid) {

                        $preProcessEvent = new PreProcessEvent($job);
                        $this->fireEvent(JobEvents::PRE_PROCESS, $preProcessEvent);

                        try {

                            if (!$preProcessEvent->isCancelled()) {
                                $job->process();

                                $processsedEvent = new ProcessedEvent($job);
                                $this->fireEvent(JobEvents::PROCESSED, $processsedEvent);
                            }

                        }
                        catch (\Exception $e) {
                            $failedEvent = new FailedEvent($job, $e);
                            $this->fireEvent(JobEvents::FAILED, $failedEvent);
                        }

                        $process->exit(0);
                    }
                    else if ($pid > 0) {
                        $status = 'Forked ' . $pid . ' at ' . strftime('%F %T');
                        $exitStatus = $process->wait($status);
                        // @TODO - what to do here if 0 !== $exitStatus??
                    }

                    $pid = null;
                }
                catch (\Exception $e) {
                    $failedEvent = new FailedEvent($job, $e);
                    $this->fireEvent(JobEvents::FAILED, $failedEvent);
                }
            }

            //@codeCoverageIgnoreStart
            if ($interval >= 0) {
                usleep($interval);
            }
            //@codeCoverageIgnoreEnd

            if (-1 === $interval) {
                $this->shutdown();
            }

        }
    }

    /**
     * Shutdown worker
     *
     * @return $this
     */
    public function shutdown()
    {
        $this->shutdown = true;

        return $this;
    }

    /**
     * Is worker shutting down
     *
     * @return bool
     */
    public function isShuttingDown(): bool
    {
        return true === $this->shutdown;
    }

    /**
     * Pause worker
     *
     * @return $this
     */
    public function pause()
    {
        $this->paused = true;

        return $this;
    }

    /**
     * Unpause worker
     *
     * @return $this
     */
    public function unpause()
    {
        $this->paused = false;

        return $this;
    }

    /**
     * Is worker paused
     *
     * @return bool
     */
    public function isPaused(): bool
    {
        return true === $this->paused;
    }

    /**
     * Get next job
     *
     * @return AbstractJob|null
     */
    protected function getNextJob()
    {
        foreach ($this->queues as $queue) {

            if ($job = $this->storage->pop($queue)) {

                return $job;
            }
        }

        return null;
    }

    /**
     * Fire a given event if event dispatcher is present
     *
     * @param $eventName
     * @param Event $event
     * @return Event|null
     */
    protected function fireEvent($eventName, Event $event)
    {
        if ($this->eventDispatcher) {
            return $this->eventDispatcher->dispatch($eventName, $event);
        }
    }

    /**
     * @return bool
     */
    protected function stopIfNeeded()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function shouldRun()
    {
        if ($this->paused) {
            return false;
        }

        return true;
    }

    /**
     * @param $memoryLimit
     * @return bool
     */
    protected function hasMemoryExceeded($memoryLimit)
    {
        return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
    }

    public function shutDownNow()
    {
        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }

        //echo 'Shutting down NOW' . PHP_EOL;
        exit(0);
    }

    public function killChild()
    {
        echo 'kill' . PHP_EOL;
        exit(0);
    }

    public function shutdownWorker()
    {
        echo 'Shutting down';
        exit(0);
    }

    public function pauseProcessing()
    {
        $this->pause();
        //echo 'Paused';
    }

    public function unPauseProcessing()
    {
        $this->unpause();
        //echo 'Un paused';
    }

    /**
     * Register signal handlers that a worker should respond to.
     *
     * TERM: Shutdown immediately and stop processing jobs.
     * INT: Shutdown immediately and stop processing jobs.
     * QUIT: Shutdown after the current job finishes processing.
     * USR1: Kill the forked child immediately and continue processing jobs.
     */
    private function registerSigHandlers()
    {
        if ( ! function_exists('pcntl_signal')) {
            return;
        }

        // @TODO - Do we need all of these?

        pcntl_signal(SIGTERM, [$this, 'shutDownNow']);
        pcntl_signal(SIGUSR2, [$this, 'pauseProcessing']);
        pcntl_signal(SIGCONT, [$this, 'unPauseProcessing']);

        //pcntl_signal(SIGINT, array($this, 'shutDownNow'));
        //pcntl_signal(SIGQUIT, array($this, 'shutdown'));
        //pcntl_signal(SIGUSR1, array($this, 'killChild'));

        //$this->logger->log(Psr\Log\LogLevel::DEBUG, 'Registered signals');
    }

}
