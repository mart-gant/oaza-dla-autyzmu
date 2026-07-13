<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can register', function () {
    $response = $this->post(route('register'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role' => 'parent',
        'terms' => true,
    ]);
    
    $response->assertRedirect();
    
    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    
    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with incorrect password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);
    
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('suspended user cannot login', function () {
    $user = User::factory()->create([
        'email' => 'suspended@example.com',
        'password' => Hash::make('password'),
        'is_suspended' => true,
    ]);
    
    $response = $this->post(route('login'), [
        'email' => 'suspended@example.com',
        'password' => 'password',
    ]);
    
    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('user can logout', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user);
    
    $response = $this->post(route('logout'));
    
    $response->assertRedirect('/');
    $this->assertGuest();
});

test('verified middleware redirects unverified users', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertRedirect(route('verification.notice'));
});

test('verified user can access protected routes', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertStatus(200);
});

test('user can view profile edit page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('profile.edit'));
    
    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertSee($user->email);
});

test('user can update profile information', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);
    
    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);
    
    $response->assertRedirect(route('profile.edit'));
    
    $user->refresh();
    
    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('new@example.com');
});

test('user can delete their account', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);
    
    $response = $this->actingAs($user)->delete(route('profile.destroy'), [
        'password' => 'password',
    ]);
    
    $response->assertRedirect('/');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('email must be unique', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    
    $response = $this->post(route('register'), [
        'name' => 'John Doe',
        'email' => 'taken@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('password must be confirmed', function () {
    $response = $this->post(route('register'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'differentpassword',
    ]);
    
    $response->assertSessionHasErrors('password');
});
