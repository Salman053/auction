<?php

namespace Database\Seeders;

use App\Models\Proxy;
use Illuminate\Database\Seeder;

class ProxySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proxies = [
            ['scheme' => 'http', 'host' => 'jp-proxy-1.local', 'port' => 8001, 'country' => 'JP', 'is_active' => true],
            ['scheme' => 'http', 'host' => 'jp-proxy-2.local', 'port' => 8002, 'country' => 'JP', 'is_active' => true],
            ['scheme' => 'http', 'host' => 'jp-proxy-3.local', 'port' => 8003, 'country' => 'JP', 'is_active' => false],
        ];

        foreach ($proxies as $proxy) {
            Proxy::query()->updateOrCreate(
                [
                    'scheme' => $proxy['scheme'],
                    'host' => $proxy['host'],
                    'port' => $proxy['port'],
                    'username' => null,
                ],
                $proxy,
            );
        }
    }
}
