<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('terminal42_tus');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('upload_dir')->defaultValue('%kernel.project_dir%/var/tus_php/uploads')->end()
                ->scalarNode('expires')->defaultValue('daily')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
