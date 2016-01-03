<?php

namespace Recca0120\Cheatsheet\Parser;

use Recca0120\Cheatsheet\Helper;
use Reflection;
use ReflectionException;
use Reflector;

class Parameter extends Fluent
{
    protected $reflection;

    public function __construct(Reflector $reflection)
    {
        $this->reflection = $reflection;
        $this->boot();
    }

    protected function boot()
    {
        $default = null;
        $reference = false;
        $optional = false;
        $typeHint = null;

        try {
            $class = $this->reflection->getClass();
            if ($class) {
                $typeHint = Doc::factory('\\'.$class->getName(), true)->toArray();
            } elseif ($this->reflection->isArray() === true) {
                $typeHint = 'array';
            }
        } catch (ReflectionException $e) {
        }

        if ($this->reflection->isDefaultValueAvailable()) {
            $default = Helper::varExport($this->reflection->getDefaultValue());
        }

        if ($this->reflection->isPassedByReference()) {
            $reference = true;
        }

        if ($this->reflection->isOptional()) {
            $optional = true;
        }

        $this->attributes = [
            'name'      => $this->reflection->getName(),
            'position'  => $this->reflection->getPosition(),
            'typeHint'  => $typeHint,
            'default'   => $default,
            'reference' => $reference,
            'optional'  => $optional,
        ];

        return $this;
    }

    public static function factory(Reflector $reflection)
    {
        return new static($reflection);
    }
}
