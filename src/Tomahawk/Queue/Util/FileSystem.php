<?php

namespace Tomahawk\Queue\Util;

class FileSystem
{
    /**
     * Make directory
     *
     * @param $dir
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function mkdir(string $dir, int $mode = 0777, bool $recursive = false)
    {
        return mkdir($dir, $mode, $recursive);
    }

    /**
     * Write file
     *
     * @param $fileName
     * @param $contents
     * @return int
     */
    public function writeFile($fileName, $contents)
    {
        return file_put_contents($fileName, $contents);
    }

    /**
     * Read file
     *
     * @param $fileName
     * @return string
     */
    public function readFile($fileName)
    {
        return file_get_contents($fileName);
    }

    /**
     * Delete file
     *
     * @param $fileName
     * @return string
     */
    public function unlink($fileName)
    {
        return unlink($fileName);
    }

    /**
     * Check if a file exists
     *
     * @param $fileName
     * @return bool
     */
    public function exists($fileName)
    {
        return file_exists($fileName);
    }

    /**
     * Get path info for a file
     *
     * @param $filename
     * @param $flag
     * @return mixed
     */
    public function pathinfo($filename, $flag)
    {
        return pathinfo($filename, $flag);
    }

    /**
     * Get pid files from directory
     *
     * @param $directory
     * @return array
     */
    public function getPidFiles($directory)
    {
        return glob(sprintf('%s/*.pid', $directory));
    }
}
