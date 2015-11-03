<?php

namespace Recca0120\Cheatsheet\Parser;

use Reflection;
use Reflector;

class Method extends Fluent
{
    protected $reflection;

    public function __construct(Reflector $reflection)
    {
        $this->reflection = $reflection;

        $this->boot();
    }

    protected function boot()
    {
        $this->attributes = [
            'name' => $this->reflection->getName(),
            'declare' => Doc::factory($this->reflection->getDeclaringClass(), true)->toArray(),
            'parameters' => $this->getParameters(),
            'comment' => $this->reflection->getDocComment(),
            'modifiers' => implode(' ', Reflection::getModifierNames($this->reflection->getModifiers())),
            'file' => $this->reflection->getFileName(),
            'line' => $this->reflection->getStartLine(),
        ];

        return $this;
    }

    protected function getParameters()
    {
        $collect = [];
        $data = $this->reflection->getParameters();
        foreach ($data as $value) {
            $collect[] = Parameter::factory($value);
        }

        return $collect;
    }

    public static function factory(Reflector $reflection)
    {
        return new static($reflection);
    }
}
