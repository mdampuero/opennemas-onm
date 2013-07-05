<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

$sc = new ContainerBuilder();

// Load parameters from an configuration file
$loader = new YamlFileLoader($sc, new FileLocator(__DIR__.'/config/'));

$sc->setParameter('routes', $routes);
$sc->setParameter('request', $request);
$sc->set('event_dispatcher', new ContainerAwareEventDispatcher($sc));

$loader->load('app.yml');

return $sc;

