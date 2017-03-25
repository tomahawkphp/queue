# Tomahawk Queue

A nice and simple PHP Worker Queue library


## Requirements

- PHP 7.0 +
- pcntl extension.
- posix extension.


## Installation

You can install Tomahawk Queue using composer:

`composer require tomahawk/queue`


###Setup configuration

First you need to create a new file called `tomahawk.xml`

You will need to configure the following things:

- Storage directory - for logs and pid files
- Bootstrap file (optional) - Allows you to add event listeners and extend storage
- Your workers

Below is an example:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<tomahawk
    storage="./storage"
    bootstrap="./queue-bootstrap.php">

    <workers>
        <worker pidkey="emails" name="Emails" queues="emails" />
    </workers>

</tomahawk>
```


###Bootstrap example file

```php
<?php

use Tomahawk\Queue\Application;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Storage\RedisStorage;
use Predis\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Get the Autoloader
 */
require_once(__DIR__.'/vendor/autoload.php');

/**
 * Set Default Timezone
 */

date_default_timezone_set('Europe/London');

// Get the container
$container = Application::getContainer();

$container[StorageInterface::class] = function(Container $c) {
    $client = Client([
         'scheme' => 'tcp',
         'host'   => '10.0.0.1',
         'port'   => 6379,
    ]);
    return new RedisStorage();
};

$eventDispatcher = $container[EventDispatcherInterface::class];

// Add events
$eventDispatcher->addListener(\Tomahawk\Queue\JobEvents::PROCESSED, function(\Tomahawk\Queue\Event\PreProcessEvent $event) {
    // Log to a file
});

$container[EventDispatcherInterface::class];

```

## License

Tomahawk Queue is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
