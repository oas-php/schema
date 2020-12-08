<?php declare(strict_types=1);

namespace OAS\Schema;

class ConstNull implements \JsonSerializable
{
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return null;
    }
}
