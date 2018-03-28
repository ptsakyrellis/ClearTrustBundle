<?php
namespace AcToulouse\ClearTrustBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('clear_trust');

        $rootNode
            ->children()
            ->scalarNode('rsa_remote_user')->end()
            ->scalarNode('rsa_cookie_name')->end()
            ->scalarNode('logout_target_url')->end()
            ->scalarNode('login_target_url')->end()
            ->end()
            ->fixXmlConfig('attribute_definition')
            ->children()
            ->arrayNode('attribute_definitions')
            ->useAttributeAsKey('alias')
            ->prototype('array')
            ->children()
            ->scalarNode('header')->isRequired()->end()
            ->booleanNode('multivalue')->defaultValue(false)->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
