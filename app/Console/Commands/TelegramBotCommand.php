
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TelegramBotCommand extends Command
{
    protected $signature = 'telegram:start';
    protected $description = 'Start the Telegram bot listener';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Cosmic Stars Telegram bot is running...');
        $telegramService->startListening();
    }
}
