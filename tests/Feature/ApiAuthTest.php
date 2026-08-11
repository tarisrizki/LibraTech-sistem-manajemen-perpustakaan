<?php

declare(strict_types=1);

use App\Models\User;

it('api login returns token for valid credentials', function (): void {
    $user = User::factory()->create(['password' => 'password123']);

    $response = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password123']);

    $response->assertOk()->assertJsonStructure(['data' => ['token', 'user']]);
});

it('api me returns user when authenticated', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.email', $user->email);
});

it('api loans require authentication', function (): void {
    $this->getJson('/api/v1/loans')->assertUnauthorized();
});
