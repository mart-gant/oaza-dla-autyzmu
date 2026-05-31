<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ResendMailService;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Mockery;

class ResendMailTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_resend_mail_service_sends_html_email_via_sdk(): void
    {
        $service = new ResendMailService();

        // Create a mock for the Resend Emails service
        $mockEmails = Mockery::mock(\Resend\Service\Email::class);
        $mockEmails->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return $payload['to'] === 'test@example.com' &&
                       $payload['subject'] === 'Test HTML Email' &&
                       $payload['html'] === '<h1>Hello</h1>' &&
                       str_contains($payload['from'], config('mail.from.address'));
            }))
            ->andReturn(Mockery::mock(\Resend\Email::class));

        // Bind a dummy client to 'resend' in the container
        $mockClient = new \stdClass();
        $mockClient->emails = $mockEmails;
        $this->app->instance('resend', $mockClient);

        $result = $service->sendHtml('test@example.com', 'Test HTML Email', '<h1>Hello</h1>');

        $this->assertTrue($result);
    }

    public function test_resend_mail_service_sends_mailable_via_laravel_mail(): void
    {
        Mail::fake();

        $service = new ResendMailService();
        $mailable = new ContactMail([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
        ]);

        $result = $service->sendMailable('test@example.com', $mailable);

        $this->assertTrue($result);

        Mail::assertSent(ContactMail::class, function ($mail) {
            return $mail->hasTo('test@example.com') &&
                   $mail->data['name'] === 'John Doe';
        });
    }

    public function test_resend_artisan_command_sends_html_successfully(): void
    {
        // Mock the Resend client in the container
        $mockEmails = Mockery::mock(\Resend\Service\Email::class);
        $mockEmails->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($payload) {
                return $payload['to'] === 'test@example.com' &&
                       $payload['subject'] === 'Test wysyłki HTML - Resend SDK';
            }))
            ->andReturn(Mockery::mock(\Resend\Email::class));

        $mockClient = new \stdClass();
        $mockClient->emails = $mockEmails;
        $this->app->instance('resend', $mockClient);

        $this->artisan('mail:test-resend test@example.com --method=html')
            ->expectsOutput('Przygotowanie do wysłania wiadomości testowej do: test@example.com')
            ->expectsOutput('Używana metoda: html')
            ->expectsOutput('Wysyłanie...')
            ->expectsOutput('Sukces! Wiadomość testowa została pomyślnie wysłana.')
            ->assertExitCode(0);
    }

    public function test_resend_artisan_command_sends_mailable_successfully(): void
    {
        Mail::fake();

        $this->artisan('mail:test-resend test@example.com --method=mailable')
            ->expectsOutput('Przygotowanie do wysłania wiadomości testowej do: test@example.com')
            ->expectsOutput('Używana metoda: mailable')
            ->expectsOutput('Wysyłanie...')
            ->expectsOutput('Sukces! Wiadomość testowa została pomyślnie wysłana.')
            ->assertExitCode(0);

        Mail::assertSent(ContactMail::class);
    }
}
