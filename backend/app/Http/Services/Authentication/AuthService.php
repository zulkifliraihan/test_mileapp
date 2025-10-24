<?php

namespace App\Http\Services\Authentication;

use App\Http\Repositories\UserRepository\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService {
    private $userInterface;

    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function login($data): array
    {
        $return = [];

        $findByEmail = $this->userInterface->detailByEmail($data['email']);

        if (!$findByEmail) {
            $return = [
                'status' => false,
                'response' => 'auth',
                'errors' => ['Email or Password is incorrect!']
            ];

            return $return;
        }

        $checkPassword = Hash::check($data['password'], $findByEmail->password);
        if(!$checkPassword) {
            $return = [
                'status' => false,
                'response' => 'auth',
                'errors' => ["Email or Password is incorrect!"]
            ];

            return $return;
        }

        $token = JWTAuth::attempt($data);
        if (!$token) {
            $return = [
                'status' => false,
                'response' => 'server',
                'message' => null,
                'errors' => ["Failed generate token!"]
            ];

            return $return;
        }

        $user = JWTAuth::user();

        $resultData = [
            'authorization' => [
                'type' => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'token' => $token
            ],
            'user' => $user
        ];

        $return = [
            'status' => true,
            'response' => 'login',
            'message' => 'Successfully login!',
            'data' => $resultData,
        ];
    

        return $return;

    }

    public function logout($request): array
    {
        Auth::logout();
        JWTAuth::invalidate($request->bearerToken());

        $return = [
            'status' => true,
            'response' => 'logout',
            'data' => null,
            'message' => 'Successfully Logout!'
        ];

        return $return;
    }
}