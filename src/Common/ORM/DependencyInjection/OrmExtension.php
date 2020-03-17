<?php

namespace Common\ORM\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OrmExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        // Parse config to use item names as keys
        $orm = [];
        foreach ($config as $type => $values) {
            $keys = array_map(function ($a) {
                return $a['name'];
            }, $values);

            $orm[$type] = array_combine($keys, $values);
        }

        $container->setParameter('orm', $orm);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $path = !$container->hasParameter('orm.path')
            ? __DIR__ . '/../Resources/config/orm'
            : $container->getParameter('kernel.root_dir')
                . $container->getParameter('orm.path');

        $model = $container->hasParameter('orm.model')
            ? $container->getParameter('orm.model')
            : 'model.yml';

        $loader = new Loader\YamlFileLoader($container, new FileLocator($path));
        $loader->load($model);
    }
}
