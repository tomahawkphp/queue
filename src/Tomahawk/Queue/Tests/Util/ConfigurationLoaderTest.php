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
        $this->assertEquals('./storage', $configuration->getStorage());
        $this->assertCount(3, $configuration->getWorkers());

        foreach ($configuration->getWorkers() as $worker) {
            $this->assertArrayHasKey('pidkey', $worker);
            $this->assertArrayHasKey('name', $worker);
            $this->assertArrayHasKey('queues', $worker);
        }
    }

    public function testLoaderWithNoConfiguration()
    {
        $loader = new ConfigurationLoader(__DIR__);
        $configuration = $loader->load();

        $this->assertInstanceOf(Configuration::class, $configuration);

        $this->assertNull($configuration->getBootstrap());
        $this->assertEquals(__DIR__, $configuration->getStorage());
        $this->assertCount(0, $configuration->getWorkers());

    }
}
