<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Message from ' . $this->message->sender->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a new message from ' . $this->message->sender->name . '.')
            ->action('View Message', route('messages.show', $this->message->conversation))
            ->line('Thank you for using our platform!');
    }
}
