<?php

namespace paid_api\notification;

class Subject
{
    private $subject;
    public const MAX_LENGTH = 500;

    public function __construct(string $subject)
    {
        if (strlen($subject) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException("Notifications subject cannot be longer than " . self::MAX_LENGTH . " characters", 301);
        }
        $this->subject = $subject;
    }

    public function value(): string
    {
        return $this->subject;
    }
}
