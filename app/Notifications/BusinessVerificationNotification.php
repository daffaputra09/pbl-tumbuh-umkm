<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessVerificationNotification extends Notification
{
    public function __construct(public Business $business) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verified = $this->business->verification_status === Business::STATUS_VERIFIED;
        $businessName = $this->business->business_name ?: 'Usaha kamu';
        $ownerName = $this->business->owner_name ?: '';
        $note = trim((string) $this->business->verification_note);
        $url = $verified
            ? route('umkm.dashboard')
            : route('umkm.profil');

        $data = [
            'verified' => $verified,
            'businessName' => $businessName,
            'ownerName' => $ownerName,
            'note' => $note,
            'url' => $url,
        ];

        $subject = $verified
            ? 'Data usaha '.$businessName.' sudah diverifikasi'
            : 'Data usaha '.$businessName.' perlu perbaikan';

        return (new MailMessage)
            ->from((string) config('mail.from.address'), 'Tumbuh UMKM')
            ->subject($subject)
            ->view('mail.business-verification', $data)
            ->text('mail.business-verification-text', $data);
    }
}
