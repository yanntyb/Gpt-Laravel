<?php

namespace App\DTO\GptApi\Response;

use App\DTO\ExtendedData;
use App\DTO\GptApi\GptApiMessageDTO;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class GptApiResponseDTO extends ExtendedData
{
    public function __construct(
        public string $id,
        public string $object,
        public Carbon $created,
        public GptApiResponseUsageDTO $usage,
        public string $model,

        #[DataCollectionOf(GptApiIAResponseDTO::class)]
        public DataCollection $responses,
    ) {}

    /**
     * @return DataCollection|GptApiIAResponseDTO[]
     */
    public function getResponsesMessage(): DataCollection|array
    {
        return $this->responses->items();
    }

    public function getFirstResponseMessage(): GptApiMessageDTO
    {
        return $this->getResponsesMessage()[0]?->message;
    }

    public function getMessage(): string
    {
        return $this->getFirstResponseMessage()->content;
    }

}
