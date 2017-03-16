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
        $configurationFile = $this->directory . '/tomahawk.xml';

        $contents = '';

        if (file_exists($configurationFile)) {
            $contents  = file_get_contents($configurationFile);
        }

        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $internal  = libxml_use_internal_errors(true);
        $reporting = error_reporting(0);
        $configuration = [
            'bootstrap' => null,
            'workers' => [],
            'storage' => $this->directory // Default
        ];

        if (false !== $document->loadXML($contents)) {

            $xpath = new DOMXPath($document);

            $root = $document->documentElement;

            if ($root->hasAttribute('bootstrap')) {
                $configuration['bootstrap'] = $root->getAttribute('bootstrap');
            }

            if ($root->hasAttribute('storage')) {
                $configuration['storage'] = $root->getAttribute('storage');
            }

            foreach ($xpath->query('workers/worker') as $worker) {

                /** @var \DOMNode $worker */

                $pidkey = $worker->attributes->getNamedItem('pidkey')->textContent;
                $name = $worker->attributes->getNamedItem('name')->textContent;
                $number = $worker->attributes->getNamedItem('number')->textContent;
                $queues = $worker->attributes->getNamedItem('queues')->textContent;

                $configuration['workers'][] = [
                    'pidkey' => $pidkey,
                    'name'   => $name,
                    'number' => $number,
                    'queues' => $queues
                ];

            }

        }

        libxml_use_internal_errors($internal);
        error_reporting($reporting);

        return new Configuration($configuration);
    }
}
