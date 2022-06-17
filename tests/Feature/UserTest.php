<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker;


    public function testUserShowSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/users');
        $response
            ->assertOk();
    }

    public function testUserShowFailUnauth(): void
    {
        $response = $this->getJson('/api/users');
        $response
            ->assertStatus(401);
    }

    public function testUserShowFailNotFound(): void
    {
        $response = $this->getJson('/api/user');
        $response
            ->assertStatus(404);
    }

    public function testUserStoreSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $user = [
            'username' => $this->faker->userName(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '11111111',
            'password_confirmation' => '11111111',
            'isEmailPublic' => $this->faker->boolean(30),
            'bio' => $this->faker->text(200),
            'color' => $this->faker->hexColor(),
        ];
        $response = $this->postJson('/api/users', $user);
        $response
            ->assertCreated();
    }

    public function testUserStoreFailUnath(): void
    {
        $user = [
            'username' => $this->faker->userName(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '11111111',
            'password_confirmation' => '11111111',
            'isEmailPublic' => $this->faker->boolean(30),
            'bio' => $this->faker->text(200),
            'color' => $this->faker->hexColor(),
        ];
        $response = $this->postJson('/api/users', $user);
        $response
            ->assertStatus(401);
    }

    public function testUserStoreFail(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $user = [
            'username' => '',
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'isEmailPublic' => '',
            'bio' => '',
            'color' => '',
        ];
        $response = $this->postJson('/api/users', $user);
        $response
            ->assertStatus(422);
    }

    public function testUserByIdShowSuccess(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users/' . $user->id);
        $response
            ->assertOk();
    }

    public function testUserByIdShowFailUnauth(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id
        ]);
        $response = $this->getJson('/api/users/' . $user->id);
        $response
            ->assertStatus(401);
    }

    public function testUserByIdShowFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/users/10000');
        $response
            ->assertStatus(404);
    }

    public function testUserByIdShowFailBadContent(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/users/wef');
        $response
            ->assertStatus(500);
    }


    public function testUserUpdateSuccess(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id
        ]);
        Sanctum::actingAs($user);

        $dataForUpdateUser = [
            'bio' => $this->faker->text,
        ];

        $response = $this->json('PUT', '/api/users/' . $user->id, $dataForUpdateUser);
        $response
            ->assertCreated();

        $this->assertDatabaseHas(
            User::class,
            array_merge(
                ['id' => $user->id],
                $dataForUpdateUser
            )
        );
    }

    public function testUserUpdateFailUnauth(): void
    {
        $response = $this->json('PUT', '/api/users/2', [
            'bio' => $this->faker->text,
        ]);
        $response
            ->assertStatus(401);
    }

    public function testUserUpdateFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->json('PUT', '/api/users/2434', [
            'bio' => $this->faker->text,
        ]);
        $response
            ->assertStatus(404);
    }

    public function testUserUpdateFailUnprocessable(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id
        ]);
        Sanctum::actingAs($user);

        $dataForUpdateUser = [
            'bio' => '',
        ];

        $response = $this->json('PUT', '/api/users/' . $user->id, $dataForUpdateUser);
        $response
            ->assertStatus(422);
        $this->assertDatabaseMissing(
            User::class,
            array_merge(
                ['id' => $user->id],
                $dataForUpdateUser
            )
        );
    }

    public function testUserDestroySuccess(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id
        ]);
        Sanctum::actingAs($user);
        $response = $this->json('Delete', '/api/users/' . $user->id);
        $response
            ->assertStatus(204);

        $this->assertDatabaseMissing(User::class, [
            'id' => $user->id
        ]);
    }

    public function testUserDestroyFailUnauth(): void
    {
        $user = User::factory()->create();
        $response = $this->json('Delete', '/api/users/' . $user->id);
        $response
            ->assertStatus(401);
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id
        ]);
    }

    public function testUserDestroyFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->json('Delete', '/api/users/00000');
        $response
            ->assertStatus(404);
    }
}
