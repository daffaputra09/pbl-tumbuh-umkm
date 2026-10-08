<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use SensitiveParameter;

class ResetPasswordNotification extends Notification
{
    public function __construct(#[SensitiveParameter] public string $token) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $email = $notifiable->getEmailForPasswordReset();
        $name = is_string($notifiable->name ?? null) ? trim($notifiable->name) : '';
        $expireMinutes = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        $data = [
            'url' => url(route('password.reset', [
                'token' => $this->token,
                'email' => $email,
            ], false)),
            'name' => $name,
            'expireMinutes' => $expireMinutes,
        ];

        return (new MailMessage)
            ->from((string) config('mail.from.address'), 'Tumbuh UMKM')
            ->subject('Atur ulang kata sandi Tumbuh UMKM')
            ->view('mail.reset-password', $data)
            ->text('mail.reset-password-text', $data);
    }
}
