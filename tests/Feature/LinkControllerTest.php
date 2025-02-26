<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

class LinkControllerTest extends TestCase
{
    use WithFaker;

    public function test_index()
    {
        $user = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($user);

        for ($i = 0; $i < 10; $i++) {
            Link::create([
                'url' => $this->faker->url,
                'user_id' => $user->id,
                'approved' => true,
            ]);
        }

        $response = $this->getJson('/api/links?per_page=5');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'current_page',
                     'total_pages',
                     'total_items'
                 ]);
    }


    public function test_approve()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $link = Link::create([
            'url' => $this->faker->url,
            'user_id' => $user->id,
            'approved' => false,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/links/{$link->id}/approve");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $link->id,
                     'approved' => true
                 ]);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'approved' => true
        ]);
    }

    public function test_destroy()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $link = Link::create([
            'url' => $this->faker->url,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/links/{$link->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Link excluído com sucesso.']);

        $this->assertDatabaseMissing('links', [
            'id' => $link->id
        ]);
    }

}