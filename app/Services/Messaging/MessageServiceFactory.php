<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Interfaces\MessageServiceInterface;
use App\Services\Messaging\Implementations\TelegramService;
use App\Services\Messaging\Implementations\WhatsappService;
use App\Services\Messaging\Implementations\DiscordService;
use App\Services\Messaging\Implementations\SlackService;

class MessageServiceFactory
{
    public function make(string $platform): MessageServiceInterface
    {
        return match ($platform) {
            'Telegram' => new TelegramService(),
            'Whatsapp' => new WhatsappService(),
            'Discord' => new DiscordService(),
            'Slack' => new SlackService(),
            default => throw new \Exception("Unsupported platform: $platform"),
        };
    }
}
