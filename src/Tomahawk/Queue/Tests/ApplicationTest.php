<?php

namespace Tomahawk\Queue\Tests\Storage;

use Tomahawk\Queue\Application;
use Tomahawk\Queue\Util\Configuration;
use Pimple\Container;
use Tomahawk\Queue\Tests\AbstractTestCase;

class ApplicationTest extends AbstractTestCase
{
    public function testApplication()
    {
        $configuration = new Configuration([]);
        $container = new Container();

        Application::setConfiguration($configuration);
        Application::setContainer($container);

        $this->assertSame($configuration, Application::getConfiguration());
        $this->assertSame($container, Application::getContainer());

    }
}
