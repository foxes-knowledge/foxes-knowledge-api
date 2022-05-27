<?php

namespace Database\Seeders;

use App\Models\Tag;
use Database\Factories\TagFactory;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'JS',
            ],
            [
                'tag_id' => 1,
                'name' => 'React.js',
            ],
            [
                'tag_id' => 1,
                'name' => 'Next.js',
            ],
            [
                'name' => 'PHP',
            ],
            [
                'tag_id' => 4,
                'name' => 'Laravel',
            ],
            [
                'tag_id' => 4,
                'name' => 'Symfony',
            ],
            [
                'name' => 'PostgreSQL',
            ],
            [
                'name' => 'MySQL',
            ],
        ];

        foreach ($data as $tag) {
            Tag::create($tag);
        }

        Tag::factory()->count(TagFactory::NUMBER + count($data))->create();
    }
}
