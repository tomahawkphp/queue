# Tomahawk Queue

A nice and simple PHP Worker Queue library


## Requirements

- PHP 7.0 +
- pcntl extension.
- posix extension.


## Installation

You can install Tomahawk Queue using composer:

`composer require tomahawk/queue`


### 1. Setup configuration

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


### 2. Create Bootstrap file

Bootstrap example file

```php
<?php

use Tomahawk\Queue\Application;
use Tomahawk\Queue\Storage\StorageInterface;
use Tomahawk\Queue\Storage\RedisStorage;
use Predis\Client;
use Pimple\Container;
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

// Set storage for jobs
$container[StorageInterface::class] = function(Container $c) {
    $client = new Client([
         'scheme' => 'tcp',
         'host'   => '10.0.0.1',
         'port'   => 6379,
    ]);
    return new RedisStorage($client);
};

$eventDispatcher = $container[EventDispatcherInterface::class];

// Add events
$eventDispatcher->addListener(\Tomahawk\Queue\JobEvents::PROCESSED, function(\Tomahawk\Queue\Event\PreProcessEvent $event) {
    // Log to a file
});

$container[EventDispatcherInterface::class];

```

## Using the CLI

### Create a new worker
 
```./bin/tomahawk-queue work emails emails --daemon```

### Queue a new job to worker
 
```./bin/tomahawk-queue queue emails JobClass {"id":"1"}```

### List running workers
 
```./bin/tomahawk-queue list```

### Stop a running worker
 
```./bin/tomahawk-queue stop emails```

### Load and run all workers defined in configuration file
 
```./bin/tomahawk-queue load```

## Using the Queue Manager

If you have your works setup on a different VM or server you can still push jobs onto the queue using the Queue Manager.

Below is an example of how to do this
 
 ```php
 <?php 
 
 use Predis\Client;
 use Tomahawk\Queue\Manager;
 use Tomahawk\Queue\Storage\RedisStorage;
 
 $client = new Client([
      'scheme' => 'tcp',
      'host'   => '10.0.0.1',
      'port'   => 6379,
 ]);
 $storage = new RedisStorage($client);
     
 $manager = new Manager($storage);
 
 $arguments = [
     'email' => '...',
     'subject' => '...',
];
 
 $manager->queue('queue_email', 'JobClass', $arguments);
 ```

## License

Tomahawk Queue is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
