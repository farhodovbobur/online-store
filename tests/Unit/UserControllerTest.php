<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_user_can_be_created()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }

    public function test_user_can_be_retrieved()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_user_can_be_updated()
    {
        $user = User::factory()->create();

        $updatedData = [
            'name' => 'Jane Doe',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Jane Doe',
            ]);

        $this->assertDatabaseHas('users', $updatedData);
    }

    public function test_user_can_be_deleted()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_email_is_unique()
    {
        $user = User::factory()->create([
            'email' => 'unique@example.com'
        ]);

        $response = $this->postJson('/api/users', [
            'name' => 'John Doe',
            'email' => 'unique@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_user_password_is_required()
    {
        $response = $this->postJson('/api/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }
}
