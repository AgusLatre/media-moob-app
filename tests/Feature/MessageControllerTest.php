<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function a_guest_cannot_access_messages()
    {
        $response = $this->get(route('messages.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_can_view_their_messages()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Message::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('messages.index'));

        $response->assertStatus(200);
        $response->assertViewIs('messages.index');
    }

    /** @test */
    public function a_message_can_be_stored_with_attachment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('doc.pdf', 500, 'application/pdf');

        $data = [
            'platform' => 'Telegram',
            'recipients' => 'Juan,Pablo',
            'message' => 'Hola mundo',
            'attachment' => $file,
        ];

        $response = $this->post(route('messages.store'), $data);

        $response->assertRedirect(route('messages.index'));

        $this->assertDatabaseCount('messages', 1);

        $message = Message::first();

        $this->assertEquals('Telegram', $message->platform);
        $this->assertEquals(json_encode(['Juan', 'Pablo']), $message->recipients);
        $this->assertNotNull($message->attachment);
        Storage::disk('public')->assertExists($message->attachment);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('messages.store'), []);

        $response->assertSessionHasErrors(['platform', 'recipients', 'message']);
    }

    /** @test */
    public function it_fails_with_invalid_platform()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('messages.store'), [
            'platform' => 'InvalidPlatform',
            'recipients' => 'Alguien',
            'message' => 'Texto',
        ]);

        $response->assertSessionHasErrors(['platform']);
    }
}
