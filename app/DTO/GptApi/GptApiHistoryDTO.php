<?php

namespace App\DTO\GptApi;

use App\DTO\GptApi\Request\GptApiRequestDTO;
use App\DTO\GptApi\Response\GptApiResponseDTO;
use Spatie\LaravelData\Data;

class GptApiHistoryDTO extends Data
{
    public function __construct(
        public GptApiResponseDTO|GptApiRequestDTO $action,
    )
    {}
}
