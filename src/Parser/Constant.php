<?php

namespace Recca0120\Cheatsheet\Parser;

class Constant extends Fluent
{
    public function __construct(array $attribute)
    {
        $this->attributes = $attribute;
    }

    public static function factory(array $attribute)
    {
        return new static($attribute);
    }
}
