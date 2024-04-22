<?php declare(strict_types=1);

namespace OAS\Schema;

enum Type: string
{
    case NULL = 'null';
    case STRING = 'string';
    case NUMBER = 'number';
    case INTEGER = 'integer';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
}