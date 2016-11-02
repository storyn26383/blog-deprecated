<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\Login as LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        auth()->attempt($request->only('email', 'password'));

        if ($user = auth()->user()) {
            $token = str_random(60);

            $user->update([
                'api_token' => $token,
            ]);

            return compact('token');
        }

        return abort(401);
    }

    public function logout()
    {
        $user = auth()->user();

        $user->api_token = null;
        $user->save();

        return ['success' => true];
    }
}
