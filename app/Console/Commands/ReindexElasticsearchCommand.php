<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReindexElasticsearchCommand extends Command
{
    protected $signature = 'search:reindex {class}';

    protected $description = 'Indexes all elements of class to Elasticsearch';

    private Client $elasticsearch;

    private string $modelPath = 'App\\Models\\';

    public function __construct(Client $elasticsearch)
    {
        parent::__construct();
        $this->elasticsearch = $elasticsearch;
    }

    public function handle(): void
    {
        $class = ($this->modelPath.Str::ucfirst($this->argument('class')));
        foreach ($class::cursor() as $element) {
            $this->elasticsearch->index([
                'index' => $element->getSearchIndex(),
                'type' => $element->getSearchType(),
                'id' => $element->getKey(),
                'body' => $element->toSearchArray(),
            ]);
            $this->output->write('.');
        }
        $this->info('\nDone!');
    }
}
