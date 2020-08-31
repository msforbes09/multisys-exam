<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test*/
    public function a_user_can_register()
    {
        $user = factory(User::class)->raw();

        $this->post('/api/register', $user)
            ->assertStatus(201)
            ->assertJson([
                'message' => 'User successfully registered.',
            ]);

        $this->assertDataBaseHas('users', ['email' => $user['email']]);
    }

    /** @test*/
    public function an_email_wont_registered_twice()
    {
        $user = factory(User::class)->raw();

        $this->post('/api/register', $user);

        $this->post('/api/register', $user)
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Email already taken.',
            ]);
    }
}
