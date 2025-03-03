<?php

namespace App\Http\Client\TheTvDB;

class SearchResult
{
    private string $status;
    private array $data;
    private array $links;

    public function __construct(array $response)
    {
        $this->status = $response['status'] ?? 'error';
        $this->data = $response['data'] ?? [];
        $this->links = $response['links'] ?? [];
    }

    public function hasLinkPrevious(): bool
    {
        return $this->links['prev'] !== null;
    }

    public function hasLinkNext(): bool
    {
        if (($this->getCurrentPage() + 1) === $this->getTotalPages()) {
            return false;
        }

        return $this->links['next'] !== null;
    }

    public function getTotalItems(): int
    {
        return $this->links['total_items'] ?? 0;
    }

    public function getPageSize(): int
    {
        return $this->links['page_size'] ?? 0;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getTotalPages(): int
    {
        return (int)ceil($this->getTotalItems() / $this->getPageSize());
    }

    /**
     * read page number from link self
     * @return int
     */
    public function getCurrentPage(): int
    {
        $url = $this->links['self'];
        $parts = parse_url($url);

        $queryParams = [];
        parse_str($parts['query'], $queryParams);

        return isset($queryParams['page']) ? (int) $queryParams['page'] : 0;
    }
}
