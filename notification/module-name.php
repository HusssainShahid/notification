<?php

namespace paid_api\notification;

class ModuleName
{
    private $name;
    public const MAX_LENGTH = 200;

    public function __construct(string $name)
    {
        if (strlen($name) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException("Module name cannot be longer than " . self::MAX_LENGTH . " characters", 3001);
        }
        $this->name = $name;
    }

    public function value(): string
    {
        return $this->name;
    }
}
