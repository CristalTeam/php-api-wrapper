<?php

namespace Cristal\ApiWrapper\Bridges\Symfony\DependencyInjection;

use Cristal\ApiWrapper\Bridges\Symfony\ConnectionInterface;
use Cristal\ApiWrapper\Bridges\Symfony\Repository;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WrapperExtension extends Extension
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(ConnectionInterface::class)
            ->addTag('api_wrapper.connection');

        $container
            ->registerForAutoconfiguration(Repository::class)
            ->addTag('api_wrapper.repository_service');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
