<?php

namespace Tomahawk\Queue\Storage;

use Predis\Client;
use Tomahawk\Queue\Job\AbstractJob;
use Tomahawk\Queue\Exception\JobNotFoundException;

/**
 * Class RedisStorage
 *
 * @package Tomahawk\Queue\Storage
 */
class RedisStorage implements StorageInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * RedisStorage constructor.
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client();
    }

    /**
     * Pop a job off the queue if one exists
     *
     * @param $queue
     * @return AbstractJob|null
     */
    public function pop($queue)
    {
        $rawJob = $this->client->lpop('tomahawk:queue:' . $queue);

        if ( ! $rawJob) {
            return null;
        }

        if ($jobData = @json_decode($rawJob, true)) {

            $jobClass = isset($jobData['job']) ? $jobData['job'] : null;
            $arguments = isset($jobData['args']) ? $jobData['args'] : [];

            if ( ! ($jobClass && class_exists($jobClass))) {
                throw new JobNotFoundException();
            }

            $job = new $jobClass($queue, $arguments);

            return $job;
        }


        return null;
    }

    /**
     * Push a job onto the queue
     *
     * @param $queue
     * @param string $jobClass
     * @param array $arguments
     * @return bool
     */
    public function push($queue, $jobClass, array $arguments)
    {
        $data = [
            'job'  => $jobClass,
            'args' => $arguments,
        ];

        $result = $this->client->lpush('tomahawk:queue:' . $queue, json_encode($data));

        return $result ? true : false;
    }
}
