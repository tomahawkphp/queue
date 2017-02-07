<?php

namespace Tomahawk\Queue\Util;
use DOMDocument;
use DOMXPath;

/**
 * Class Configuration
 *
 * @package Tomahawk\Queue\Util
 */
class Configuration
{
    /**
     * @var string
     */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function load()
    {
        $contents  = file_get_contents($this->directory . '/tomahawk.xml');

        //var_dump($contents);
        //exit;

        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $internal  = libxml_use_internal_errors(true);
        $message   = '';
        $reporting = error_reporting(0);
        $document->loadXML($contents);

        $xpath = new DOMXPath($document);

        $root = $document->documentElement;

        if ($root->hasAttribute('bootstrap')) {
            //echo $root->getAttribute('bootstrap');
        }

        foreach ($xpath->query('workers/worker') as $worker) {

            /** @var \DOMNode $worker */

            $queues = [];

            var_dump(
                $worker->attributes->getNamedItem('amount')->textContent,
                $worker->attributes->getNamedItem('queue')->textContent
            );


            //$root->has('workers')
            //echo $root->getAttribute('bootstrap');
        }



        //$domxpath->query('tomahawk')
    }
}