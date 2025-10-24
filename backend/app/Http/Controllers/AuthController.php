<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Services\Authentication\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    /**
     * AuthController constructor.
     *
     * @param $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        
        try {
            $service = $this->authService->login($request->all());
            if ($service['status']) {
                return $this->success($service['response'], $service['data']);
            } else {
                if ($service['response'] == 'validation') {
                    return $this->errorValidator($service['errors']);
                } elseif ($service['response'] == 'auth') {
                    return $this->errorAuthentication($service['errors']);
                } else {
                    return $this->errorServer($service['errors']);
                }
            }
        } catch (\Throwable $th) {
            return $this->errorServer($th->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $service = $this->authService->logout($request);
            if ($service['status']) {
                return $this->success($service['response'], $service['data'], $service['message'] ?? null);
            }
            if (($service['response'] ?? null) === 'auth') {
                return $this->errorAuthentication($service['errors'] ?? ['Unauthorized']);
            }
            return $this->errorServer($service['errors'] ?? ['Unexpected error']);
        } catch (\Throwable $th) {
            return $this->errorServer($th->getMessage());
        }
    }
}
