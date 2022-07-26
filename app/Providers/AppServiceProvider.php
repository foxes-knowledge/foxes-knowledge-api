<?php

namespace App\Providers;

use App\Services\Elasticsearch\ElasticsearchService;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (config('app.env') === 'local') {
            try {
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            } catch (\Throwable $ignored) {
            }
        }

        $this->app->bind(ElasticsearchService::class, function () {
            return new ElasticsearchService(
                $this->app->make(Client::class)
            );
        });

        $this->bindSearchClient();
    }

    private function bindSearchClient(): void
    {
        $this->app->bind(Client::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts($app['config']->get('elasticsearch.config.hosts'))
                ->build();
        });
    }
}
