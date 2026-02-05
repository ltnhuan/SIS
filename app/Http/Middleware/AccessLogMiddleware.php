<?php

namespace App\Http\Middleware;

use App\Modules\Core\Models\AccessLog;
use Closure;
use Illuminate\Http\Request;

class AccessLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        AccessLog::create([
            'user_id' => $request->user()?->id,
            'route' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return $response;
    }
}
