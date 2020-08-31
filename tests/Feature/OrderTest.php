<?php

namespace Tests\Feature;

use App\Product;
use App\User;
use Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        factory(Product::class, 20)->create();
    }

    /** @test*/
    public function a_user_can_order_a_product()
    {
        Passport::actingAs(factory(User::class)->create());

        $product = Product::get()->random();

        $this->post('/api/order', [
                'product_id' => $product->id,
                'quantity' => 1
            ])
            ->assertStatus(201)
            ->assertJson([
                'message' => 'You have successfully ordered this product.'
            ]);

        $this->assertEquals($product->fresh()->stock, ($product->stock - 1));
    }

    /** @test*/
    public function a_user_cant_order_more_than_stock_quantity_of_a_product()
    {
        Passport::actingAs(factory(User::class)->create());

        $product = Product::get()->random();

        $this->post('/api/order', [
                'product_id' => $product->id,
                'quantity' => 9999
            ])
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Failed to order this product due to unavailability of the stock.'
            ]);
    }
}
