<?php

namespace Tomahawk\Queue\Util;

use DOMDocument;
use DOMXPath;
use Tomahawk\Queue\Util\Worker\Settings;

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
                $queues = $worker->attributes->getNamedItem('queues')->textContent;
                $autoloadSetting = $worker->attributes->getNamedItem('autoload')->textContent;

                // If autoload is not set then default to true
                if ( ! $autoloadSetting) {
                    $autoloadSetting = 'true';
                }

                $autoload = 'true' === $autoloadSetting;

                // Is it better to hold the worker settings in a class or array??

                //$configuration['workers'][$pidkey] = new Settings($name, $pidkey, $queues, $autoload);

                $configuration['workers'][$pidkey] = [
                    'pidkey' => $pidkey,
                    'name' => $name,
                    'queues' => $queues,
                    'autoload' => $autoload,
                ];

            }

        }

        libxml_use_internal_errors($internal);
        error_reporting($reporting);

        return new Configuration($configuration);
    }
}
