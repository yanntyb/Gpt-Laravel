<?php

namespace App\DTO\GptApi\Response;

use App\DTO\ExtendedData;
use App\DTO\GptApi\GptApiMessageDTO;
use App\DTO\GptApi\History\WithHistory;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class GptApiResponseDTO extends ExtendedData implements WithHistory
{
    public static string $historyProperty = 'response';

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

    /**
     * @return GptApiMessageDTO
     */
    public function getFirstResponseMessage(): GptApiMessageDTO
    {
        return $this->getResponsesMessage()[0]?->message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->getFirstResponseMessage()->content;
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
        return $this->getFirstResponseMessage()->role;
    }


}
