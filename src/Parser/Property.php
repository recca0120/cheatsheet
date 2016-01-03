<?php

namespace Recca0120\Cheatsheet\Parser;

use Reflection;
use Reflector;

class Property extends Fluent
{
    protected $reflection;

    public function __construct(Reflector $reflection, $defaults = null)
    {
        $this->reflection = $reflection;
        $this->defaults = $defaults;
        $this->boot();
    }

    protected function boot()
    {
        $name = $this->reflection->getName();

        $this->attributes = [
            'name'      => $name,
            'declare'   => Doc::factory($this->reflection->getDeclaringClass(), true)->toArray(),
            'comment'   => $this->reflection->getDocComment(),
            'modifiers' => implode(' ', Reflection::getModifierNames($this->reflection->getModifiers())),
        ];

        return $this;
    }

    public static function factory(Reflector $reflection, $defaults = null)
    {
        return new static($reflection, $defaults);
    }
}
