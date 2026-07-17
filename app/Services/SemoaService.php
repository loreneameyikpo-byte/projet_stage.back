<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemoaService
{
    protected string $baseUrl;
    protected string $apiKey;

    private string $url;
    private string $userName;
    private string $password;
    private string $clientSecret;
    private string $clientId;
    private string $apiKeyValue;
    private string $apiReference;
    private string $gatewayReference;

    public function __construct()
    {
        $this->loadStaticData();
    }

    private function loadStaticData(): void
    {
        $this->url = rtrim((string) config('semoa.url'), '/') . '/';
        $this->userName = trim((string) config('semoa.username'));
        $this->password = trim((string) config('semoa.password'));
        $this->clientSecret = trim((string) config('semoa.client_secret'));
        $this->clientId = trim((string) config('semoa.client_id'));
        $this->apiKeyValue = trim((string) config('semoa.api_key'));
        $this->apiReference = trim((string) config('semoa.api_reference', '20'));
        $this->gatewayReference = trim((string) config('semoa.gateway_reference'));
    }


private function getToken(): string
{
    return Cache::remember('semoa_access_token', 30 * 60, function () {
        $response = Http::post($this->url . 'auth', [
            "grant_type" => "password",
            "username" => $this->userName,
            "password" => $this->password,
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret,
        ]);

        if ($response->failed()) {
            \Log::error('SEMOA Auth Failed:', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception("Échec d'authentification SEMOA : " . $response->body());
        }

        $data = $response->json();
        \Log::debug('SEMOA Auth Success:', ['data' => array_keys($data)]);

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!isset($data['access_token'])) {
            throw new \Exception("Token absent de la réponse SEMOA : " . $response->body());
        }

        return $data['access_token'];
    });
}
    
private function getHeaders(string $token): array
{
    $salt = (string) random_int(0, 999999);
    $signature = hash('sha256', $this->userName . $this->apiKeyValue . $salt);

    $headers = [
        "Authorization" => "Bearer $token",
        "login" => $this->userName,
        "apisecure" => $signature,
        "apireference" => $this->apiReference,
        "api-key" => $this->apiKeyValue,
        "salt" => $salt,
        "Content-Type" => "application/json",
        "Accept" => "application/json"
    ];

    return $headers;
}

public function initializePayment(array $data, bool $isRetry = false): array
{
    $token = $this->getToken();

    $gatewayRef = $data['gateway_reference'] ?? $this->gatewayReference;

    $payload = [
        "amount" => $data['amount'],
        "client" => [
            "phone" => $data['phone']
        ],
        "gateway" => [
            "reference" => $gatewayRef
        ]
    ];

    if (isset($data['description'])) $payload["description"] = $data['description'];

    $baseUrl = rtrim(env('APP_URL'), '/');
    $payload["callback_url"] = $data['callback_url'] ?? ($baseUrl . '/api/semoa-callback-url');

    Log::info('SEMOA Payment Initialization Payload', [
        'url' => $this->url . 'orders',
        'payload' => $payload
    ]);

    $response = Http::withHeaders($this->getHeaders($token))->post($this->url . 'orders', $payload);

    if ($response->failed()) {
        if ($response->status() === 401 && !$isRetry) {
            Cache::forget('semoa_access_token');
            return $this->initializePayment($data, true);
        }
        throw new \Exception("Erreur SEMOA (Initialisation) : " . $response->body());
    }

    return $response->json();
}

public function checkPaymentStatus(string $reference): array
{
    $token = $this->getToken();

    $response = Http::withHeaders($this->getHeaders($token))
        ->get($this->url . "orders/{$reference}");

    if ($response->failed()) {
        throw new \Exception("Erreur SEMOA (Vérification) : " . $response->body());
    }

    return $response->json();
}
}