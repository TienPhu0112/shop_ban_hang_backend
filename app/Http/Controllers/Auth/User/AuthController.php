<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User\User;
use App\Notifications\SendRegisterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    const TOKEN_EXPIRE_MINUTES = 15;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function login(LoginRequest $request)
    {

        if ($token = Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (!Auth::user()->email_verified_at) {
                $verifyToken = DB::table('verify_account_tokens')->where('user_id', '=', Auth::user()->id);
                if ($verifyToken->first() && Carbon::now()->gt($verifyToken->first()->expired_at)) {
                    $verifyToken->delete();
                    $this->sendVerifyAccountEmail(Auth::user(), $verifyToken->first());
                }

                Auth::logout();

                return response()->json([
                    'message' => 'Unverified account, please check your email to verified its'
                ], 401);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => Carbon::now()->addMinutes(Config::get('jwt.ttl')),
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'message' => 'Wrong usename or password'
        ], 401);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'status' => true
        ]);
    }

    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
                $user = $this->user->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'avatar' => 'image'
                ]);
                
                $this->sendVerifyAccountEmail($user);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return response()->json([
            'message' => 'Please verify your email, link will expire in ' . self::TOKEN_EXPIRE_MINUTES . ' minutes'
        ]);
    }

    public function verify($token)
    {
        $isValid = false;
        $userToken = DB::table('verify_account_tokens')->where('token', '=', $token);
        try {
            DB::beginTransaction();
                $verifyToken = $userToken->first();
                if ($verifyToken && Carbon::now()->lt($verifyToken->expired_at)) {
                    $isValid = true;
                    $user = $this->user->find($verifyToken->user_id);
                    $user->email_verified_at = Carbon::now();
                    $user->save();
                    $userToken->delete();
                }
                
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        if (!$isValid) {
            $userToken->delete();

            return response()->json([
                'status' => false
            ], 404);
        }

        return response()->json([
            'status' => true
        ]);
    }

    public function sendVerifyAccountEmail($user, $verifyToken = false)
    {
        DB::table('verify_account_tokens')->insert([
            'user_id' => $user->id,
            'token' => Str::random(25),
            'expired_at' => Carbon::now()->addMinutes(self::TOKEN_EXPIRE_MINUTES)
        ]);

        if (!$verifyToken) {
            $verifyToken = DB::table('verify_account_tokens')
            ->where('user_id', '=', $user->id)
            ->first();
        }

        Notification::send($user, new SendRegisterMail($verifyToken->token));
    }
}
