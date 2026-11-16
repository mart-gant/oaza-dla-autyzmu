<?php

use App\Models\Facility;
use App\Models\User;

test('facility owner cannot verify their own facility', function () {
    $owner = User::factory()->create();
    $facility = Facility::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner)->put(route('facilities.update', $facility), [
        'name' => $facility->name,
        'address' => $facility->address,
        'city' => $facility->city,
        'postal_code' => $facility->postal_code,
        'province' => $facility->province ?? 'Mazowieckie',
        'phone' => $facility->phone ?? '123456789',
        'email' => $facility->email ?? 'a@b.pl',
        'verification_status' => 'certified',
        'verified_by' => $owner->id,
    ])->assertRedirect();

    expect($facility->fresh()->verification_status)->not->toBe('certified')
        ->and($facility->fresh()->verified_by)->toBeNull();
});

test('api login is refused when two-factor auth is enabled', function () {
    $user = User::factory()->create(['password' => 'Secret-Pass-123!']);
    $user->forceFill([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'Secret-Pass-123!',
    ])->assertForbidden()->assertJsonMissing(['access_token']);
});

test('suspended user loses an already open session', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/dashboard')->assertOk();

    $user->forceFill(['is_suspended' => true])->save();

    $this->get('/dashboard')->assertRedirect(route('login'));
    $this->assertGuest();
});
