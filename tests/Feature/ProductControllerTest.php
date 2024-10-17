<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
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

    public function test_index_returns_products_with_successful_response()
    {
        Category::factory(5)->create();
        Product::factory(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'name', 'description', 'price', 'created_at', 'category'
                ]

            ]
        ]);
    }

    public function test_show_returns_product_with_successful_response()
    {
        Category::factory(5)->create();

        $product  = Product::factory()->create();
        $response = $this->getJson("/api/products/$product->id");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'created_at'  => $product->created_at,
            ]);
    }

    public function test_show_returns_product_with_unsuccessful_response()
    {
        Category::factory(5)->create();
        Product::factory()->create();
        $response = $this->getJson("/api/products/999");
        $response->assertStatus(404);
    }

    public function test_store_create_new_product_with_successful_response()
    {
        Category::factory(5)->create();
        $response = $this->postJson('/api/products', [
            'name'        => 'New Product name',
            'price'       => 123,
            'category_id' => Category::query()->first()->id
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name'  => 'New Product name',
                'price' => 123,
            ]);
    }

    public function test_store_create_new_product_with_unsuccessful_response()
    {

        $response = $this->postJson('/api/products', [
            'name' => '',
            'price' => '',
            'category_id' => ''
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_product_with_successful_response()
    {
        Category::factory(5)->create();
        $product  = Product::factory()->create();
        $response = $this->putJson("/api/products/$product->id", [
            'name' => 'New product name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'New product name',
            ]);
    }

    public function test_update_product_with_unsuccessful_response()
    {
        Category::factory(5)->create();
        $product = Product::factory()->create();

        $response = $this->putJson("/api/products/$product->id", [
            'price' => 'string',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_destroy_product_with_successful_response()
    {
        Category::factory(5)->create();
        $product  = Product::factory()->create();
        $response = $this->deleteJson("/api/products/$product->id");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_destroy_product_with_unsuccessful_response()
    {
        Category::factory(5)->create();
        Product::factory(5)->create();
        $response = $this->deleteJson("/api/products/999");
        $response->assertStatus(404);
    }
}
