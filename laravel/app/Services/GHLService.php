<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\log;

class GHLService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('GHL_BASE_URL', 'https://rest.gohighlevel.com/v1');
        $this->apiKey  = env('GHL_API_KEY');
    }

    /**
     * Crear o actualizar un contacto en GHL
     *
     * @param array $data
     * @return array|null
     */
    public function createOrUpdateContact(array $data): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/contacts/', [
            'firstName' => $data['first_name'] ?? '',
            'lastName'  => $data['last_name'] ?? '',
            'email'     => $data['email'] ?? '',
            'phone'     => $data['phone'] ?? '',
        ]);

        if ($response->successful()) {
            return $response->json()['contact']['id'] ?? null;
        }

        Log::error('Error creando contacto en GHL', [
            'payload'  => $data,
            'response' => $response->body()
        ]);

        return null;
    }

}
