<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;

class AuthControllerTest extends TestCase
{
    use WithFaker;

    public function test_register()
    {
        $email = $this->faker->unique()->safeEmail;

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'is_admin'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }

    public function test_login()
    {
        $email = $this->faker->unique()->safeEmail;

        $user = User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => Hash::make('password'),
            'is_admin' => false
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'is_admin'
                     ]
                 ]);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso.']);
    }

    public function test_check_token()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/check-token');

        $response->assertStatus(200)
                 ->assertJson(['valid' => true]);
    }
}