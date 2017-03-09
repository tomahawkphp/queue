<?php

/**
 * Tomahawk
 *
 * @author Tom Ellis <tellishtc@gmail.com>
 *
 */

use Tomahawk\Queue\Application;

/**
 * Get the Autoloader
 */
require_once(__DIR__.'/vendor/autoload.php');

/**
 * Set Default Timezone
 */

date_default_timezone_set('Europe/London');


//Application::getContainer()->register()