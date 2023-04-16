<?php

namespace App\DTO\GptApi\Request;

use App\DTO\ExtendedData;
use App\DTO\GptApi\GptApiMessageDTO;

class GptApiRequestDTO extends ExtendedData
{
    public function __construct(
        public GptApiMessageDTO $message,
        public string $apiKey,
        public string $model,
        public string $baseUrl = 'https://api.openai.com/v1/chat/completions',
        public int $maxToken = 100,
    ) {}

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message->content;
    }
}
