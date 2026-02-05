<?php

namespace App\Http;

use App\Http\Middleware\AccessLogMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'web' => [
            AccessLogMiddleware::class,
        ],
        'api' => [
            AccessLogMiddleware::class,
        ],
    ];

    protected $routeMiddleware = [
        'access.log' => AccessLogMiddleware::class,
    ];
}
