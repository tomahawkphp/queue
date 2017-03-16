<?php

namespace Tomahawk\Queue\Tests\DependencyInjection;

use Tomahawk\Queue\Process\PcntlProcess;
use Tomahawk\Queue\Process\ProcessFactory;
use Tomahawk\Queue\Tests\AbstractTestCase;

/**
 * Class ProcessFactoryTest
 *
 * @package Tomahawk\Queue\Tests\DependencyInjection
 */
class ProcessFactoryTest extends AbstractTestCase
{
    public function testFactory()
    {
        $factory = new ProcessFactory();
        $this->assertInstanceOf(PcntlProcess::class, $factory->make());
    }
}
