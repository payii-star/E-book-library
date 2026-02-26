<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = env('FRONTEND_URL') . '/reset-password?token=' . $this->token . '&email=' . $notifiable->email;

        return (new MailMessage)
            ->subject('Reset Password - laravel')
            ->greeting('Halo!')
            ->line('Kamu menerima email ini karena ada permintaan reset password untuk akun kamu.')
            ->action('Reset Password', $url)
            ->line('Link ini akan expired dalam 60 menit.')
            ->line('Jika kamu tidak merasa meminta reset password, abaikan email ini.');
    }
}