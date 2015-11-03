<?php

namespace Recca0120\Cheatsheet;

use Symfony\Component\Finder\Finder;

class Cheatsheet
{
    public static $editor = 'subl://open?url=file://%file&line=%line';

    public $autoloads = [

    ];

    public function setAutoloads($autoloads)
    {
        $this->autoloads = $autoloads;

        return $this;
    }

    public function findClasses()
    {
        $extraTypes = PHP_VERSION_ID < 50400 ? '' : '|trait';
        if (defined('HHVM_VERSION') && version_compare(HHVM_VERSION, '3.3', '>=')) {
            $extraTypes .= '|enum';
        }

        $classes = [];

        $iterator = Finder::create()
            ->files()
            ->exclude('Tests')
            ->name('/\.(php|inc|hh)$/')
            ->in($this->autoloads);

        foreach ($iterator as $file) {
            $findClasses = Helper::findClasses($file);
            if (count($findClasses) > 0) {
                foreach ($findClasses as $class) {
                    $split = explode('\\', $class);
                    if (count($split) == 1) {
                        $namespace = $split[0];
                    } else {
                        $namespace = implode('\\', [$split[0], $split[1]]);
                    }
                    $classes[trim($namespace, '\\')][] = $class;
                }
            }
        }

        return $classes;
    }

    public function make()
    {
        $classes = $this->findClasses();
        $namespaces = array_keys($classes);

        ob_start();
        require __DIR__.'/../resources/views/cheatsheet.php';

        return ob_get_clean();
    }
}
