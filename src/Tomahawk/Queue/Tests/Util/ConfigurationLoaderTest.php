<?php

namespace Tomahawk\Queue\Tests\Util;

use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Util\Configuration;
use Tomahawk\Queue\Util\ConfigurationLoader;

/**
 * Class ConfigurationLoaderTest
 *
 * @package Tomahawk\Queue\Tests\Util
 */
class ConfigurationLoaderTest extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $directory;

    protected function setUp()
    {
        $this->directory = __DIR__ . '/../../Resources';
    }

    public function testLoader()
    {
        $loader = new ConfigurationLoader($this->directory);
        $configuration = $loader->load();

        $this->assertInstanceOf(Configuration::class, $configuration);

        $this->assertNotNull($configuration->getBootstrap());
        $this->assertCount(2, $configuration->getWorkers());

        foreach ($configuration->getWorkers() as $worker) {
            $this->assertArrayHasKey('name', $worker);
            $this->assertArrayHasKey('number', $worker);
            $this->assertArrayHasKey('queues', $worker);
        }
    }
}
