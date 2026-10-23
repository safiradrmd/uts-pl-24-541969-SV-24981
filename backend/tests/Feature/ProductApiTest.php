<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Menolak akses posting produk tanpa token (401 Unauthorized)
     */
    public function test_cannot_create_product_without_authentication(): void
    {
        $response = $this->postJson('/api/products', [
            'name'  => 'Produk Tanpa Token',
            'price' => 10000,
            'stock' => 10,
        ]);

        $response->assertStatus(401);
    }
    /**
     * Test 2: Berhasil menambahkan produk dengan token valid (201 Created)
     */
    public function test_can_create_product_with_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/products', [
                'name'        => 'Kopi Susu Tester',
                'description' => 'Varian Kopi Uji Coba',
                'price'       => 15000,
                'stock'       => 20,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data'   => [
                    'name'  => 'Kopi Susu Tester',
                    'price' => 15000,
                    'stock' => 20,
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Susu Tester',
        ]);
    }
}