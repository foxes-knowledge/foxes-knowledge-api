<?php

namespace App\Observers;

use Elastic\Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;

class ElasticSearchObserver
{
    private Client $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function saved(Model $model): void
    {
        $this->elasticsearch->index([
            'index' => $model->getSearchIndex(),
            'type' => $model->getSearchType(),
            'id' => $model->getKey(),
            'body' => $model->toSearchArray(),
        ]);
    }

    public function deleted(Model $model): void
    {
        $this->elasticsearch->delete([
            'index' => $model->getSearchIndex(),
            'type' => $model->getSearchType(),
            'id' => $model->getKey(),
        ]);
    }
}
