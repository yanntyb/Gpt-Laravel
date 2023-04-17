<?php

namespace App\Service;

class ConsoleService
{
    /**
     * Clear console output
     * @return void
     */
    public function clearConsole(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
    }
}
