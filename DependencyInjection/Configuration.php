<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('enhavo_backup');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('driver')->defaultValue('doctrine/orm')->end()
                ->scalarNode('object_manager')->defaultValue('default')->end()
                ->scalarNode('tmp_dir')->defaultValue('media/backup/tmp')->end()
            ->end()
            ->children()
                ->arrayNode('backups')
                    ->useAttributeAsKey('key')
                    ->arrayPrototype()
                        ->children()
                            ->variableNode('sources')
                                ->cannotBeEmpty()
                            ->end()
                            ->variableNode('normalizer')
                                ->cannotBeEmpty()
                            ->end()
                            ->variableNode('storages')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
