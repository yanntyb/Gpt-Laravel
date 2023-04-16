<?php

namespace App\DTO;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class GptApiIAResponseDTO extends Data
{
    public function __construct(
        public string $finishReason,
        public int $index,

        public GptApiMessageDTO $message,
    ) {}
}
