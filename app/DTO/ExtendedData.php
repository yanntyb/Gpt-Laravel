<?php

namespace App\DTO;

use DeepCopy\Exception\PropertyException;
use Exception;
use Spatie\LaravelData\Data;

class ExtendedData extends Data
{


    /**
     * @param  string  $propertyName
     * @param  mixed  $value
     * @return self
     * @throws Exception
     */
    public function changePropertyValue(string $propertyName, mixed $value): static
    {
        if (!property_exists($this, $propertyName)) {
            throw new Exception(
                'La propriété ' . $propertyName . ' n\'existe pas pour la class ' . self::class
            );
        }

        $clone = clone $this;
        $clone->{$propertyName} = $value;
        return $clone;
    }
}
