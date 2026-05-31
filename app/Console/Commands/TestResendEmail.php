<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ResendMailService;
use App\Mail\ContactMail;

class TestResendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-resend {email : Adres e-mail odbiorcy} {--method=html : Metoda wysyłki: "html" (bezpośrednio przez API Resend) lub "mailable" (przez Laravel Mailer)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wysyła testową wiadomość e-mail przez usługę Resend';

    /**
     * Execute the console command.
     */
    public function handle(ResendMailService $resendMailService): int
    {
        $recipient = $this->argument('email');
        $method = $this->option('method');

        $this->info("Przygotowanie do wysłania wiadomości testowej do: {$recipient}");
        $this->info("Używana metoda: {$method}");

        if ($method === 'html') {
            $subject = 'Test wysyłki HTML - Resend SDK';
            $html = '<h1>Wiadomość testowa Resend</h1><p>Ta wiadomość została wysłana bezpośrednio przez <strong>API SDK Resend</strong>.</p>';
            
            $this->comment('Wysyłanie...');
            $result = $resendMailService->sendHtml($recipient, $subject, $html);
        } elseif ($method === 'mailable') {
            $data = [
                'name' => 'Tester Resend',
                'email' => 'test-sender@example.com',
                'message' => 'To jest testowa wiadomość wygenerowana przez komendę Artisan w celu przetestowania integracji Laravel Mailer z Resend.',
            ];
            $mailable = new ContactMail($data);

            $this->comment('Wysyłanie...');
            $result = $resendMailService->sendMailable($recipient, $mailable);
        } else {
            $this->error("Niepoprawna metoda. Dopuszczalne wartości to: 'html' lub 'mailable'.");
            return self::FAILURE;
        }

        if ($result) {
            $this->info('Sukces! Wiadomość testowa została pomyślnie wysłana.');
            return self::SUCCESS;
        } else {
            $this->error('Błąd! Wysyłanie wiadomości nie powiodło się. Sprawdź logi w storage/logs/laravel.log w celu uzyskania szczegółów.');
            return self::FAILURE;
        }
    }
}
