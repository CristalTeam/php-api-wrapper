<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;

interface PaginatorInterface extends \Countable, \IteratorAggregate
{
    public function getLastPage();

    public function getTotalItems();

    public function getCurrentPage();

    public function getItemsPerPage();
}
