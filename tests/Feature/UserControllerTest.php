<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_index_returns_users_with_successful_response()
    {
        User::factory(5)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at'
                    ]
                ]
            ]);
    }

    public function test_show_returns_user_with_successful_response()
    {
        $user     = User::factory()->create();
        $response = $this->getJson("/api/users/$user->id");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'created_at' => $user->created_at,
                ]
            ]);
    }

    public function test_show_returns_user_with_unsuccessful_response()
    {
        User::factory()->create();
        $response = $this->getJson("/api/categories/999");
        $response->assertStatus(404);
    }

    public function test_store_create_new_user_with_successful_response()
    {
        $response = $this->postJson('/api/users', [
            'name'     => 'New User name',
            'email'    => 'new@user.com',
            'password' => 'New User password',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name'  => 'New User name',
                'email' => 'new@user.com',
            ]);
    }

    public function test_store_create_new_user_with_unsuccessful_response()
    {

        $response = $this->postJson('/api/categories', [
            'name'     => '',
            'email'    => '',
            'password' => ''
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_user_with_successful_response()
    {
        $user = User::factory()->create();
        $response = $this->putJson("/api/users/$user->id", [
            'name' => 'New Category name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'New Category name',
            ]);
    }

    public function test_update_user_with_unsuccessful_response()
    {
        User::factory(5)->create();
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/$user->id", [
            'email' => $user->email,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_destroy_user_with_successful_response()
    {
        $user = User::factory()->create();
        $response = $this->deleteJson("/api/users/$user->id");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_destroy_user_with_unsuccessful_response()
    {
        User::factory(5)->create();
        $response = $this->deleteJson("/api/users/999");
        $response->assertStatus(404);
    }
}
