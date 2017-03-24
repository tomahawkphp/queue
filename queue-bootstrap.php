<?php

/**
 * Tomahawk
 *
 * @author Tom Ellis <tellishtc@gmail.com>
 *
 */

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Queue\Application;

/**
 * Get the Autoloader
 */
require_once(__DIR__.'/vendor/autoload.php');

/**
 * Set Default Timezone
 */

date_default_timezone_set('Europe/London');


$container = Application::getContainer();

/** @var EventDispatcherInterface $eventDispatcher */
$eventDispatcher = $container[EventDispatcherInterface::class];
/*$eventDispatcher->addListener(\Tomahawk\Queue\JobEvents::PRE_PROCESS, function(\Tomahawk\Queue\Event\PreProcessEvent $event) {
    $event->cancel();
});*/