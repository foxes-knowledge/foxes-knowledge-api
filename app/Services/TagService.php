<?php

namespace App\Services;

use App\Models\Tag;
use App\Services\Elasticsearch\ElasticsearchCollection;
use App\Services\Elasticsearch\ElasticsearchService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TagService
{
    public function getTags(Request $request): Collection|ElasticsearchCollection
    {
        $search = $request->input('search');
        if (isset($search)) {
            $elasticsearchService = app(ElasticsearchService::class);

            return $elasticsearchService->search(Tag::class, ['name'], $search)
                ->with(['posts', 'parent']);
        }

        $tags = Tag::query()
            ->with(['posts', 'parent']);

        return $tags->get();
    }
}
