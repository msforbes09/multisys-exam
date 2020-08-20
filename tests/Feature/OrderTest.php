<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test*/
    public function a_user_can_order_a_product()
    {
        \Artisan::call('passport:install');
        factory(Product::class, 20)->create();

        $credentials = $this->createCredentials();
        $this->registerUser($credentials);
        $token = $this->loginUser($credentials)->json('access_token');
        $product = $this->getProduct();

        $response = $this
            ->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])
            ->post('/api/order', [
                'product_id' => $product->id,
                'quantity' => 1
            ]);

        $this->assertEquals(Product::find($product->id)->stock, ($product->stock - 1));

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'You have successfully ordered this product.'
            ]);
    }

    /** @test*/
    public function a_user_cant_order_more_than_stock_quantity_of_a_product()
    {
        \Artisan::call('passport:install');
        factory(Product::class, 20)->create();

        $credentials = $this->createCredentials();
        $this->registerUser($credentials);
        $token = $this->loginUser($credentials)->json('access_token');
        $product = $this->getProduct();

        $response = $this
            ->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])
            ->post('/api/order', [
                'product_id' => $product->id,
                'quantity' => 9999
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Failed to order this product due to unavailability of the stock.'
            ]);
    }

    protected function createCredentials()
    {
        return [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];
    }

    protected function registerUser($credentials)
    {
        return $this->post('/api/register', $credentials);
    }

    protected function loginUser($credentials)
    {
        return $this->post('/api/login', $credentials);
    }

    protected function getProduct()
    {
        return Product::get()->random();
    }
}
