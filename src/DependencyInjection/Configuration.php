<?php
/**
 * PHP version 7.1
 *
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Auth\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Classe décrivant la configuration du AuthBundle.
 *
 * Exemple de configuration yaml :
 *
 * <pre>
 * auth:
 *   authenticator_url: 'https://auth.etna-alternance.net'
 *   api_path: '^/admin?'
 *   cookie_expiration: '+10years'
 * </pre>
 *
 * @example TestApp/config/packages/test/auth.php Exemple de configuration PHP
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Configure la structure de la configuration du AuthBundle.
     *
     * @return TreeBuilder Contient la config
     */
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
