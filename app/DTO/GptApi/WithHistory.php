<?php

namespace App\DTO\GptApi;

interface WithHistory
{
    public function getHistoryContent(): string;

    public function getHistoryLabel(): string;
}
