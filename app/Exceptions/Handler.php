<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    public function render($request, Throwable $exception): Response
    {
        if (! $request->expectsJson()) {
            return parent::render($request, $exception);
        }

        return match (true) {
            $exception instanceof ValidationException => $this->error('Dữ liệu không hợp lệ', 422, $exception->errors()),
            $exception instanceof AuthenticationException => $this->error('Chưa xác thực', 401),
            $exception instanceof AuthorizationException => $this->error('Không có quyền truy cập', 403),
            $exception instanceof ModelNotFoundException => $this->error('Không tìm thấy dữ liệu', 404),
            default => $this->error(
                app()->environment('production') ? 'Lỗi hệ thống' : $exception->getMessage(),
                500,
            ),
        };
    }
}
