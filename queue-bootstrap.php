<?php

/**
 * Tomahawk
 *
 * @author Tom Ellis <tellishtc@gmail.com>
 *
 */

use Pimple\Container;
use Predis\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Queue\Application;
use Tomahawk\Queue\Storage\RedisStorage;
use Tomahawk\Queue\Storage\StorageInterface;

/**
 * Get the Autoloader
 */
require_once(__DIR__.'/vendor/autoload.php');

/**
 * Set Default Timezone
 */

date_default_timezone_set('Europe/London');

$container = Application::getContainer();

$container[StorageInterface::class] = function(Container $c) {
    $client = new Client([
        //'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ]);
    return new RedisStorage($client);
};

/** @var EventDispatcherInterface $eventDispatcher */
$eventDispatcher = $container[EventDispatcherInterface::class];
/*$eventDispatcher->addListener(\Tomahawk\Queue\JobEvents::PRE_PROCESS, function(\Tomahawk\Queue\Event\PreProcessEvent $event) {
    $event->cancel();
});*/