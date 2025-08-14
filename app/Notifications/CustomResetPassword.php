<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = rtrim(env('FRONTEND_URL'), '/') 
             . '/reset-password?token=' . urlencode($this->token) 
             . '&email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Reset Your Password - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We received a request to reset your password for your ' . config('app.name') . ' account.')
            ->action('Reset Password', $url)
            ->line('If you did not request this password reset, you can ignore this email.')
            ->salutation('Regards, ' . config('app.name'));
    }
}
