<?php

namespace Tomahawk\Queue\Tests\Console;

use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Console\Application;

class ApplicationTest extends AbstractTestCase
{
    public function testApplication()
    {
        $directory = __DIR__ . '/../../Resources';
        $application = new Application($directory);
        $application->configure();
    }
}
