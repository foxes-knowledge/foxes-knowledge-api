<?php

namespace App\Services\Elasticsearch;

use App\Observers\ElasticSearchObserver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionMethod;

trait Searchable
{
    public static function bootSearchable(): void
    {
        if (config('elasticsearch.default_index')) {
            static::observe(ElasticsearchObserver::class);
        }
    }

    public function getSearchIndex(): string
    {
        return $this->getTable();
    }

    public function getSearchType(): mixed
    {
        if (property_exists($this, 'useSearchType')) {
            return $this->useSearchType;
        }

        return $this->getTable();
    }

    public function toSearchArray(): array
    {
        return $this->toArray();
    }

    /**
     * New From Hit Builder
     *
     * Variation on newFromBuilder. Instead, takes
     *
     * @param  array  $hit
     * @return Model
     */
    public function newFromHitBuilder($hit = []): Model
    {
        $key_name = $this->getKeyName();

        $attributes = $hit['_source'];

        if (isset($hit['_id'])) {
            $attributes[$key_name] = is_int($hit['_id']) ? intval($hit['_id']) : $hit['_id'];
        }

        // Add fields to attributes
        if (isset($hit['fields'])) {
            foreach ($hit['fields'] as $key => $value) {
                $attributes[$key] = $value;
            }
        }

        $instance = $this::newFromBuilderRecursive($this, $attributes);

        // In addition to setting the attributes
        // from the index, we will set the score as well.
        $instance->documentScore = $hit['_score'];

        // This is now a model created
        // from an Elasticsearch document.
        $instance->isDocument = true;

        // Set our document version if it's
        if (isset($hit['_version'])) {
            $instance->documentVersion = $hit['_version'];
        }

        return $instance;
    }

    /**
     * Create an elacticquent result collection of models from plain elasticsearch result.
     */
    public static function hydrateElasticsearchResult(array $result): ElasticsearchCollection
    {
        $items = $result['hits']['hits'];

        return static::hydrateElasticquentResult($items, $meta = $result);
    }

    /**
     * Create an elacticquent result collection of models from plain arrays.
     */
    public static function hydrateElasticquentResult(array $items, array $meta = null): ElasticsearchCollection
    {
        $instance = new static;

        $items = array_map(function ($item) use ($instance) {
            return $instance->newFromHitBuilder($item);
        }, $items);

        return $instance->newElasticquentResultCollection($items, $meta);
    }

    /**
     * Create a new model instance that is existing recursive.
     */
    public static function newFromBuilderRecursive(Model $model, array $attributes = [], Relation $parentRelation = null): Model
    {
        $instance = $model->newInstance([], $exists = true);

        $instance->setRawAttributes((array) $attributes, $sync = true);

        // Load relations recursive
        static::loadRelationsAttributesRecursive($instance);
        // Load pivot
        static::loadPivotAttribute($instance, $parentRelation);

        return $instance;
    }

    /**
     * Get the relations attributes from a model.
     */
    public static function loadRelationsAttributesRecursive(Model $model): void
    {
        $attributes = $model->getAttributes();

        foreach ($attributes as $key => $value) {
            if (method_exists($model, $key)) {
                $reflection_method = new ReflectionMethod($model, $key);

                // Check if method class has or inherits Illuminate\Database\Eloquent\Model
                if (! static::isClassInClass("Illuminate\Database\Eloquent\Model", $reflection_method->class)) {
                    $relation = $model->$key();

                    if ($relation instanceof Relation) {
                        // Check if the relation field is single model or collections
                        if (is_null($value) === true || ! static::isMultiLevelArray($value)) {
                            $value = [$value];
                        }

                        $models = static::hydrateRecursive($relation->getModel(), $value, $relation);

                        // Unset attribute before match relation
                        unset($model[$key]);
                        $relation->match([$model], $models, $key);
                    }
                }
            }
        }
    }

    /**
     * Get the pivot attribute from a model.
     */
    public static function loadPivotAttribute(Model $model, Relation $parentRelation = null): void
    {
        $attributes = $model->getAttributes();

        foreach ($attributes as $key => $value) {
            if ($key === 'pivot') {
                unset($model[$key]);
                $pivot = $parentRelation->newExistingPivot($value);
                $model->setRelation($key, $pivot);
            }
        }
    }

    /**
     * Create a collection of models from plain arrays recursive.
     */
    public static function hydrateRecursive(Model $model, array $items, Relation $parentRelation = null): Collection
    {
        $instance = $model;

        $items = array_map(function ($item) use ($instance, $parentRelation) {
            // Convert all null relations into empty arrays
            $item = $item ?: [];

            return static::newFromBuilderRecursive($instance, $item, $parentRelation);
        }, $items);

        return $instance->newCollection($items);
    }

    /**
     * Create a new Elasticquent Result Collection instance.
     */
    public function newElasticquentResultCollection(array $models = [], array $meta = null): ElasticsearchCollection
    {
        return new ElasticsearchCollection($models, $meta);
    }
}
