<?php

namespace App\Modules\Auth\Features\Register\Dtos;

use App\Models\User;
use Spatie\LaravelData\Data;

class RegisterResultDto extends Data
{
    public function __construct(
        public User $user,
        public string $token,
    ) {}
}
