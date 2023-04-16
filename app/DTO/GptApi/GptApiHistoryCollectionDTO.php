<?php

namespace App\DTO\GptApi;

use App\DTO\ExtendedCollectionData;
use App\DTO\ExtendedData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class GptApiHistoryCollectionDTO extends ExtendedCollectionData
{
    public function __construct(

        #[DataCollectionOf(GptApiHistoryDTO::class)]
        public DataCollection $items,
    )
    {}
}
