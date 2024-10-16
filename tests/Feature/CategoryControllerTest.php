<?php

namespace Tests\Feature;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
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

    public function test_index_returns_categories_with_successful_response()
    {
        Category::factory(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'image',
                        'parent',
                        'created_at'
                    ]
                ]
            ]);
    }

    public function test_show_returns_category_with_successful_response()
    {
        $category = Category::factory()->create();
        $response = $this->getJson("/api/categories/$category->id");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'id'          => $category->id,
                    'name'        => $category->name,
                    'description' => $category->description,
                    'image'       => $category->image,
                    'parent'      => $category->parent,
                    'created_at'  => $category->created_at,
                ]
            ]);
    }

    public function test_show_returns_category_with_unsuccessful_response()
    {
        $category = Category::factory()->create();
        $response = $this->getJson("/api/categories/999");
        $response->assertStatus(404);
    }

    public function test_store_create_new_category_with_successful_response()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'New Category name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'New Category name',
            ]);
    }

    public function test_store_create_new_category_with_unsuccessful_response()
    {

        $response = $this->postJson('/api/categories', [
            'name' => '',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_category_with_successful_response()
    {
        $category = Category::factory()->create();
        $response = $this->putJson("/api/categories/$category->id", [
            'name' => 'New Category name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'New Category name',
            ]);
    }

    public function test_update_category_with_unsuccessful_response()
    {
        Category::factory(5)->create();
        $category = Category::factory()->create();

        $response = $this->putJson("/api/categories/$category->id", [
            'name' => $category->name,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_destroy_category_with_successful_response()
    {
        $category = Category::factory()->create();
        $response = $this->deleteJson("/api/categories/$category->id");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_destroy_category_with_unsuccessful_response()
    {
        Category::factory(5)->create();
        $response = $this->deleteJson("/api/categories/999");
        $response->assertStatus(404);
    }
}
