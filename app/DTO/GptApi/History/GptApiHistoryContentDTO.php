<?php

namespace App\DTO\GptApi\History;

use App\DTO\GptApi\Request\GptApiRequestDTO;
use App\DTO\GptApi\Response\GptApiResponseDTO;
use Spatie\LaravelData\Data;

class GptApiHistoryContentDTO extends Data
{
    public function __construct(
        public string $role,
        public string $message,
    )
    {}
}
