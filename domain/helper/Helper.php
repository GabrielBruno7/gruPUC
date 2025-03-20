<?php

namespace domain\helper;

use Ramsey\Uuid\Uuid;

class Helper
{
    public static function uuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}

