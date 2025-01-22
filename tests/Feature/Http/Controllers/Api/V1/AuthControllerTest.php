<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase {
    use RefreshDatabase;

    public function test_login_returns_token_with_valid_credentials(): void {
        $user = $this->createRandomUser();

        $response = $this->postJson(route('auth.store'), [
            'email' => $user->email,
            'password' => 'Password123',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(['data' => ['accessToken']]);
    }

    public function test_login_returns_error_with_invalid_credentials(): void {
        $response = $this->postJson(route('auth.store'), [
            'email' => 'nonexisting@user.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
