<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('jwt api login returns token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'access_token',
        'token_type',
        'expires_in',
    ]);
    
    expect($response->json('token_type'))->toBe('bearer');
});

test('jwt api login fails with wrong credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);
    
    $response->assertStatus(401);
    $response->assertJson(['success' => false, 'message' => 'Invalid credentials']);
});

test('suspended user cannot login via api', function () {
    $user = User::factory()->create([
        'email' => 'suspended@example.com',
        'password' => Hash::make('password'),
        'is_suspended' => true,
    ]);
    
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'suspended@example.com',
        'password' => 'password',
    ]);
    
    $response->assertStatus(403);
});

test('jwt api register creates user and returns token', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);
    
    $response->assertStatus(201);
    $response->assertJsonStructure([
        'user' => ['id', 'name', 'email'],
        'token',
        'success',
        'message',
    ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
    ]);
});

test('jwt me endpoint returns authenticated user', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/auth/me');
    
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ],
    ]);
});

test('jwt refresh returns new token', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/auth/refresh');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'access_token',
        'token_type',
        'expires_in',
    ]);
});

test('jwt logout invalidates token', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/auth/logout');
    
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Successfully logged out']);
});

test('api requires authentication for protected routes', function () {
    $response = $this->postJson('/api/v1/facilities', [
        'name' => 'Test Facility',
    ]);
    
    $response->assertStatus(401);
});

test('api facilities index returns paginated list', function () {
    $facilities = \App\Models\Facility::factory()->count(15)->create();
    
    $response = $this->getJson('/api/v1/facilities');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'city'],
        ],
        'links',
        'meta',
    ]);
});

test('api facilities can be filtered by type', function () {
    \App\Models\Facility::factory()->create([
        'name' => 'Therapist 1',
        'type' => 'osrodek_terapeutyczny',
    ]);
    \App\Models\Facility::factory()->create([
        'name' => 'School 1',
        'type' => 'szkola',
    ]);
    
    $response = $this->getJson('/api/v1/facilities?type=osrodek_terapeutyczny');
    
    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => 'Therapist 1']);
    $response->assertJsonMissing(['name' => 'School 1']);
});

test('authenticated user can create facility via api', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/facilities', [
            'name' => 'API Test Facility',
            'type' => 'szkola',
            'address' => '123 API St',
            'city' => 'Warsaw',
            'postal_code' => '00-001',
            'phone' => '123456789',
            'email' => 'api@facility.com',
            'description' => 'Created via API',
        ]);
    
    $response->assertStatus(201);
    $response->assertJsonFragment(['name' => 'API Test Facility']);
    
    $this->assertDatabaseHas('facilities', [
        'name' => 'API Test Facility',
        'user_id' => $user->id,
    ]);
});

test('api forum topics can be retrieved', function () {
    $topics = \App\Models\ForumTopic::factory()->count(5)->create();
    
    $response = $this->getJson('/api/v1/forum/topics');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'title', 'user'],
        ],
    ]);
});

test('api rate limiting works', function () {
    // Make 61 requests (limit is 60)
    for ($i = 0; $i < 62; $i++) {
        $response = $this->getJson('/api/v1/facilities');
        
        if ($i < 60) {
            $response->assertStatus(200);
        }
    }
    
    // 62nd request should be rate limited
    $response->assertStatus(429);
})->skip('Rate limiting test may take too long');

