<?php

use App\Mail\ContactMail;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

$registration = [
    'name' => 'Test User',
    'email' => 'new@example.com',
    'password' => 'Password1!',
    'password_confirmation' => 'Password1!',
    'role' => 'parent',
    'terms' => true,
];

test('resend mailer uses the HTTPS API transport', function () {
    // Resend wysyła przez API po HTTPS, więc blokada portów SMTP na serwerze go nie dotyczy
    config(['services.resend.key' => 're_test_key']);

    expect((string) Mail::mailer('resend')->getSymfonyTransport())->toBe('resend');
});

test('contact form sends mail to the configured address', function () {
    Mail::fake();
    config(['mail.contact.to.address' => 'kontakt@oaza.test']);

    $this->post('/contact', [
        'name' => 'Anna',
        'email' => 'anna@example.com',
        'message' => 'Dzień dobry',
    ])->assertRedirect(route('contact'))->assertSessionHas('success');

    Mail::assertSent(ContactMail::class, fn ($mail) => $mail->hasTo('kontakt@oaza.test') && $mail->hasReplyTo('anna@example.com'));
});

test('registration sends a verification email', function () use ($registration) {
    Notification::fake();

    $this->post('/register', $registration)->assertRedirect(route('dashboard', absolute: false));

    Notification::assertSentTo(User::where('email', 'new@example.com')->first(), VerifyEmail::class);
});

test('password reset shows an error instead of 500 when the mail server is down', function () {
    $user = User::factory()->create();
    config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1, 'mail.mailers.smtp.timeout' => 1]);

    $this->from('/forgot-password')->post('/forgot-password', ['email' => $user->email])
        ->assertRedirect('/forgot-password')
        ->assertSessionHasErrors('email');
});

test('registration still works when the mail server is down', function () use ($registration) {
    config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1, 'mail.mailers.smtp.timeout' => 1]);

    $this->post('/register', $registration)->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    expect(User::where('email', 'new@example.com')->exists())->toBeTrue();
});
