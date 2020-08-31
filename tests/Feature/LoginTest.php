<?php

namespace Tests\Feature;

use App\User;
use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test*/
    public function a_user_can_login_with_right_credentials()
    {
        Artisan::call('passport:install');

        $user = factory(User::class)->create();

        $this->post('/api/login', [
                'email' => $user->email,
                'password' => 'password'
            ])
            ->assertStatus(201)
            ->assertJsonStructure(['access_token']);
    }

    /** @test*/
    public function a_user_cant_login_with_wrong_credentials()
    {
        $user = factory(User::class)->create();

        $this->post('/api/login', [
                'email' => $user->email,
                'password' => 'wrong_password'
            ])
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    /** @test*/
    public function a_user_wait_five_minutes_after_five_failed_login()
    {
        $user = factory(User::class)->create();

        for ($i = 1; $i <=5; $i++ ){
            $response = $this->post('/api/login', [
                    'email' => $user->email,
                    'password' => 'wrong_password'
                ]);
        }

        $response->assertStatus(429);
    }
}
