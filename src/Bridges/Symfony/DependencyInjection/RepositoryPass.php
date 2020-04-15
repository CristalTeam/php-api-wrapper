<?php

namespace Cristal\ApiWrapper\Bridges\Symfony\DependencyInjection;

use Cristal\ApiWrapper\Bridges\Symfony\ManagerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RepositoryPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ManagerRegistry::class)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds('api_wrapper.repository_service');

        foreach ($taggedServices as $id => $tags) {
            $container
                ->getDefinition($id)
                ->setAutowired(true)
                ->setPublic(true);
        }
    }
}
