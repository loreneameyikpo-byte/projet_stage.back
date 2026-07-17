<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SemoaCallBackController extends Controller
{
    private const TOKEN_CACHE_KEY = 'semoa_access_token';
    private const TOKEN_EXPIRATION_MINUTES = 30;

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    private function generateApiSecure(): string
    {
        $login = config('semoa.api_reference', '20');
        $apiKey = config('semoa.api_key');
        $concatenatedString = $login . $apiKey . $this->generateSalt();

        return hash('sha256', $concatenatedString);
    }

private function generateSalt(): int
{
    return random_int(0, 999999);
}

private function getApiBaseUrl(): string
{
    return rtrim((string) config('semoa.url', env('SEMOA_URL', 'https://api.semoa-payments.ovh/sandbox-v3')), '/');
}


private function f(?string $token = null): array
{
    $salt = $this->generateSalt();

    $login = config('semoa.username');
    $apiKey = config('semoa.api_key');
    $apiReference = config('semoa.api_reference', '20');

    if (empty($login) || empty($apiKey)) {
        throw new \RuntimeException('SEMOA credentials are missing. Please configure semoa.username and semoa.api_key.');
    }

    $headers = [
        'login' => $login,
        'apisecure' => hash('sha256', $login . $apiKey . $salt),
        'apireference' => $apiReference,
        'api-key' => $apiKey,
        'salt' => $salt,
        'Content-Type' => 'application/json',
    ];

    if ($token) {
        $headers["Authorization"] = "Bearer $token";
    }

    return $headers;
}



private function getToken(): string
{
    try {
        return Cache::remember(self::TOKEN_CACHE_KEY, self::TOKEN_EXPIRATION_MINUTES * 60, function () {
            $response = $this->client->post($this->getApiBaseUrl() . "/auth", [
                'json' => [
                    'grant_type' => 'password',
                    'username' => config('semoa.username'),
                    'password' => config('semoa.password'),
                    'client_id' => config('semoa.client_id'),
                    'client_secret' => config('semoa.client_secret'),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);
            if (!isset($data['access_token'])) {
                \Log::error('Token response missing access_token', [
                    'raw_body' => $body,
                    'decoded'  => $data,
                    'type'     => gettype($data)
                ]);

                throw new \RuntimeException('Invalid token response format');
            }

            return $data['access_token'];
        });
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        \Log::error('Authentication failed', [
            'error' => $e->getMessage(),
            'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null
        ]);
        throw new \RuntimeException('Authentication failed: ' . $e->getMessage());
    }
}


private function invalidateToken(): void
{
    Cache::forget(self::TOKEN_CACHE_KEY);
}



public function authentification()
{
    try {
        $token = $this->getToken();
        return response()->json(['token' => $token]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur d\'authentification: ' . $e->getMessage()], 500);
    }
}


public function ping()
{
    try {
        $token = $this->getToken();

        $response = $this->client->request('POST', $this->getApiBaseUrl() . "/ping", [
            'headers' => $this->getHeaders($token),
            'json' => ["action" => "ping"]
        ]);

        return response()->json(json_decode($response->getBody(), true));
    } catch (\Exception $e) {
        $this->invalidateToken();
        return response()->json(['error' => 'Erreur de ping: ' . $e->getMessage()], 500);
    }
}


public function paymentStatus($reference)
{
    $response = $this->getOrder($reference);
    $data = json_decode($response->getContent(), true);

    \Log::debug('Full API Response', $data);

    if (isset($data['items'][0])) {
        $orderData = $data['items'][0];
    } else {
        $orderData = $data;
    }

    return view('regions.status', [
        'status' => $orderData['state'] ?? 'UNKNOWN',
        'reference' => $orderData['order_reference'],
        'amount' => $orderData['amount'],
        'date' => $orderData['date_create'],
        'client' => [
            'phone' => $orderData['client']['phone']
        ]
    ]);
}


public function createOrder(Request $request)
{
    try {
        $token = $this->getToken();

        $payload = [
            'amount' => (float) $request->input('amount', 1000),
            'description' => $request->input('description', 'Paiement des frais de scolarité'),
            'client' => [
                'lastname' => $request->input('lastname', 'Test'),
                'firstname' => $request->input('firstname', 'User'),
                'phone' => $request->input('phone', '+22890123456'),
            ],
            'payment_method' => $request->input('payment_method', '14f4597d-ef96-4263-8107-1e1970959133'),
        ];

        $response = $this->client->request('POST', $this->getApiBaseUrl() . '/orders', [
            'headers' => $this->f($token),
            'json' => $payload,
        ]);

        $body = json_decode((string) $response->getBody(), true);

        return response()->json([
            'success' => true,
            'sent_payload' => $payload,
            'provider_response' => $body,
            'debug' => [
                'base_url' => $this->getApiBaseUrl(),
                'api_reference' => config('semoa.api_reference', '20'),
                'username' => config('semoa.username'),
            ],
        ], 200);
    } catch (\Exception $e) {
        $this->invalidateToken();
        return response()->json([
            'success' => false,
            'error' => 'Erreur lors de la création de la commande',
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function getOrder($reference)
{
    try {
        $token = $this->getToken();

        $response = $this->client->get($this->getApiBaseUrl() . "/orders/{$reference}", [
            'headers' => $this->f($token)
        ]);

        return response()->json(
            json_decode($response->getBody(), true),
            $response->getStatusCode()
        );
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}


public function orderList()
{
    try {
        $token = $this->getToken();

        $response = $this->client->request('GET', $this->getApiBaseUrl() . "/orders", [
            'headers' => $this->f($token)
        ]);

        return response()->json(json_decode($response->getBody(), true));
    } catch (\Exception $e) {
        $this->invalidateToken();
        return response()->json(['error' => 'Erreur lors de la récupération des commandes: ' . $e->getMessage()], 500);
    }
}


public function processPayment(Request $request)
{
    $validator = Validator::make($request->all(), [
        'lastname' => 'required|string|max:255',
        'firstname' => 'required|string|max:255',
        'phone' => 'required|string|regex:/^\+228\d{8}$/',
        'amount' => 'required|numeric|min:100|max:1000000',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $etudiant = auth()->user();
        $token = $this->getToken();
        if (!$etudiant) {
            return response()->json(['success' => false, 'message' => 'Étudiant non trouvé'], 404);
        }
        $annee = AnneeScolaire::where('active', true)->first();

        $etudiant = Candidature::where('etudiant_id', $etudiant->id)->where('annee_scolaire_id', $annee->id)->latest()->first();
        $niveau = $etudiant->niveau;
        $frais =  FraisScolarite::where('annee_scolaire_id', $annee->id)
            ->where('niveau_id', $niveau->id)
            ->first();

        $tranches = TranchePaiement::where('frais_scolarite_id', $frais->id)
            ->get();

        $trancheNonPaye = null;
        $montantRestant = $request->amount;
        $montantPaye = 0;
        // while ($montantRestant > 0) {
        //     foreach ($tranches as $tranche) {
        //         $montantTranchePaye = Paiement::where('etudiant_id', $etudiant->etudiant_id)
        //             ->where('tranche_paiement_id', $tranche->id)
        //             ->where('annule', false)
        //             ->sum('montant');
        //
        //         $resteTranche = $tranche->montant - $montantTranchePaye;
        //
        //         if ($resteTranche > 0) {
        //             $montantPaye = min($montantRestant, $resteTranche);
        //
        //             Paiement::create([
        //                 'etudiant_id' => $etudiant->etudiant_id,
        //                 'tranche_paiement_id' => $tranche->id,
        //                 'montant' => $montantPaye,
        //                 'mode_paiement' => 'semoa',
        //                 'reference' => $request->input('reference', 'REF-' . uniqid()),
        //                 'status' => 'en_attente',
        //                 'recu' => false,
        //                 'date_paiement' => now(),
        //                 'annule' => false,
        //                 'motif_annulation' => null,
        //                 'date_annulation' => null,
        //                 'annule_par' => null,
        //             ]);
        //
        //             $montantRestant -= $montantPaye;
        //         }
        //         if ($montantRestant <= 0) {
        //             break;
        //         }
        //     }
        //     break;
        // }

        $response = $this->client->post($this->getApiBaseUrl() . "/orders", [
            'headers' => $this->f($token),
            'json' => [
                'amount' => (float) $request->input('amount'),
                'description' => 'Paiement via ' . config('app.name'),
                'client' => [
                    'lastname' => $request->input('lastname'),
                    'firstname' => $request->input('firstname'),
                    'phone' => $request->input('phone'),
                ],
                "gateway" => [
                    "reference" => $request->input("payment_method"),
                ],
                "currency" => "XOF",
                "callback_url" => "http://localhost:8000/espace-etudiant/mes-payements"
            ]
        ]);

        \Log::info('API Response', ['response' => (string) $response->getBody()]);

        $data = json_decode($response->getBody(), true);
        \Log::info('Data after API call', $data);

        $gatewayConfigs = [
            '14f4597d-ef96-4263-8107-1e1970959133' => [
                'id' => 11,
                'type' => 'recap-sandbox',
            ],
            '016eb63c-f29d-4384-92e4-b1bd37ef69f8' => [
                'id' => 1,
                'type' => 'recap',
            ],
            'a2c87957-1033-46e9-8706-056e45737de1' => [
                'id' => 27,
                'type' => 'recap',
            ],
            '52bfd484-13ef-44f3-b128-adf7187779b0' => [
                'id' => 6,
                'type' => 'recap',
            ],
            'f7bbfaef-eba3-4b82-ac31-61eb2b772289' => [
                'type' => 'external',
            ],
        ];

        $gatewayRef = $request->input('payment_method');
        $config = $gatewayConfigs[$gatewayRef] ?? null;

        if (!$config) {
            return response()->json([
                'success' => false,
                'error' => 'Méthode de paiement inconnue.'
            ], 400);
        }

        $orderReference = $data['order_reference'] ?? null;

        if (!$orderReference) {
            return response()->json([
                'success' => false,
                'error' => 'Référence de commande manquante.'
            ], 500);
        }

        if ($config['type'] === 'recap') {
            $redirectUrl = "https://sandbox.cashpay.tg/facture/recap/{$orderReference}/{$config['id']}";
        } elseif ($config['type'] === 'recap-sandbox') {
            $redirectUrl = "https://sandbox.cashpay.tg/facture/recap-sandbox/{$orderReference}/{$config['id']}";
        } elseif ($config['type'] === 'external') {
            $redirectUrl = $data['redirect_url'] ?? $data['long_bill_url'] ?? null;

            if (!$redirectUrl) {
                return response()->json([
                    'success' => false,
                    'error' => 'URL de redirection externe manquante'
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Type de redirection inconnu.'
            ], 500);
        }

        return redirect()->away($redirectUrl);
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        \Log::error('API Request failed', [
            'message' => $e->getMessage(),
            'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Payment processing failed',
            'details' => env('APP_DEBUG') ? $e->getMessage() : null
        ], 500);
    }
}

}
