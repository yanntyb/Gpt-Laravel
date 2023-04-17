<?php

namespace App\DTO\GptApi\History;

use App\DTO\ExtendedCollectionData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class GptApiHistoryCollectionDTO extends ExtendedCollectionData
{
    public function __construct(

        #[DataCollectionOf(GptApiHistoryDTO::class)]
        public DataCollection $items,
    )
    {}


    /**
     * @return Collection<string>
     */
    public function getHistoryContent(): Collection
    {
        return $this->items()->map(
            function (GptApiHistoryDTO $item) {
                $filledProperty = $item->request ? 'request' : 'response';

                /** @var WithHistory $realProperty */
                $realProperty = $item->{$filledProperty};

                return GptApiHistoryContentDTO::from([
                    'role' => ucfirst($realProperty->getHistoryLabel()),
                    'message' => $realProperty->getHistoryContent()
                ]);
            }
        );
    }
}
