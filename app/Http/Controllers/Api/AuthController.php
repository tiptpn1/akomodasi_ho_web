<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Login Action
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            // Cek apakah user ada berdasarkan email
            $user = User::where('master_user_nama', $request->username)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            // Ambil password terenkripsi dari database dan decrypt

            // Bandingkan password
            if (!password_verify($request->password, $user->master_user_password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Jika login berhasil, buat token
            $token = bin2hex(random_bytes(32));
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => User::with(['hakAkses', 'bagian'])->find($user->master_user_id),
            ]);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Logout Action
     */
    public function logout(Request $request)
    {
        try {
            // Ambil user berdasarkan token dari request
            $user = User::where('api_token', $request->bearerToken())->first();

            // Jika user tidak ditemukan (token tidak valid)
            if (!$user) {
                return response()->json(['message' => 'Invalid token'], 401);
            }

            // Kosongkan atau hapus token
            $user->api_token = null;
            $user->save();

            return ApiResponse::success('Logout successful', [], 200);
        } catch (\Exception $th) {
            return ApiResponse::success($th->getMessage(), [], 500);
        }
    }

    /**
     * Get Current User
     */
    public function currentUser(Request $request)
    {
        $user = $request->current_user;
        if ($user) {
            return ApiResponse::success('Success get current user', $user, 200);
        }
        return ApiResponse::success('Failed to get current user', [], 400);
    }
}
