<?php

namespace Recca0120\Cheatsheet\Parser;

use Recca0120\Cheatsheet\Helper;
use Reflection;
use ReflectionClass;

class Doc extends Fluent
{
    protected $reflection;

    protected $declare = false;

    public function __construct($class, $declare = false)
    {
        if (($class instanceof ReflectionClass) === false) {
            $this->reflection = new ReflectionClass($class);
        } else {
            $this->reflection = $class;
        }

        $this->declare = $declare;
        $this->boot();
    }

    protected function boot()
    {
        $this->attributes = [
            'name'      => $this->reflection->getName(),
            'file'      => $this->reflection->getFileName(),
            'line'      => $this->reflection->getStartLine(),
            'modifiers' => implode(' ', Reflection::getModifierNames($this->reflection->getModifiers())),
        ];

        if ($this->declare === false) {
            $this->attributes = array_merge($this->attributes, [
                'parent'     => $this->reflection->getParentClass(),
                'constants'  => $this->getConstants(),
                'properties' => $this->getProperties(),
                'methods'    => $this->getMethods(),
            ]);
        }

        return $this;
    }

    protected function getConstants()
    {
        $collect = [];
        $data = $this->reflection->getConstants();
        foreach ($data as $key => $value) {
            $collect[] = Constant::factory(compact('key', 'value'));
        }

        return $collect;
    }

    protected function getProperties()
    {
        $collect = [];
        $data = $this->reflection->getProperties();
        $defaults = $this->reflection->getDefaultProperties();
        foreach ($data as $value) {
            $collect[] = Property::factory($value, $defaults);
        }
        $collect = Helper::sortByModifiers($collect);

        return $collect;
    }

    protected function getMethods()
    {
        $collect = [];
        $data = $this->reflection->getMethods();
        foreach ($data as $value) {
            $collect[] = Method::factory($value);
        }
        $collect = Helper::sortByModifiers($collect);

        return $collect;
    }

    public static function factory($class, $declare = false)
    {
        return new static($class, $declare);
    }
}
