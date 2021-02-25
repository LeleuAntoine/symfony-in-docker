<?php

namespace App\MessageHandler;

use App\Message\TestMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class TestMessageHandler implements MessageHandlerInterface
{
    public function __invoke(TestMessage $message)
    {
        dump($message);
    }
}
