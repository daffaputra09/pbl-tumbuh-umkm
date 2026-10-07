<?php

namespace Tests\Feature;

use App\Mail\Transport\GmailApiTransport;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class GmailApiTransportTest extends TestCase
{
    public function test_it_sends_mail_through_the_gmail_https_api(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'access-token']),
            'https://gmail.googleapis.com/gmail/v1/users/me/messages/send' => Http::response(['id' => 'msg-1']),
        ]);

        $email = (new Email)
            ->from('mail.notification.app.service@gmail.com')
            ->to('siti@example.com')
            ->subject('Atur ulang kata sandi')
            ->text('Halo Siti');

        $sent = (new GmailApiTransport('client-id', 'client-secret', 'refresh-token'))->send($email);

        $this->assertNotNull($sent);
        $this->assertSame('msg-1', $sent->getMessageId());

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://oauth2.googleapis.com/token'
                && $request['grant_type'] === 'refresh_token'
                && $request['refresh_token'] === 'refresh-token';
        });

        Http::assertSent(function ($request): bool {
            $raw = $request['raw'] ?? '';

            if (! is_string($raw) || $raw === '') {
                return false;
            }

            $decoded = base64_decode(strtr($raw, '-_', '+/'), true);

            return $request->url() === 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send'
                && $request->hasHeader('Authorization', 'Bearer access-token')
                && is_string($decoded)
                && str_contains($decoded, 'siti@example.com')
                && str_contains($decoded, 'Halo Siti');
        });
    }

    public function test_it_refuses_to_send_without_a_refresh_token(): void
    {
        Http::preventStrayRequests();

        $email = (new Email)
            ->from('mail.notification.app.service@gmail.com')
            ->to('siti@example.com')
            ->subject('Atur ulang kata sandi')
            ->text('Halo');

        $this->expectException(TransportException::class);

        (new GmailApiTransport('client-id', 'client-secret', ''))->send($email);
    }
}
