<?php

namespace App\Modules\Auth\Features\Login\Dtos;

use App\Models\User;
use Spatie\LaravelData\Data;

class LoginResultDto extends Data
{
    public function __construct(
        public User $user,
        public string $token,
    ) {}
}
