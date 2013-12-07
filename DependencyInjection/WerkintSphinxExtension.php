<?php
namespace Werkint\Bundle\SphinxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * WerkintSphinxExtension.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class WerkintSphinxExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(
        array $configs,
        ContainerBuilder $container
    ) {
        $processor = new Processor();
        $config = $processor->processConfiguration(
            new Configuration($this->getAlias()),
            $configs
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        /**
         * Indexer.
         */
        if (isset($config['indexer'])) {
            $container->setParameter(
                $this->getAlias() . '.indexer.bin',
                $config['indexer']['bin']
            );
        }

        /**
         * Indexes.
         */
        $container->setParameter(
            $this->getAlias() . '.indexes',
            $config['indexes']
        );

        /**
         * Searchd.
         */
        if (isset($config['searchd'])) {
            $container->setParameter(
                $this->getAlias() . '.searchd.host',
                $config['searchd']['host']
            );
            $container->setParameter(
                $this->getAlias() . '.searchd.port',
                $config['searchd']['port']
            );
            $container->setParameter(
                $this->getAlias() . '.searchd.socket',
                $config['searchd']['socket']
            );
        }
    }
}
