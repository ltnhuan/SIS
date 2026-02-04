<?php

namespace App\Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ.'], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'message' => 'Thông tin tài khoản hiện tại.',
            'data' => $request->user(),
        ]);
    }
}
