<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => UserSeeder::class, '--force' => true]);
    }

    public function test_login_success_returns_expected_response_shape(): void
    {
        $email = 'admin@mileapp.com';
        $password = '12345678';

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('response_code', 200)
            ->assertJsonPath('response_status', 'successfully-login')
            ->assertJsonPath('message', 'Successfully Action!')
            ->assertJsonStructure([
                'response_code',
                'response_status',
                'message',
                'data' => [
                    'authorization' => [
                        'type', 'expires_in', 'token',
                    ],
                    'user' => [
                        'name', 'email',
                    ],
                ],
                'redirect',
            ])
            ->assertJsonPath('data.authorization.type', 'Bearer')
            ->assertJsonPath('data.user.email', $email);

        $this->assertIsInt($response->json('data.authorization.expires_in'));
        $this->assertIsString($response->json('data.authorization.token'));
        $this->assertNotEmpty($response->json('data.authorization.token'));
    }

    public function test_login_with_wrong_credentials_returns_authentication_error(): void
    {
        $email = 'admin@mileapp.com';
        $password = 'wrongpassword';

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('response_code', 401)
            ->assertJsonPath('response_status', 'failed-authentication')
            ->assertJsonStructure([
                'response_code',
                'response_status',
                'message',
                'errors',
            ]);
    }

    public function test_login_validation_errors_return_failed_validation(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonPath('response_code', 422)
            ->assertJsonPath('response_status', 'failed-validation')
            ->assertJsonStructure([
                'response_code',
                'response_status',
                'message',
                'errors' => [
                    'email',
                    'password',
                ],
            ]);
    }
}
