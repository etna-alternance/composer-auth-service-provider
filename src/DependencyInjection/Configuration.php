<?php

namespace ETNA\Auth\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tree_builder = new TreeBuilder();
        $root_node    = $tree_builder->root('auth');

        $root_node
            ->children()
                ->scalarNode('authenticator_url')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('api_path')
                    ->defaultValue('^/?')
                ->end()
                ->scalarNode('cookie_expiration')
                    ->defaultFalse()
                ->end()
            ->end();

        return $tree_builder;
    }
}
