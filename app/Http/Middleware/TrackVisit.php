<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Visit;

class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            // Only track GET requests to public pages and API GETs
            if ($request->isMethod('GET')) {
                Visit::create([
                    'user_id' => Auth::id(),
                    'path' => substr($request->path(), 0, 255),
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'referer' => substr((string) $request->headers->get('referer'), 0, 255),
                ]);
            }
        } catch (\Throwable $e) {
            // swallow tracking errors
        }

        return $response;
    }
}
