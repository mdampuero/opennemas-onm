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
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

$sc = new ContainerBuilder();
$sc->setParameter('charset', 'UTF-8');

# {{ Move this to a container compiler
$fileLocator = new FileLocator(__DIR__.'/config/');
$router = new Symfony\Component\Routing\Router(
    new YamlFileLoader($fileLocator),
    'routing.yml',
    array('cache_dir' => __DIR__.'/cache'),
    new Symfony\Component\Routing\RequestContext()
);
$sc->set('router', $router);
$sc->set('matcher', $router->getMatcher());
# }}

$loader = new Loader\YamlFileLoader($sc, $fileLocator);
$loader->load('app.yml');

# {{ Move this to a container compiler
$definition = $sc->getDefinition('dispatcher');
$taggedServices = $sc->findTaggedServiceIds('dispatcher.listener');
foreach ($taggedServices as $id => $attributes) {
    $definition->addMethodCall('addSubscriber', array(new Reference($id)));
}
# }}

return $sc;
