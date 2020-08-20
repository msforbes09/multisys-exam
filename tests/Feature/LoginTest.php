<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test*/
    public function a_user_can_login_with_right_credentials()
    {
        \Artisan::call('passport:install');

        $credentials = $this->createCredentials();

        $this->registerUser($credentials);

        $response = $this->loginUser($credentials);

        $response->assertStatus(201);

        $this->assertArrayHasKey('access_token', $response);
    }

    /** @test*/
    public function a_user_cant_login_with_wrong_credentials()
    {
        \Artisan::call('passport:install');

        $credentials = $this->createCredentials();

        $this->registerUser($credentials);

        $response = $this->failedLoginUser($credentials);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);;
    }

    /** @test*/
    public function a_user_wait_five_minutes_after_five_failed_login()
    {
        \Artisan::call('passport:install');

        $credentials = $this->createCredentials();

        $this->registerUser($credentials);

        $this->failedLoginUser($credentials);
        $this->failedLoginUser($credentials);
        $this->failedLoginUser($credentials);
        $this->failedLoginUser($credentials);
        $response = $this->failedLoginUser($credentials);

         $response
            ->assertStatus(429);
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

    protected function failedLoginUser($credentials)
    {
        return $this->post('/api/login', [
            'email' => $credentials['email'],
            'password' => 'wrong_password',
        ]);
    }
}
