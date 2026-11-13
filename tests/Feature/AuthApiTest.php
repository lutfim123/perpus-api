<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_bisa_register_dan_mendapat_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['message', 'token', 'token_type', 'user' => ['id', 'name', 'email', 'created_at']])
            ->assertJsonPath('user.email', 'budi@example.com')
            ->assertJsonMissingPath('user.password');

        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
    }

    public function test_user_bisa_login_dengan_kredensial_benar(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonStructure(['token', 'user' => ['id', 'email']]);
    }

    public function test_login_gagal_jika_password_salah(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'salah-total',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_me_dan_logout_menghapus_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/me', $headers)
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);

        $this->postJson('/api/logout', [], $headers)
            ->assertOk()
            ->assertJsonPath('message', 'Logout berhasil');

        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Reset guard agar request berikutnya benar-benar memeriksa token lagi
        $this->app['auth']->forgetGuards();

        $this->getJson('/api/me', $headers)->assertUnauthorized();
    }
}
