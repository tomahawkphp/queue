<?php

declare(ticks=1);

namespace Tomahawk\Queue;

use RuntimeException;
use Tomahawk\Queue\Job\AbstractJob;
use Tomahawk\Queue\Storage\StorageInterface;

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
     * @var string
     */
    protected $hostname;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var bool
     */
    protected $shutdown = false;

    /**
     * @var bool
     */
    protected $running = true;

    public function __construct(StorageInterface $storage, array $queues)
    {
        $this->storage = $storage;
        $this->queues = $queues;
        $this->hostname = php_uname('n');
        $this->id = $this->hostname . ':'.getmypid() . ':' . implode(',', $this->queues);
    }

    /**
     * @param int $interval
     */
    public function work($interval = 5000)
    {
        while ($this->running) {

            $job = $this->getNextJob();

            if ($job) {

                try {

                    $pid = pcntl_fork();

                    if (-1 === $pid) {
                        throw new \RuntimeException('Could not fork process');
                    }

                    if (0 === $pid || false === $pid) {
                        $job->process();
                        if (0 === $pid) {
                            //file_put_contents(__DIR__ .'/../../../log/test.log', 'Exiting...' . PHP_EOL, FILE_APPEND);
                            exit(0);
                        }
                    }
                    else if($pid > 0) {
                        $status = 'Forked ' . $pid . ' at ' . strftime('%F %T');
                        //file_put_contents(__DIR__ .'/../../../log/test.log', $status . PHP_EOL, FILE_APPEND);
                        pcntl_wait($status);
                        $exitStatus = pcntl_wexitstatus($status);
                        if (0 !== $exitStatus) {
                            //Job exited with exit code
                        }
                    }

                    $pid = null;
                }
                catch (\Exception $e) {

                }
            }


        }
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

}
