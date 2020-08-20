<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test*/
    public function a_user_can_register()
    {
        $credentials = $this->createCredentials();

        $response = $this->post('/api/register', $credentials);

        $this->assertDataBaseHas('users', ['email' => $credentials['email']]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'User successfully registered.',
            ]);
    }

    /** @test*/
    public function an_emeail_wont_registered_twice()
    {
        $credentials = $this->createCredentials();

        $this->post('/api/register', $credentials);

        $response = $this->post('/api/register', $credentials);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Email already taken.',
            ]);
    }

    protected function createCredentials()
    {
        return [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];
    }
}
