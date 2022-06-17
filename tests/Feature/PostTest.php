<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use WithFaker;

    public function testPostsShowSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/posts');
        $response
            ->assertOk();
    }

    public function testPostsShowFailUnauth(): void
    {
        $response = $this->getJson('/api/posts');
        $response
            ->assertStatus(401);
    }

    public function testPostsShowFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->getJson('/api/post');
        $response
            ->assertStatus(404);
    }

    public function testPostStoreSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $post = [
            'user_id' => $this->faker->numberBetween(1, UserFactory::NUMBER),
            'title' => $this->faker->realText(64),
            'content' => $this->faker->realText(),
            'tag_ids' => [1],
        ];
        $response = $this->postJson('/api/posts', $post);
        $response
            ->assertCreated();
    }

    public function testPostStoreFailUnauth(): void
    {
        $post = [
            'user_id' => $this->faker->numberBetween(1, UserFactory::NUMBER),
            'title' => $this->faker->realText(64),
            'content' => $this->faker->realText(),
            'tag_ids' => [1],
        ];
        $response = $this->postJson('/api/posts', $post);
        $response
            ->assertStatus(401);
    }

    public function testPostStoreFailUnprocessable(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $post = [
            'user_id' => '',
            'title' => $this->faker->realText(64),
            'content' => $this->faker->realText(),
            'tag_ids' => '',
        ];
        $response = $this->postJson('/api/posts', $post);
        $response
            ->assertStatus(422);
    }

    public function testPostByIdShowSuccess(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/posts/' . $post->id);
        $response
            ->assertOk();
    }

    public function testPostByIdShowFailUnauth(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);
        $response = $this->getJson('/api/posts/' . $post->id);
        $response
            ->assertStatus(401);
    }

    public function testPostByIdShowFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/posts/10000');
        $response
            ->assertStatus(404);
    }

    public function testPostByIdShowFailBadContent(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/posts/wef');
        $response
            ->assertStatus(500);
    }

    public function testPostUpdateSuccess(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $dataForUpdatePost = [
            'title' => $this->faker->title,
        ];

        $response = $this->json('PUT', '/api/posts/' . $post->id, $dataForUpdatePost);
        $response
            ->assertCreated();
        $this->assertDatabaseHas(
            Post::class,
            array_merge(
                ['id' => $post->id],
                $dataForUpdatePost
            )
        );
    }

    public function testPostUpdateFailUnauth(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);

        $dataForUpdatePost = [
            'title' => $this->faker->title,
        ];
        $response = $this->json('PUT', '/api/posts/' . $post->id, $dataForUpdatePost);
        $response
            ->assertStatus(401);
    }

    public function testPostUpdateFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->json('PUT', '/api/posts/2434', [
            'title' => $this->faker->title,
        ]);
        $response
            ->assertStatus(404);
    }

    public function testPostUpdateFailUnprocessable(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $dataForUpdatePost = [
            'title' => '',
        ];

        $response = $this->json('PUT', '/api/posts/' . $post->id, $dataForUpdatePost);
        $response
            ->assertStatus(422);
        $this->assertDatabaseMissing(
            Post::class,
            array_merge(
                ['id' => $post->id],
                $dataForUpdatePost
            )
        );
    }

    public function testPostDestroySuccess(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->json('Delete', '/api/posts/' . $post->id);
        $response
            ->assertStatus(204);
        $this->assertDatabaseMissing(Post::class, [
            'id' => $post->id
        ]);
    }

    public function testPostDestroyFailUnauth(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);
        $response = $this->json('Delete', '/api/posts/' . $post->id);
        $response
            ->assertStatus(401);
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id
        ]);
    }

    public function testPostDestroyFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->json('Delete', '/api/posts/00000');
        $response
            ->assertStatus(404);
    }
}
