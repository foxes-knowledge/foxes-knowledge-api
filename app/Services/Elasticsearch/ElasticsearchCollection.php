<?php

namespace App\Services\Elasticsearch;

use App\Services\Elasticsearch\ElasticsearchPaginator as Paginator;
use Illuminate\Support\Collection;

class ElasticsearchCollection extends Collection
{
    protected int $took;

    protected bool $timed_out;

    protected array $shards;

    protected array $hits;

    protected array|null $aggregations = null;

    /**
     * @param  mixed  $items
     * @param  array  $meta
     * @return void
     */
    public function __construct($items, $meta = null)
    {
        if (isset($items['hits'])) {
            $instance = $meta;
            $meta = $items;
            $items = $instance::hydrateElasticsearchResult($meta);
        }
        parent::__construct($items);
        if (is_array($meta)) {
            $this->setMeta($meta);
        }
    }

    /**
     * Set the result meta.
     *
     * @param  array  $meta
     * @return $this
     */
    public function setMeta(array $meta)
    {
        $this->took = isset($meta['took']) ? $meta['took'] : null;
        $this->timed_out = isset($meta['timed_out']) ? $meta['timed_out'] : null;
        $this->shards = isset($meta['_shards']) ? $meta['_shards'] : null;
        $this->hits = isset($meta['hits']) ? $meta['hits'] : null;
        $this->aggregations = isset($meta['aggregations']) ? $meta['aggregations'] : [];

        return $this;
    }

    /**
     * Total Hits
     *
     * @return int
     */
    public function totalHits()
    {
        return $this->hits['total'];
    }

    /**
     * Max Score
     *
     * @return float
     */
    public function maxScore()
    {
        return $this->hits['max_score'];
    }

    /**
     * Get Shards
     *
     * @return array
     */
    public function getShards()
    {
        return $this->shards;
    }

    public function took(): int
    {
        return $this->took;
    }

    /**
     * Timed Out
     *
     * @return bool
     */
    public function timedOut()
    {
        return (bool) $this->timed_out;
    }

    /**
     * Get Hits
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Get aggregations
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Paginate Collection
     *
     * @param  int  $pageLimit
     * @return Paginator
     */
    public function paginate($pageLimit = 25)
    {
        $page = Paginator::resolveCurrentPage() ?: 1;

        return new Paginator(
            $this->items,
            count($this->items),
            $pageLimit,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    public function with(array $relations): ElasticsearchCollection
    {
        foreach ($this->items as $key => $item) {
            $builder = $item->where('id', $item->id);
            foreach ($relations as $relation) {
                $builder->with($relation);
            }
            $this->items[$key] = $builder->first();
        }

        return $this;
    }
}
