<?php

namespace App\Services\Elasticsearch;

use Illuminate\Pagination\LengthAwarePaginator;

class ElasticsearchPaginator extends LengthAwarePaginator
{
    /**
     * Create a new paginator instance.
     *
     * @param  mixed  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int|null  $currentPage
     * @param  array  $options (path, query, fragment, pageName)
     */
    public function __construct($items, $total, $perPage, $currentPage = null, array $options = [])
    {
        parent::__construct($items, $total, $perPage, $currentPage, $options);

        $this->items = $this->items->splice(($currentPage - 1) * $perPage, $perPage);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'count' => $this->count(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'data' => $this->items->toArray(),
        ];
    }
}
