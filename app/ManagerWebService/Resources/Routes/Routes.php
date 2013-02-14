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

$routes->add(
    'managerws_root',
    new Route(
        '/{url}',
        array(
            '_controller' => 'ManagerWebService:Controllers:WebServiceController:default',
            'url' => 'default'
        ),
        array(
            'url' => '.*'
        )
    )
);
