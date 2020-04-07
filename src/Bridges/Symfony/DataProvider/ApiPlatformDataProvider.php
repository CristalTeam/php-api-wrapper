<?php

namespace Cristal\ApiWrapper\Bridges\Symfony\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Cristal\ApiWrapper\Bridges\Symfony\Adapter\ApiWrapperPaginatorAdapter;
use Cristal\ApiWrapper\Bridges\Symfony\ClassMetadata;
use Cristal\ApiWrapper\Bridges\Symfony\ManagerRegistry;
use Cristal\ApiWrapper\Bridges\Symfony\PaginatorInterface;

final class ApiPlatformDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $this->managerRegistry->getMetadataFromClass($resourceClass) instanceof ClassMetadata;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $allowedFilters = $this->managerRegistry->getMetadataFromClass($resourceClass)->getAllowedFilters();

        array_intersect($allowedFilters, $context['filters'] ?? []);

        $paginator = $this->managerRegistry
            ->getRepository($resourceClass)
            ->findBy(
                $context['filters'] ?? [],
                $context['filters']['sort'] ?? null,
                $context['filters']['limit'] ?? null,
                $context['filters']['page'] ?? null,
            )
        ;

        if($paginator instanceof PaginatorInterface){
            return new ApiWrapperPaginatorAdapter($paginator);
        }

        return $paginator;
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->managerRegistry
            ->getRepository($resourceClass)
            ->find($id)
        ;
    }
}
