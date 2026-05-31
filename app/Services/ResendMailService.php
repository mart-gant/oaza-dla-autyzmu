<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;
use Illuminate\Mail\Mailable;

class ResendMailService
{
    /**
     * Wysyła e-mail jako HTML bezpośrednio przez API Resend (SDK).
     *
     * @param string|array $to Adresat lub tablica adresatów
     * @param string $subject Temat wiadomości
     * @param string $htmlContent Treść HTML wiadomości
     * @param array $options Dodatkowe opcje (np. from, reply_to, text, tags itp.)
     * @return bool True jeśli wysłano pomyślnie, false w przeciwnym wypadku
     */
    public function sendHtml(string|array $to, string $subject, string $htmlContent, array $options = []): bool
    {
        try {
            $fromAddress = $options['from'] ?? sprintf('%s <%s>', config('mail.from.name'), config('mail.from.address'));

            $payload = array_merge([
                'from' => $fromAddress,
                'to' => $to,
                'subject' => $subject,
                'html' => $htmlContent,
            ], array_intersect_key($options, array_flip(['reply_to', 'cc', 'bcc', 'text', 'tags', 'headers'])));

            Resend::emails()->send($payload);

            return true;
        } catch (\Throwable $exception) {
            Log::error('Resend API email delivery failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Wysyła e-mail przy użyciu standardowego systemu Mailable w Laravelu z transporterem Resend.
     *
     * @param string|array $to Adresat lub tablica adresatów
     * @param Mailable $mailable Instancja klasy Mailable
     * @return bool True jeśli wysłano pomyślnie, false w przeciwnym wypadku
     */
    public function sendMailable(string|array $to, Mailable $mailable): bool
    {
        try {
            Mail::mailer('resend')->to($to)->send($mailable);

            return true;
        } catch (\Throwable $exception) {
            Log::error('Laravel Mailer (Resend) delivery failed', [
                'to' => $to,
                'mailable' => get_class($mailable),
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
