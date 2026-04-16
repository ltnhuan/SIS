<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

return Application::configure(basePath: dirname(__DIR__))
    ->create();
