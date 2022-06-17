<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TagTest extends TestCase
{
    use WithFaker;

    public function testTagsShowSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/tags');
        $response
            ->assertOk();
    }

    public function testTagsShowFailUnauth(): void
    {
        $response = $this->getJson('/api/tags');
        $response
            ->assertStatus(401);
    }

    public function testTagsShowFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->getJson('/api/tag');
        $response
            ->assertStatus(404);
    }

    public function testTagStoreSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $tag = [
            'name' => $this->faker->colorName(),
            'color' => $this->faker->hexColor(),
        ];
        $response = $this->postJson('/api/tags', $tag);
        $response
            ->assertCreated();
    }

    public function testTagStoreFailUnauth(): void
    {
        $tag = [
            'name' => $this->faker->colorName(),
            'color' => $this->faker->hexColor(),
        ];
        $response = $this->postJson('/api/tags', $tag);
        $response
            ->assertStatus(401);
    }

    public function testTagStoreFailUnprocessable(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $tag = [
            'name' => '',
            'color' => '',
        ];
        $response = $this->postJson('/api/tags', $tag);
        $response
            ->assertStatus(422);
    }

    public function testTagByIdShowSuccess(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/tags/' . $tag->id);
        $response
            ->assertOk();
    }

    public function testTagByIdShowFailUnauth(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);
        $response = $this->getJson('/api/tags/' . $tag->id);
        $response
            ->assertStatus(401);
    }

    public function testTagByIdShowFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/tags/10000');
        $response
            ->assertStatus(404);
    }

    public function testTagByIdShowFailBadContent(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/tags/wef');
        $response
            ->assertStatus(500);
    }

    public function testTagUpdateSuccess(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $dataForUpdateTag = [
            'color' => $this->faker->hexColor()
        ];

        $response = $this->json('PUT', '/api/tags/' . $tag->id, $dataForUpdateTag);
        $response
            ->assertCreated();
        $this->assertDatabaseHas(
            Tag::class,
            array_merge(
                ['id' => $tag->id],
                $dataForUpdateTag
            )
        );
    }

    public function testTagUpdateFailUnauth(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);

        $dataForUpdateTag = [
            'color' => $this->faker->hexColor()
        ];
        $response = $this->json('PUT', '/api/tags/' . $tag->id, $dataForUpdateTag);
        $response
            ->assertStatus(401);
    }

    public function testTagUpdateFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->json('PUT', '/api/tags/2434', [
            'color' => $this->faker->hexColor()
        ]);
        $response
            ->assertStatus(404);
    }

    public function testTagUpdateFailUnprocessable(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $dataForUpdateTag = [
            'color' => ''
        ];

        $response = $this->json('PUT', '/api/tags/' . $tag->id, $dataForUpdateTag);
        $response
            ->assertStatus(422);
        $this->assertDatabaseMissing(
            Tag::class,
            array_merge(
                ['id' => $tag->id],
                $dataForUpdateTag
            )
        );
    }

    public function testTagDestroySuccess(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->json('Delete', '/api/tags/' . $tag->id);
        $response
            ->assertStatus(204);
        $this->assertDatabaseMissing(Tag::class, [
            'id' => $tag->id
        ]);
    }

    public function testTagDestroyFailUnauth(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);
        $response = $this->json('Delete', '/api/tags/' . $tag->id);
        $response
            ->assertStatus(401);
        $this->assertDatabaseHas(Tag::class, [
            'id' => $tag->id
        ]);
    }

    public function testTagDestroyFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->json('Delete', '/api/tags/00000');
        $response
            ->assertStatus(404);
    }
}
