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

$frontendRoutes = new RouteCollection();

// Common content management routes
$frontendRoutes->add(
    'frontend_ad_get',
    new Route(
        '/adss/get/{id}',
        array(
            '_controller' => 'Frontend:Controllers:AdvertisementController:get',
        )
    )
);

$frontendRoutes->add(
    'frontend_ad_redirect',
    new Route(
        '/adss/show/{id}',
        array(
            '_controller' => 'Frontend:Controllers:AdvertisementController:redirect',
        )
    )
);

$routes->addCollection($frontendRoutes);
