<?php

namespace Sherlockode\SyliusNorbrPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sherlockode_sylius_norbr');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->arrayNode('model')
                        ->children()
                            ->scalarNode('token')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
