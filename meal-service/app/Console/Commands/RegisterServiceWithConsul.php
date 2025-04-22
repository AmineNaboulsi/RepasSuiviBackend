<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegisterServiceWithConsul extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:register-service-with-consul';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $port = 80;
            $serviceId = 'meal-service-' . uniqid();
            $address = 'meal-service';
            $consulHost = env('CONSUL_HOST', 'http://service-registry:8500');

            $response = Http::put("$consulHost/v1/agent/service/register", [
                'ID' => $serviceId,
                'Name' => 'meal-service',
                'Address' => $address,
                'Port' => $port,
                'Check' => [
                    'HTTP' => "http://$address/health",
                    'Interval' => '10s',
                ]
            ]);

            if ($response->successful()) {
                Log::info("✅ Registered to Consul: $serviceId");
            } else {
                Log::error("❌ Consul registration failed: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("⚠️ Consul registration exception: " . $e->getMessage());
        }
    }

}
