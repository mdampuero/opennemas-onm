<?php
/**
 * Validates and merges the bundle configuration
 *
 * @package Framework_DependencyInjection
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://bit.ly/197017v}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('opennemas');
        $rootNode
            ->children()
                ->arrayNode('paths')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('extensions_assets_path')
                            ->defaultValue('/media/core/extensions')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('assetic')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('asset_compilation_in_dev')
                            ->defaultValue('false')
                        ->end()
                        ->scalarNode('asset_url')
                            ->defaultValue('asset_url')
                        ->end()
                        ->scalarNode('cache_path')
                            ->defaultValue(SITE_PATH . 'compile')
                        ->end()
                        ->scalarNode('build_path')
                            ->defaultValue('build/assets')
                        ->end()
                        ->scalarNode('output_path')
                            ->defaultValue('build/assets/dist')
                        ->end()
                        ->scalarNode('asset_domain')
                            ->defaultValue(false)
                        ->end()
                        ->scalarNode('asset_servers')
                            ->defaultValue(1)
                        ->end()
                        ->scalarNode('use_asset_servers')
                            ->defaultValue(true)
                        ->end()
                        ->scalarNode('root')
                            ->defaultValue(SITE_PATH)
                        ->end()
                        ->arrayNode('filters')
                            ->children()
                                ->arrayNode('uglifyjs')
                                    ->children()
                                        ->scalarNode('bin')->end()
                                        ->scalarNode('node')->end()
                                        ->arrayNode('options')
                                            ->prototype('boolean')->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('uglifycss')
                                    ->children()
                                        ->scalarNode('bin')->end()
                                        ->scalarNode('node')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('less')
                                    ->children()
                                        ->scalarNode('node')->end()
                                        ->arrayNode('node_paths')
                                            ->prototype('scalar')->end()
                                        ->end()
                                        ->arrayNode('options')
                                            ->children()
                                                ->arrayNode('sourceMap')
                                                    ->prototype('scalar')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('folders')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('common')
                                    ->defaultValue('assets')
                                ->end()
                                ->scalarNode('themes')
                                    ->defaultValue('themes')
                                ->end()
                                ->scalarNode('bundles')
                                    ->defaultValue('bundles')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('orm')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('config_path')
                            ->defaultValue('config/orm')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
