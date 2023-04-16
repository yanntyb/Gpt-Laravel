<?php

namespace App\DTO\GptApi;

use App\DTO\ExtendedData;

class GptApiMessageDTO extends ExtendedData
{
    public function __construct(
        public string $role,
        public string $content,
    ) {}
}
