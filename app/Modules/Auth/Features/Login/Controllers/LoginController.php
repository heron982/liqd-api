<?php

namespace App\Modules\Auth\Features\Login\Controllers;

use App\Modules\Auth\Features\Login\Requests\LoginRequest;
use App\Modules\Auth\Features\Login\Services\LoginService;
use Illuminate\Http\JsonResponse;

class LoginController
{
    public function execute(LoginRequest $request, LoginService $service): JsonResponse
    {
        $result = $service->execute(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        return response()->json([
            'user' => [
                'id' => $result->user->id,
                'name' => $result->user->name,
                'email' => $result->user->email,
            ],
            'token' => $result->token,
        ]);
    }
}
