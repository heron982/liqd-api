<?php

namespace App\Modules\Auth\Features\Me\Dtos;

use Spatie\LaravelData\Data;

class MeDto extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}
