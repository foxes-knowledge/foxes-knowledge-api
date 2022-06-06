<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\SignUpRequest;
use App\Http\Requests\SignInRequest;

class AuthController extends Controller
{
    /**
     * Register a user and issue a token.
     */
    public function signUp(SignUpRequest $request): Response
    {
        $color = dechex(rand(0x000000, 0xFFFFFF));

        $user = User::create([
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'color' => "#$color"
        ]);

        $token = $user->createToken('auth_token');

        return response([
            'message' => 'Signed up successfully.',
            'user' => $user->toArray(),
            'token' => [
                'type' => 'Bearer',
                'value' => $token->plainTextToken,
                'ttl' => config('sanctum.expiration')
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Issue user a token.
     */
    public function signIn(SignInRequest $request): Response
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid login credentials.'
            ], Response::HTTP_CONFLICT);
        }

        $user = User::where('email', $request->input('email'))->firstOrFail();

        $token = $user->createToken('auth_token');

        return response([
            'message' => 'Signed in successfully.',
            'token' => [
                'type' => 'Bearer',
                'value' => $token->plainTextToken,
                'ttl' => config('sanctum.expiration')
            ]
        ]);
    }

    /**
     * Revoke a token.
     */
    public function signOut(): Response
    {
        Auth::user()->currentAccessToken()->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Revoke a token.
     */
    public function revokeTokens(): Response
    {
        Auth::user()->tokens()->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }


    /**
     * Get current user.
     */
    public function me(): Response
    {
        return response(Auth::user());
    }
}
