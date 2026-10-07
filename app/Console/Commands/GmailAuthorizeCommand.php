<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

#[Signature('gmail:authorize')]
#[Description('Buka izin Gmail API sekali dan simpan refresh token ke .env')]
class GmailAuthorizeCommand extends Command
{
    public function handle(): int
    {
        $clientId = (string) config('services.gmail_api.client_id');
        $clientSecret = (string) config('services.gmail_api.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            $this->error('Isi GMAIL_CLIENT_ID dan GMAIL_CLIENT_SECRET di .env.');

            return self::FAILURE;
        }

        $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);

        if ($server === false) {
            $this->error('Server lokal untuk izin Gmail gagal dibuka: '.$error);

            return self::FAILURE;
        }

        $address = stream_socket_get_name($server, false);
        $port = (int) substr((string) $address, (int) strrpos((string) $address, ':') + 1);
        $redirect = 'http://localhost:'.$port;
        $state = Str::random(40);

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirect,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/gmail.send',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);

        $this->line('Buka tautan ini dengan akun mail.notification.app.service@gmail.com:');
        $this->line($url);

        $connection = stream_socket_accept($server, 600);

        if ($connection === false) {
            fclose($server);
            $this->error('Izin belum diterima. Jalankan perintah ini lagi.');

            return self::FAILURE;
        }

        $request = $this->readRequest($connection);
        $this->respond($connection, 'Izin Gmail sudah diterima. Tab ini boleh ditutup.');
        fclose($connection);
        fclose($server);

        $query = $this->queryFromRequest($request);

        if (($query['state'] ?? '') !== $state) {
            $this->error('Balasan Google tidak cocok. Jalankan perintah ini lagi.');

            return self::FAILURE;
        }

        $code = $query['code'] ?? '';

        if (! is_string($code) || $code === '') {
            $this->error('Google tidak mengembalikan kode izin: '.($query['error'] ?? 'tidak diketahui'));

            return self::FAILURE;
        }

        $token = Http::asForm()
            ->acceptJson()
            ->timeout(15)
            ->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirect,
                'grant_type' => 'authorization_code',
            ]);

        $refreshToken = $token->json('refresh_token');

        if ($token->failed() || ! is_string($refreshToken) || $refreshToken === '') {
            $this->error('Refresh token tidak diterima: '.$token->body());

            return self::FAILURE;
        }

        $this->storeRefreshToken($refreshToken);
        $this->info('Refresh token tersimpan di .env. Email reset kini lewat Gmail API.');

        return self::SUCCESS;
    }

    private function readRequest($connection): string
    {
        $raw = '';

        while (! str_contains($raw, "\r\n\r\n") && strlen($raw) < 8192) {
            $chunk = fread($connection, 1024);

            if (! is_string($chunk) || $chunk === '') {
                break;
            }

            $raw .= $chunk;
        }

        return $raw;
    }

    /**
     * @return array<string, mixed>
     */
    private function queryFromRequest(string $request): array
    {
        $line = strtok($request, "\r\n") ?: '';

        if (! preg_match('/^GET\s+(\S+)/', $line, $matches)) {
            return [];
        }

        $query = parse_url($matches[1], PHP_URL_QUERY);

        if (! is_string($query)) {
            return [];
        }

        parse_str($query, $parameters);

        return $parameters;
    }

    private function respond($connection, string $message): void
    {
        $body = '<!DOCTYPE html><html lang="id"><body><p>'.e($message).'</p></body></html>';
        $response = "HTTP/1.1 200 OK\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Length: ".strlen($body)."\r\nConnection: close\r\n\r\n".$body;
        fwrite($connection, $response);
    }

    private function storeRefreshToken(string $refreshToken): void
    {
        $path = app()->environmentFilePath();
        $contents = file_get_contents($path);

        if (! is_string($contents)) {
            return;
        }

        $line = 'GMAIL_REFRESH_TOKEN='.$refreshToken;

        if (preg_match('/^GMAIL_REFRESH_TOKEN=.*$/m', $contents) === 1) {
            $contents = preg_replace('/^GMAIL_REFRESH_TOKEN=.*$/m', $line, $contents) ?? $contents;
        } else {
            $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }

        file_put_contents($path, $contents);
    }
}
