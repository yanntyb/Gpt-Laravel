<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use Iterator;
use ReturnTypeWillChange;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

abstract class ExtendedCollectionData extends ExtendedData implements Iterator
{
    protected int $itemIndex;
    protected DataCollection $items;

    abstract public function __construct(DataCollection $items);

    #[ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->itemIndex = 0;
    }

    #[ReturnTypeWillChange]
    public function next(): void
    {
        $this->itemIndex++;
    }

    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->itemIndex;
    }

    #[ReturnTypeWillChange]
    public function current()
    {
        $keys = array_keys($this->items->items());
        return $this->items->items()[$keys[$this->itemIndex]];
    }

    #[ReturnTypeWillChange]
    public function valid(): bool
    {
        return $this->itemIndex < $this->items->count();
    }

    /**
     * Return collection of items
     * @return Collection
     */
    public function items(): Collection
    {
        return collect($this->items->items());
    }

    /**
     * Return true if items contain a certain pair of key values
     * If array is passed on first parameter then methode act like it does with ->where([]) on collection
     * @param  array|string  $keys
     * @param  mixed  $values
     * @return bool
     */
    public function contain(array|string $keys, mixed $values = ''): bool
    {
        if (is_array($keys)) {
            $filteredItem = $this->items();
            foreach ($keys as $key => $value) {
                $filteredItem = $filteredItem->where($key, $value);
            }

            return (bool)$filteredItem->count();
        }
        return (bool)$this->items()->firstWhere($keys, $values);
    }

    /**
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return $this->items()->isNotEmpty();
    }

    /**
     * @param  string  $key
     * @return Collection
     */
    public function pluck(string $key): Collection
    {
        return $this->items()->pluck($key);
    }

    /**
     * Count items
     * @return int
     */
    public function count(): int
    {
        return $this->items()->count();
    }
}
