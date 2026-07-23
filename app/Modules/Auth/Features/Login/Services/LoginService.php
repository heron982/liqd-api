<?php

namespace App\Modules\Auth\Features\Login\Services;

use App\Modules\Auth\Features\Login\Dtos\LoginResultDto;
use App\Modules\Auth\Domain\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function execute(string $email, string $password): LoginResultDto
    {
        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! $this->userRepository->passwordMatches($user, $password)) {
            throw ValidationException::withMessages([
                'email' => [trans('messages.auth.credentials_incorrect')],
            ]);
        }

        $token = $this->userRepository->createToken($user);

        return LoginResultDto::from([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
