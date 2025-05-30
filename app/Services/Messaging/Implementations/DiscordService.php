<?php

namespace App\Services\Messaging\Implementations;

use App\Models\Message;
use App\Services\Messaging\Interfaces\MessageServiceInterface;

class DiscordService implements MessageServiceInterface
{
    public function sendMessage(Message $message): void
    {
        dump("Enviando mensaje de Discord a: " . implode(', ', json_decode($message->recipients)));
    }

    public function sendMassMessage(array $messages): void
    {
        foreach ($messages as $msg) {
            $this->sendMessage($msg);
        }
    }
}
