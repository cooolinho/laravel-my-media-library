<?php

namespace App\Http\Client\TheTVDB;

class TheTVDBApiResponse
{
    private string $status;
    private array $data;
    private array $links;

    public function __construct(array $response = [])
    {
        $this->status = $response['status'] ?? 'error';
        $this->data = $response['data'] ?? [];
        $this->links = $response['links'] ?? [];
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
}
