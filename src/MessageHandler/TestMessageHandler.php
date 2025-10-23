<?php

namespace App\MessageHandler;

use App\Scheduler\TestMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestMessageHandler
{
    public function __invoke(TestMessage $message): void
    {
        echo "✅ it works\n";
    }
}