<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class GptApiRequestDTO extends ExtendedData
{
    public function __construct(
        public GptApiMessageDTO $message,
        public string $apiKey,
        public string $model,
        public string $baseUrl = 'https://api.openai.com/v1/chat/completions',
        public int $maxToken = 100,
    ) {}
}
