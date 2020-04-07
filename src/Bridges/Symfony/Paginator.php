<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;

use ArrayObject;

/**
 * This standalone paginator allows to use a paginated result when used without API Platform.
 * If you use this bundle with API Platform, this paginator will be adapted with the APIWrapperPaginatorAdapter in the DataProvider.
 *
 * @see \Cristal\ApiWrapper\Bridges\Symfony\Adapter\ApiWrapperPaginatorAdapter
 */
class Paginator implements PaginatorInterface
{
    /**
     * @var ArrayObject
     */
    private $data;

    /**
     * @var float|null
     */
    private $total;

    /**
     * @var float|null
     */
    private $itemPerPage;

    /**
     * @var float|null
     */
    private $currentPage;

    public function __construct($data, $total, $itemPerPage = null, $currentPage = null)
    {
        $this->data = new \ArrayIterator($data);
        $this->total = $total;
        $this->itemPerPage = $itemPerPage;
        $this->currentPage = $currentPage;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->data);
    }

    public function getLastPage(): ?float
    {
        if($this->itemPerPage && $this->total) {
            return ceil($this->total / $this->itemPerPage);
        }

        return null;
    }

    public function getTotalItems(): float
    {
        return $this->total;
    }

    public function getCurrentPage(): ?float
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): ?float
    {
        return $this->itemPerPage;
    }
}
