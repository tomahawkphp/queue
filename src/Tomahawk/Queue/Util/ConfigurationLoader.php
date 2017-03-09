<?php

namespace Tomahawk\Queue\Util;

use DOMDocument;
use DOMXPath;

/**
 * Class ConfigurationLoader
 *
 * @package Tomahawk\Queue\Util
 */
class ConfigurationLoader
{
    /**
     * @var string
     */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Load configuration for queues
     *
     * @return Configuration
     */
    public function load()
    {
        $contents  = file_get_contents($this->directory . '/tomahawk.xml');

        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $internal  = libxml_use_internal_errors(true);
        $reporting = error_reporting(0);
        $document->loadXML($contents);

        $xpath = new DOMXPath($document);

        $root = $document->documentElement;

        $configuration = [];

        if ($root->hasAttribute('bootstrap')) {
            $configuration['bootstrap'] = $root->getAttribute('bootstrap');
        }

        foreach ($xpath->query('workers/worker') as $worker) {

            /** @var \DOMNode $worker */

            $name = $worker->attributes->getNamedItem('name')->textContent;
            $number = $worker->attributes->getNamedItem('number')->textContent;
            $queues = $worker->attributes->getNamedItem('queues')->textContent;

            $configuration['workers'][] = [
                'name'   => $name,
                'number' => $number,
                'queues' => $queues
            ];

        }

        libxml_use_internal_errors($internal);
        error_reporting($reporting);

        return new Configuration($configuration);
    }
}
