<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    public function testLoginSuccess(): void
    {
        $response = $this->postJson('/api/auth/signin', [
            'email' => 'navwie@gmail.com',
            'password' => '123456789',
        ]);
        $response
            ->assertOk()
            ->assertSee('user')
            ->assertSee('token')
            ->assertJson([
                'message' => 'Signed in successfully.',
            ]);
    }

    public function testLoginFail(): void
    {
        $response = $this->postJson('/api/auth/signin', [
            'email' => 'navwie@gmail.com',
            'password' => '1234444489',
        ]);
        $response
            ->assertStatus(409)
            ->assertJson([
                'message' => 'Invalid login credentials.',
            ])
            ->assertDontSee('user')
            ->assertDontSee('token');
    }

    public function testRegisterSuccess(): void
    {
        $user = [
            'username' => $this->faker->userName(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '11111111',
            'password_confirmation' => '11111111',
            'isEmailPublic' => $this->faker->boolean(30),
            'picture' => $this->faker->imageUrl(),
            'bio' => $this->faker->text(200),
            'color' => $this->faker->hexColor(),
        ];
        $response = $this->postJson('/api/auth/signup', $user);
        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Signed up successfully.',
            ])
            ->assertSee('user')
            ->assertSee('token');
    }

    public function testRegisterFail(): void
    {
        $user = [
            'username' => '',
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'isEmailPublic' => '',
            'picture' => '',
            'bio' => '',
            'color' => '',
        ];
        $response = $this->postJson('/api/auth/signup', $user);
        $response
            ->assertStatus(422)
            ->assertDontSee('token');
    }

    public function testLogoutSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->postJson('/api/auth/signout');
        $response
            ->assertStatus(204);
    }

    public function testLogoutFail(): void
    {
        $response = $this->postJson('/api/auth/signout');
        $response
            ->assertStatus(401);
    }

    public function testMeSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/auth/me');
        $response
            ->assertOk()
            ->assertDontSee('token');
    }

    public function testRevoke(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->postJson('/api/auth/revoke');
        $response
            ->assertStatus(204);
    }
}
