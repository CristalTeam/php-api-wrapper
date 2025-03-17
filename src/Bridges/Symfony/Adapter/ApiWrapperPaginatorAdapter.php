<?php

namespace Cristal\ApiWrapper\Bridges\Symfony\Adapter;

use ApiPlatform\Core\DataProvider\PaginatorInterface as ApiPlatformPaginator;
use Cristal\ApiWrapper\Bridges\Symfony\PaginatorInterface;

class ApiWrapperPaginatorAdapter implements \IteratorAggregate, ApiPlatformPaginator
{
    public function __construct(private PaginatorInterface $paginator)
    {
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->paginator->count();
    }

    /**
     * @inheritDoc
     */
    public function getLastPage(): float
    {
        return $this->paginator->getLastPage() ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function getTotalItems(): float
    {
        return $this->paginator->getTotalItems();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPage(): float
    {
        return $this->paginator->getCurrentPage() ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function getItemsPerPage(): float
    {
        return $this->paginator->getItemsPerPage() ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->paginator->getIterator();
    }
}
