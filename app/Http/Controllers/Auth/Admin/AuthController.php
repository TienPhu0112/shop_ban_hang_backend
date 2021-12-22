<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    protected $admin;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function login(LoginRequest $request)
    {
        if ($token = Auth::guard('admin_api')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $admin = $this->admin->with('adminInformation')->find(Auth::guard('admin_api')->user()->id);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => Auth::guard('admin_api')->factory()->getTTL() * 60,
                'admin' => $admin
            ]);
        }

        return response()->json([
            'message' => 'Wrong usename or password'
        ], 401);
    }

    public function refresh()
    {
        $admin = $this->admin->with('adminInformation')->find(Auth::guard('admin_api')->user()->id);

        return response()->json([
            'access_token' => Auth::guard('admin_api')->refresh(),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('admin_api')->factory()->getTTL() * 60,
            'admin' => $admin
        ]);
    }

    public function logout()
    {
        Auth::guard('admin_api')->logout();

        return response()->json([
            'status' => true
        ]);
    }
}
