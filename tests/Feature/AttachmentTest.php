<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use WithFaker;

    public function testAttachmentShowSuccess(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/attachments');
        $response
            ->assertOk();
    }

    public function testAttachmentShowFailUnauth(): void
    {
        $response = $this->getJson('/api/attachments');
        $response
            ->assertStatus(401);
    }

    public function testAttachmentShowFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->getJson('/api/attachment');
        $response
            ->assertStatus(404);
    }

    public function testAttachmentByIdShowSuccess(): void
    {
        $attachment = Attachment::factory()->create();
        $this->assertDatabaseHas(Attachment::class, [
            'id' => $attachment->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/attachments/' . $attachment->id);
        $response
            ->assertOk();
    }

    public function testAttachmentByIdShowFailUnauth(): void
    {
        $attachment = Attachment::factory()->create();
        $this->assertDatabaseHas(Attachment::class, [
            'id' => $attachment->id
        ]);
        $response = $this->getJson('/api/attachments/' . $attachment->id);
        $response
            ->assertStatus(401);
    }

    public function testAttachmentByIdShowFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/attachments/10000');
        $response
            ->assertStatus(404);
    }

    public function testAttachmentByIdShowFailBadContent(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/attachments/wef');
        $response
            ->assertStatus(500);
    }

    public function testAttachmentDestroySuccess(): void
    {
        $attachment = Attachment::factory()->create();
        $this->assertDatabaseHas(Attachment::class, [
            'id' => $attachment->id
        ]);
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->json('Delete', '/api/attachments/' . $attachment->id);
        $response
            ->assertStatus(204);
        $this->assertDatabaseMissing(Attachment::class, [
            'id' => $attachment->id
        ]);
    }

    public function testAttachmentDestroyFailUnauth(): void
    {
        $attachment = Attachment::factory()->create();
        $this->assertDatabaseHas(Attachment::class, [
            'id' => $attachment->id
        ]);
        $response = $this->json('Delete', '/api/attachments/' . $attachment->id);
        $response
            ->assertStatus(401);
        $this->assertDatabaseHas(Attachment::class, [
            'id' => $attachment->id
        ]);
    }

    public function testAttachmentDestroyFailNotFound(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->json('Delete', '/api/attachments/00000');
        $response
            ->assertStatus(404);
    }
}
