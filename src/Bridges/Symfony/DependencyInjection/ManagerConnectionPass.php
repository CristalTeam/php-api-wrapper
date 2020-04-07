<?php

namespace Cristal\ApiWrapper\Bridges\Symfony\DependencyInjection;

use Cristal\ApiWrapper\Bridges\Symfony\ManagerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ManagerConnectionPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ManagerRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(ManagerRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('api_wrapper.connection');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addConnection', [new Reference($id)]);
        }
    }
}
