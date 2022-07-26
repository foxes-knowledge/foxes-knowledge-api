<?php

namespace App\Services\Elasticsearch;

use Elastic\Elasticsearch\Client;

class ElasticsearchService
{
    private Client $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function search(string $class, array $fields, string $query = ''): ElasticsearchCollection
    {
        $model = new $class;

        $items = $this->elasticsearch->search([
            'index' => $model->getSearchIndex(), /** @phpstan-ignore-line */
            'type' => $model->getSearchType(), /** @phpstan-ignore-line */
            'body' => [
                'query' => [
                    'multi_match' => [
                        'fields' => $fields,
                        'query' => $query,
                    ],
                ],
            ],
        ]);

        return $model->hydrateElasticsearchResult($items->asArray()); /** @phpstan-ignore-line */
    }
}
