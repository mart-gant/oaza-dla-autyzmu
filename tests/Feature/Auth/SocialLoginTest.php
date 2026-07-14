<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_redirect_works(): void
    {
        $response = $this->get('/auth/google/redirect');

        // Check that it redirects to Google (accounts.google.com)
        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_social_callback_logs_in_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'social@example.com',
            'google_id' => '1234567890',
        ]);

        $mockUser = $this->createMockSocialiteUser('1234567890', 'social@example.com', 'Google User');

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->createMockSocialiteDriver($mockUser));

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_social_callback_links_existing_user_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'social@example.com',
            'google_id' => null,
        ]);

        $mockUser = $this->createMockSocialiteUser('1234567890', 'social@example.com', 'Google User');

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->createMockSocialiteDriver($mockUser));

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $this->assertEquals('1234567890', $user->fresh()->google_id);
        $response->assertRedirect('/dashboard');
    }

    public function test_social_callback_creates_new_user(): void
    {
        $mockUser = $this->createMockSocialiteUser('1234567890', 'new_social@example.com', 'New Google User');

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($this->createMockSocialiteDriver($mockUser));

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'email' => 'new_social@example.com',
            'google_id' => '1234567890',
            'name' => 'New Google User',
        ]);

        $user = User::where('email', 'new_social@example.com')->first();
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    private function createMockSocialiteUser($id, $email, $name)
    {
        $user = $this->getMockBuilder(SocialiteUser::class)->disableOriginalConstructor()->getMock();
        $user->method('getId')->willReturn($id);
        $user->method('getEmail')->willReturn($email);
        $user->method('getName')->willReturn($name);
        $user->method('getNickname')->willReturn(null);
        return $user;
    }

    private function createMockSocialiteDriver($mockUser)
    {
        $driver = $this->getMockBuilder(\Laravel\Socialite\Two\GoogleProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['user'])
            ->getMock();
        $driver->method('user')->willReturn($mockUser);
        return $driver;
    }
}
