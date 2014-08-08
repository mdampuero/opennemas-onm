<?php
/**
 * Loads the bundle configuration
 *
 * @package Framework_DependencyInjection
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\DependencyInjection;

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
class OnmFrameworkExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (array_key_exists('assetic', $config)) {
            $container->setParameter('assetic', $config['assetic']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        // determine if AcmeGoodbyeBundle is registered
        if (isset($bundles['AsseticBundle'])) {
            $extensions = array_keys($container->getExtensions());

            if (in_array('assetic', $extensions)) {
                // process the configuration of AsseticBundle
                $configs = $container->getExtensionConfig('assetic');
                $configs = array(array('assetic' => array_pop($configs)));

                // use the Configuration class to generate a config array with the settings "assetic"
                $config = $this->processConfiguration(new Configuration(), $configs);
                $container->prependExtensionConfig('onm_framework', $config);
            }
        }
    }
}
