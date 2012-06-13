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

$sc = new ContainerBuilder();

// Load parameters from an configuration file
$loader = new YamlFileLoader($sc, new FileLocator(__DIR__.'/config/'));
$loader->load('app.yml');

$sc->register('context', 'Symfony\Component\Routing\RequestContext')
    ->addMethodCall('fromRequest', array($request));
$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments(array($routes, new Reference('context')));
$sc->register('url_generator', '\Symfony\Component\Routing\Generator\UrlGenerator')
    ->setArguments(array($routes, new Reference('context')));

return $sc;