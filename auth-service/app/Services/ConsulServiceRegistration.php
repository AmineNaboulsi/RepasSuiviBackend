<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ConsulServiceRegistration
{
    protected $client;
    protected $consulHost;

    public function __construct()
    {
        // Set the Consul host and port
        $this->consulHost = env('CONSUL_HOST', 'service-registry');
        $this->client = new Client();
    }

    public function registerService()
    {
        $serviceId = 'auth-service'; 
        $serviceName = 'auth-service';
        $servicePort = env('SERVICE_PORT', 80);
        $checkUrl = '/health';

        $url = "http://{$this->consulHost}:8500/v1/agent/service/register";

        $data = [
            'ID' => $serviceId,
            'Name' => $serviceName,
            'Tags' => ['auth', 'microservice'],
            'Address' => env('SERVICE_HOST', '127.0.0.1'),
            'Port' => $servicePort,
            'Check' => [
                'HTTP' => 'http://' . env('SERVICE_HOST', '127.0.0.1') . ':' . $servicePort . $checkUrl,
                'Interval' => '10s',
                'Timeout' => '5s',
            ]
        ];

        try {
            $response = $this->client->put($url, [
                'json' => $data
            ]);

            if ($response->getStatusCode() === 200) {
                Log::info("Auth service registered successfully with Consul");
            }
        } catch (\Exception $e) {
            Log::error("Failed to register auth service with Consul: " . $e->getMessage());
        }
    }

    public function deregisterService()
    {
        $serviceId = 'auth-service';
        $url = "http://{$this->consulHost}:8500/v1/agent/service/deregister/{$serviceId}";

        try {
            $response = $this->client->put($url);

            if ($response->getStatusCode() === 200) {
                Log::info("Auth service deregistered successfully from Consul");
            }
        } catch (\Exception $e) {
            Log::error("Failed to deregister auth service from Consul: " . $e->getMessage());
        }
    }
}
