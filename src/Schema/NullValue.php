<?php declare(strict_types=1);

namespace OAS\Schema;

use JsonSerializable;

class NullValue implements JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        return null;
    }
}
