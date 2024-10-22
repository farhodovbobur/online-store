<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_product_can_be_created()
    {
        $category = Category::factory()->create();

        $product = [
            'name' => 'New Product',
            'price' => 100,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'New Product',
            ]);

        $this->assertDatabaseHas('products', $product);
    }

    public function test_product_can_be_retrieved()
    {
        Category::factory()->create();
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $product->name]);
    }

    public function test_product_can_be_updated()
    {
        Category::factory()->create();
        $product = Product::factory()->create();

        $updatedData = [
            'name' => 'Updated Product',
            'price' => 150,
            'description' => 'Updated description.'
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Product']);

        $this->assertDatabaseHas('products', $updatedData);
    }

    public function test_product_can_be_deleted()
    {
        Category::factory()->create();
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_name_is_required()
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'price' => 100,
            'description' => 'This is a test product.'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_product_price_must_be_positive()
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'price' => -50,
            'description' => 'This is a test product.'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    public function test_product_belongs_to_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertEquals($category->id, $product->category_id);
    }
}
