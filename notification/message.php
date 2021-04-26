<?php

namespace paid_api\notification;

class Message
{
    private $message;
    public const MAX_LENGTH = 1024;

    public function __construct(\NonEmptyString $message)
    {
        if (strlen($message->value()) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException("Notification message cannot be longer than " . self::MAX_LENGTH . " characters", 30001);
        }
        $this->message = $message->value();
    }

    public function value(): string
    {
        return $this->message;
    }
}
