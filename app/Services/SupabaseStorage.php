<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseStorage
{
    private const array EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function uploadProductPhoto(UploadedFile $file, int $businessId): string
    {
        $mime = (string) $file->getMimeType();
        $extension = self::EXTENSIONS[$mime] ?? null;

        if ($extension === null) {
            abort(422, 'Foto produk harus berupa JPG, PNG, atau WebP.');
        }

        $objectPath = 'products/'.$businessId.'/'.Str::ulid().'.'.$extension;

        try {
            $response = $this->client()
                ->withBody($file->get(), $mime)
                ->post($this->objectUrl($objectPath));
        } catch (ConnectionException $exception) {
            Log::warning('Supabase storage upload failed.', [
                'message' => $exception->getMessage(),
            ]);

            abort(502, 'Foto produk gagal disimpan.');
        }

        if ($response->failed()) {
            Log::warning('Supabase storage upload failed.', [
                'status' => $response->status(),
                'bucket' => $this->bucket(),
            ]);

            abort(502, 'Foto produk gagal disimpan.');
        }

        return $this->publicUrl($objectPath);
    }

    public function delete(?string $photoPath): void
    {
        $objectPath = $this->objectPath($photoPath);

        if ($objectPath === null) {
            return;
        }

        try {
            $response = $this->client()->delete($this->bucketUrl(), [
                'prefixes' => [$objectPath],
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('Supabase storage delete failed.', [
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        if ($response->failed()) {
            Log::warning('Supabase storage delete failed.', [
                'status' => $response->status(),
                'bucket' => $this->bucket(),
            ]);
        }
    }

    public function objectPath(?string $photoPath): ?string
    {
        if ($photoPath === null || $photoPath === '') {
            return null;
        }

        $prefix = $this->publicUrl('');

        if (! str_starts_with($photoPath, $prefix)) {
            return null;
        }

        $objectPath = rawurldecode(substr($photoPath, strlen($prefix)));

        if ($objectPath === '' || str_contains($objectPath, '..') || str_starts_with($objectPath, '/')) {
            return null;
        }

        return $objectPath;
    }

    private function client(): PendingRequest
    {
        $secret = (string) config('services.supabase.secret');

        if ($secret === '' || $this->projectUrl() === '' || $this->bucket() === '') {
            abort(500, 'Penyimpanan foto belum dikonfigurasi.');
        }

        return Http::withToken($secret)
            ->withHeaders([
                'apikey' => $secret,
                'Cache-Control' => '3600',
            ])
            ->connectTimeout(3)
            ->timeout(20);
    }

    private function objectUrl(string $objectPath): string
    {
        return $this->bucketUrl().'/'.$objectPath;
    }

    private function bucketUrl(): string
    {
        return $this->projectUrl().'/storage/v1/object/'.$this->bucket();
    }

    private function publicUrl(string $objectPath): string
    {
        return $this->projectUrl().'/storage/v1/object/public/'.$this->bucket().'/'.$objectPath;
    }

    private function projectUrl(): string
    {
        return rtrim((string) config('services.supabase.url'), '/');
    }

    private function bucket(): string
    {
        return (string) config('services.supabase.bucket');
    }
}
