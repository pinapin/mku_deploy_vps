<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'connect_timeout' => 30,
        ]);
        $this->baseUrl = 'https://ws.umk.ac.id/services/';
        $this->apiKey = 'a8c8803b3f6b9750b04c9891c91825e481302780';
    }

    /**
     * Get dosen data from API
     *
     * @return array
     */
    public function getDosenData()
    {
        try {
            $response = $this->client->post($this->baseUrl . 'disposisi/getdosen', [
                'headers' => [
                    'umk_api_key' => $this->apiKey,
                ],
                'form_params' => [
                    'nis' => '0'
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['status']) && $body['status'] === true) {
                return [
                    'success' => true,
                    'data' => $body['result']['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mendapatkan data dosen dari API',
                'error' => $body
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data dosen',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Make a GET request
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     */
    public function get($endpoint, $params = [], $headers = [])
    {
        try {
            $defaultHeaders = [
                'umk_api_key' => $this->apiKey,
            ];

            $response = $this->client->get($this->baseUrl . $endpoint, [
                'headers' => array_merge($defaultHeaders, $headers),
                'query' => $params
            ]);

            return [
                'success' => true,
                'data' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan request GET',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Make a POST request
     *
     * @param string $endpoint
     * @param array $formParams
     * @param array $headers
     * @return array
     */
    public function getSSO($endpoint, $headers = [])
    {
        try {
            $response = $this->client->post($this->baseUrl . $endpoint, [
                'headers' => $headers
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan request POST',
                'error' => $e->getMessage()
            ];
        }
    }
}
