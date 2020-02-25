<?php

namespace App\Pagination;

class Paginator
{
    private const PAGE_SIZE = 10;
    private $queryBuilder;
    private $currentPage;
    private $pageSize;
    private $results;
    private $numResults;
    private $totalResults;

    public function __construct($queryBuilder, int $pageSize = self::PAGE_SIZE)
    {
        $this->queryBuilder = $queryBuilder;
        $this->pageSize = $pageSize;
    }

    public function paginate(int $page = 1)
    {
        $this->currentPage = max(1, $page);
        $firstResult = ($this->currentPage - 1) * $this->pageSize;

        $this->totalResults = count($this->queryBuilder->execute()->fetchAll());

        $this->queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->pageSize)
        ;

        $this->results = $this->queryBuilder->execute()->fetchAll();

        $this->numResults = count($this->results);

        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return (int) ceil($this->totalResults / $this->pageSize);
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    public function hasToPaginate(): bool
    {
        return $this->totalResults > $this->pageSize;
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
