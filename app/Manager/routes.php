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

$routes->add(
    'manager_instances',
    new Route(
        '/instances/',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:list',
        )
    ),
    '/manager'
);