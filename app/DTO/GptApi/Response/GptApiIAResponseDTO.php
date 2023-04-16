<?php

namespace App\DTO\GptApi\Response;

use App\DTO\GptApi\GptApiMessageDTO;
use Spatie\LaravelData\Data;

class GptApiIAResponseDTO extends Data
{
    public function __construct(
        public string $finishReason,
        public int $index,

        public GptApiMessageDTO $message,
    ) {}
}
