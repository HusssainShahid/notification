<?php
declare(strict_types=1);

namespace paid_api\notification;


class SeenAt
{
    private $date;

    public function __construct(\DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public function value(): \DateTimeImmutable
    {
        return $this->date;
    }
}
