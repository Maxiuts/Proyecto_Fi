<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('shop', absolute: false));
});

test('admin email gets admin role on login', function () {
    config(['app.admin_email' => 'admin@test.com']);

    $user = User::factory()->create(['email' => 'admin@test.com', 'role' => 'user']);

    $this->post('/login', [
        'email' => 'admin@test.com',
        'password' => 'password',
    ]);

    expect($user->fresh()->role)->toBe('admin');
});

test('non-admin email gets user role on login', function () {
    config(['app.admin_email' => 'admin@test.com']);

    $user = User::factory()->create(['email' => 'otro@test.com', 'role' => 'admin']);

    $this->post('/login', [
        'email' => 'otro@test.com',
        'password' => 'password',
    ]);

    expect($user->fresh()->role)->toBe('user');
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('admin email gets admin role on oauth callback', function () {
    config(['app.admin_email' => 'admin@test.com']);

    $user = User::factory()->create([
        'email' => 'admin@test.com',
        'role' => 'user',
        'email_verified_at' => now(),
    ]);

    $socialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);
    $socialUser->shouldReceive('getEmail')->andReturn('admin@test.com');
    $socialUser->shouldReceive('getName')->andReturn('Admin');
    $socialUser->shouldReceive('getId')->andReturn('123');
    $socialUser->shouldReceive('getAvatar')->andReturn(null);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver->user')
        ->andReturn($socialUser);

    $this->get('/auth/google/callback');

    expect($user->fresh()->role)->toBe('admin');
});
