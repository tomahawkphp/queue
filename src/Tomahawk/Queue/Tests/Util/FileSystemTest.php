<?php

namespace Tomahawk\Queue\Tests\Util;

use Tomahawk\Queue\Tests\AbstractTestCase;
use Tomahawk\Queue\Util\FileSystem;

/**
 * Class FileSystemTest
 *
 * @package Tomahawk\Queue\Tests\Util
 */
class FileSystemTest extends AbstractTestCase
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $testFile;

    /**
     * @var string
     */
    protected $testDirectory;

    protected function setUp()
    {
        $this->directory = __DIR__ . '/../../Resources/fs';
        $this->testFile = $this->directory . '/test.txt';
        $this->testDirectory = $this->directory . '/foo';

        if ( ! file_exists($this->directory)) {
            @mkdir($this->directory, 0775, true);
        }

        if (file_exists($this->testFile)) {
            @unlink($this->testFile);
        }

        if (file_exists($this->testDirectory)) {
            @rmdir($this->testDirectory);
        }
    }

    protected function tearDown()
    {
        if (file_exists($this->testDirectory)) {
            @rmdir($this->testDirectory);
        }

        if (file_exists($this->directory)) {
            @rmdir($this->directory);
        }

        if (file_exists($this->testFile)) {
            @unlink($this->testFile);
        }
    }

    public function testFileSystemCreatesAndReadsFile()
    {
        $fileSystem = new FileSystem();
        $fileSystem->writeFile($this->testFile, 'foobar');
        $this->assertTrue(file_exists($this->testFile));
        $this->assertEquals('foobar', $fileSystem->readFile($this->testFile));
    }

    public function testDirectoryIsCreated()
    {
        $fileSystem = new FileSystem();

        $this->assertTrue($fileSystem->mkdir($this->testDirectory, 0775));
        $this->assertTrue(file_exists($this->testDirectory));
    }
}
