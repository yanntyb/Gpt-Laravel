<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class GptApiResponseUsageDTO extends Data
{
    public function __construct(
        public int $promptToken,
        public int $completionToken,
        public int $totalToken,
    ) {}
}
