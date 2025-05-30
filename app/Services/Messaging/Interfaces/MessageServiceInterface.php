<?php

namespace App\Services\Messaging\Interfaces;

use App\Models\Message;

interface MessageServiceInterface
{
    public function sendMessage(Message $message): void;
    public function sendMassMessage(array $messages): void;
}
