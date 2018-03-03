<?php

namespace SendinBlue\Bundle\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sendinblue_api');

        $rootNode
            ->beforeNormalization()
                ->ifTrue(function ($configuration) {
                    return \is_array($configuration)
                        && !\array_key_exists('clients', $configuration)
                        && !\array_key_exists('client', $configuration)
                    ;
                })
                ->then(function ($configuration) {
                    $defaultClient = 'default';
                    if (isset($configuration['default_client'])) {
                        $defaultClient = $configuration['default_client'];
                        unset($configuration['default_client']);
                    }

                    $clientConfiguration = [];
                    foreach (['key', 'endpoints'] as $clientKey) {
                        if (\array_key_exists($clientKey, $configuration)) {
                            $clientConfiguration[$clientKey] = $configuration[$clientKey];
                            unset($configuration[$clientKey]);
                        }
                    }

                    $configuration['clients'] = [$defaultClient => $clientConfiguration];

                    return $configuration;
                })
            ->end()
            ->children()
                ->scalarNode('default_client')->end()
            ->end()
            ->fixXmlConfig('client')
            ->append($this->getClientsNode())
        ;

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function getClientsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('clients');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->fixXmlConfig('endpoint')
                ->children()
                    ->arrayNode('endpoints')
                        ->prototype('enum')
                            ->values(SendinBlueApiExtension::getEndpoints())
                        ->end()
                    ->end()
                    ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
