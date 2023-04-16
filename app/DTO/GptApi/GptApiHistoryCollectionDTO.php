<?php

namespace App\DTO\GptApi;

use App\DTO\ExtendedData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class GptApiHistoryCollectionDTO extends ExtendedData
{
    public function __construct(

        #[DataCollectionOf(GptApiHistoryDTO::class)]
        public DataCollection $actions,
    )
    {
    }
}
