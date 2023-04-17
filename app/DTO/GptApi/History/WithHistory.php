<?php

namespace App\DTO\GptApi\History;

interface WithHistory
{
    public function getHistoryContent(): string;

    public function getHistoryLabel(): string;
}
