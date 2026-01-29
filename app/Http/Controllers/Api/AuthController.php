<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle user login and return access token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Validate dữ liệu
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        // 2. Kiểm tra thông tin đăng nhập
        if (!Auth::attempt($validated)) {
            return send_error('Email hoặc mật khẩu không chính xác', [], 401);
        }

        // 3. Tạo Token
        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        // 4. Trả về kết quả với UserResource
        return send_response([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'Đăng nhập thành công');
    }

    /**
     * Get the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return send_response(
            new UserResource($request->user()),
            'Lấy thông tin người dùng thành công'
        );
    }

    /**
     * Logout the current user (revoke current token).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Xóa token hiện tại
        $request->user()->currentAccessToken()->delete();

        return send_response(null, 'Đăng xuất thành công');
    }
}
