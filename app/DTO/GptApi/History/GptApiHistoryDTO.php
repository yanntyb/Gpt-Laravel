<?php

namespace App\DTO\GptApi\History;

use App\DTO\GptApi\Request\GptApiRequestDTO;
use App\DTO\GptApi\Response\GptApiResponseDTO;
use Spatie\LaravelData\Data;

class GptApiHistoryDTO extends Data
{
    public function __construct(
        public ?GptApiResponseDTO $response,
        public ?GptApiRequestDTO $request,
    )
    {}
}
