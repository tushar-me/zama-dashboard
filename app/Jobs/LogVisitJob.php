<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Browser;
use GeoIP;
use Illuminate\Support\Facades\Log;

class LogVisitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        try {
            $ip = $this->data['ip'];
            $userAgent = $this->data['user_agent'];

            $isBot = preg_match('/bot|crawl|slurp|spider/i', $userAgent);

            $geo = geoip($ip);

            Visit::create([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'device' => Browser::deviceFamily(),
                'platform' => Browser::platformFamily(),
                'browser' => Browser::browserName(),
                'browser_version' => Browser::browserVersion(),
                'os_version' => Browser::platformVersion(),
                'country' => $geo?->country,
                'city' => $geo?->city,
                'location' => trim(($geo?->city ?? '') . ', ' . ($geo?->country ?? '')),
                'path' => $this->data['path'],
                'method' => $this->data['method'],
                'referer' => $this->data['referer'],
                'user_id' => $this->data['user_id'],
                'page' => $this->data['route_name'],
                'language' => $this->data['language'],
                'is_bot' => $isBot,
                'is_unique' => !Visit::whereDate('created_at', today())
                    ->where('ip_address', $ip)
                    ->exists(),
            ]);
        } catch (\Throwable $e) {
            Log::error('LogVisitJob error: ' . $e->getMessage());
        }
    }
}
