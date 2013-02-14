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

$routes->add(
    'managerws_root',
    new Route(
        '/managerws/{url}',
        array(
            '_controller' => 'ManagerWebService:Controllers:WebServiceController:default',
            'url' => 'default'
        ),
        array(
            'url' => '.*'
        )
    )
);
