<?php

namespace App\DTO;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class GptApiMessageDTO extends ExtendedData
{
    public function __construct(
        public string $role,
        public string $content,
    ) {}
}
