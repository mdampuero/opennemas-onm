<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Load the available route collection
$routes = new RouteCollection();

$managerRoutes = new RouteCollection();

$managerRoutes->add(
    'manager_instances',
    new Route(
        '/instances',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:list',
        )
    ),
    'manager'
);

$managerRoutes->add(
    'manager_welcome',
    new Route(
        '/',
        array('_controller' => 'Manager:Controllers:WelcomeController:default')
    ),
    'manager'
);

$routes->addCollection($managerRoutes);
