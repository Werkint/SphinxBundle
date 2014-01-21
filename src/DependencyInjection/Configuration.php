<?php
namespace Werkint\Bundle\SphinxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class Configuration implements
    ConfigurationInterface
{
    protected $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        // @formatter:off
        $treeBuilder
            ->root($this->alias)
            ->children()
				->arrayNode('indexer')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('bin')->defaultValue('/usr/bin/indexer')->end()
					->end()
				->end()
				->arrayNode('indexes')
					->isRequired()
					->requiresAtLeastOneElement()
					->useAttributeAsKey('key')
					->prototype('scalar')->end()
				->end()
				->arrayNode('searchd')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('host')->defaultValue('localhost')->end()
						->scalarNode('port')->defaultValue('9312')->end()
						->scalarNode('socket')->defaultNull()->end()
					->end()
				->end()
            ->end()
        ;
        // @formatter:on

        return $treeBuilder;
    }

}
