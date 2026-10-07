<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class GmailApiTransport extends AbstractTransport
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $refreshToken,
    ) {
        parent::__construct();
    }

    public function __toString(): string
    {
        return 'gmail-api';
    }

    protected function doSend(SentMessage $message): void
    {
        $accessToken = $this->accessToken();
        $raw = rtrim(strtr(base64_encode($message->getOriginalMessage()->toString()), '+/', '-_'), '=');

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(15)
            ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $raw,
            ]);

        if ($response->failed()) {
            throw new TransportException('Gmail API menolak email: '.$response->body());
        }

        $messageId = $response->json('id');

        if (is_string($messageId) && $messageId !== '') {
            $message->setMessageId($messageId);
        }
    }

    private function accessToken(): string
    {
        if ($this->clientId === '' || $this->clientSecret === '' || $this->refreshToken === '') {
            throw new TransportException('Gmail API belum lengkap. Jalankan php artisan gmail:authorize.');
        }

        $response = Http::asForm()
            ->acceptJson()
            ->timeout(15)
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type' => 'refresh_token',
            ]);

        $accessToken = $response->json('access_token');

        if ($response->failed() || ! is_string($accessToken) || $accessToken === '') {
            throw new TransportException('Token Gmail API gagal diperbarui: '.$response->body());
        }

        return $accessToken;
    }
}
