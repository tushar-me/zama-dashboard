<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\LogVisitJob;
use Illuminate\Support\Facades\Log;
class TrackVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $response = $next($request);

        try {
            $data = [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'path' => $request->path(),
                'method' => $request->method(),
                'user_id' => $request->user->id,
                'language' => $request->getPreferredLanguage(),
                'route_name' => optional($request->route())->getName(),
            ];
            LogVisitJob::dispatch($data)->onQueue('tracking');
        } catch (\Throwable $e) {
            Log::error('TrackVisit middleware error: ' . $e->getMessage());
        }

        return $response;
    }
}
