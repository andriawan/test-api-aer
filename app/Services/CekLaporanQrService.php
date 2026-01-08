<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class CekLaporanQrService
{
    protected string $baseUrl;
    protected string $partnerId;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl  = 'https://gateway.ceklaporan.com/api';
        $this->partnerId = config('services.ceklaporan.partner_id');
        $this->apiKey    = config('services.ceklaporan.api_key');
    }

    public function generateQr(
        string $refId,
        int $amount,
        Carbon $expiredAt,
        bool $isStatic = false
    ): array {
        $timestamp = now()->toIso8601String();

        $payload = [
            'partner_id' => $this->partnerId,
            'refid'      => $refId,
            'amount'     => $amount,
            'exp_date'   => $expiredAt->toIso8601String(),
            'is_static'  => $isStatic,
        ];

        $signature = $this->generateSignature($payload, $timestamp);

        \Log::info('CekLaporan Qr Payload:', $payload);
        \Log::info('CekLaporan Qr Signature:', $signature);
        \Log::info('CekLaporan Qr API Key:', $this->apiKey);



        $response = Http::withHeaders([
            'Apikey'      => $this->apiKey,
            'X-Signature' => $signature,
            'X-Timestamp' => $timestamp,
        ])
        ->acceptJson()
        ->post("{$this->baseUrl}/qr/generate", $payload);

        if ($response->failed()) {
            throw new \Exception(
                'CekLaporan API Error: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function getQrInfo(PaymentHistory $paymentHistory): array {
        $timestamp = now()->toIso8601String();

        $payload = [
            'partner_id' => $this->partnerId,
            'refid'      => $paymentHistory->ref_id,
            'trxid'      => $paymentHistory->trx_id,
        ];

        $signature = $this->generateSignature($payload, $timestamp);

        $response = Http::withHeaders([
            'apikey'      => $this->apiKey,
            'X-Signature' => $signature,
            'X-Timestamp' => $timestamp,
        ])
        ->acceptJson()
        ->post("{$this->baseUrl}/qr/query", $payload);

        if ($response->failed()) {
            throw new \Exception(
                'CekLaporan API qr query Error: ' . $response->body()
            );
        }

        return $response->json();
    }

    protected function generateSignature(array $payload, string $timestamp): string
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);

        \Log::info('CekLaporan Qr Signature Body:', ['body' => $body, 'timestamp' => $timestamp]);

        return hash_hmac(
            'sha256',
            $body . $timestamp,
            $this->apiKey
        );
    }
}