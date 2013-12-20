<?php

namespace JF\GitHubBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jf_git_hub');

        $rootNode
                ->children()
                    ->scalarNode('branch')->defaultValue('master')->cannotBeEmpty()->end()
                    ->scalarNode('name')->defaultValue('Project')->cannotBeEmpty()->end()
                    ->scalarNode('repository_path')->cannotBeEmpty()->end()
                    ->arrayNode('deploy')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('sudo')->defaultValue(true)->cannotBeEmpty()->end()            //[true,false]
                            ->scalarNode('pwd')->defaultValue(true)->cannotBeEmpty()->end()            
                            ->scalarNode('git')->defaultValue('always')->cannotBeEmpty()->end()         //[always,none]
                            ->scalarNode('cache')->defaultValue('always')->cannotBeEmpty()->end()       //[always,none]
                            ->scalarNode('composer')->defaultValue('always')->cannotBeEmpty()->end()    //[always,none]
                            ->scalarNode('db')->defaultValue('doctrine')->cannotBeEmpty()->end()        //[doctrine,none]
                            ->scalarNode('dump')->defaultValue('check')->cannotBeEmpty()->end()         //[always,check,none]
                            ->scalarNode('install')->defaultValue('check')->cannotBeEmpty()->end()      //[always,check,none]
                            ->scalarNode('warmup')->defaultValue('always')->cannotBeEmpty()->end()      //[always,none]
                            ->scalarNode('chown')->end()                                                //user.group
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
