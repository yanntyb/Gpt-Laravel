<?php

namespace App\DTO;

use Carbon\Carbon;
use DeepCopy\Exception\PropertyException;
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

    public function getFirstResponseMessageContent(): string
    {
        return $this->getFirstResponseMessage()->content;
    }

}
