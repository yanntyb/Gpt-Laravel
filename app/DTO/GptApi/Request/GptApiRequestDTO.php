<?php

namespace App\DTO\GptApi\Request;

use App\DTO\ExtendedData;
use App\DTO\GptApi\GptApiMessageDTO;
use App\DTO\GptApi\WithHistory;

class GptApiRequestDTO extends ExtendedData implements WithHistory
{
    public static string $historyProperty = 'request';

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

    /**
     * @return string
     */
    public function getHistoryContent(): string
    {
        return $this->getMessage();
    }

    /**
     * @return string
     */
    public function getHistoryLabel(): string
    {
        return $this->message->role;
    }


}
