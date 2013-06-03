<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

require __DIR__.'/../app/autoload.php';

// Load the available route collection
$routes = new \Symfony\Component\Routing\RouteCollection();

$routeFiles = glob(SRC_PATH.'/*/Resources/Routes/Routes.php');
foreach ($routeFiles as $routeFile) {
    require $routeFile;
}

require 'bootstrap.php';

$sc = include __DIR__.'/../app/container.php';

$timezone = \Onm\Settings::get('time_zone');
if (isset($timezone)) {
    $availableTimezones = \DateTimeZone::listIdentifiers();
    date_default_timezone_set($availableTimezones[$timezone]);
}

if (preg_match('@^/admin@', $request->getRequestUri(), $matches)) {
    $sc->setParameter('dispatcher.exceptionhandler', 'Backend:Controllers:ErrorController:default');
} elseif (preg_match('@^/manager@', $request->getRequestUri(), $matches)) {
    $sc->setParameter('dispatcher.exceptionhandler', 'Manager:Controllers:ErrorController:default');
} else {
    $sc->setParameter('dispatcher.exceptionhandler', 'Frontend:Controllers:ErrorController:default');
}

// Dispatch the response
$dispatcher = new \Onm\Framework\Dispatcher\Dispatcher($matcher, $request, $sc);
$dispatcher->dispatch();
