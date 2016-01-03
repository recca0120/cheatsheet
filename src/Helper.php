<?php

namespace Recca0120\Cheatsheet;

class Helper
{
    /**
     * Returns HTML link to editor.
     *
     * @return string
     */
    public static function editorLink($file, $line = null)
    {
        if ($editor = self::editorUri($file, $line)) {
            $file = strtr($file, '\\', '/');
            if (preg_match('#(^[a-z]:)?/.{1,50}$#i', $file, $m) && strlen($file) > strlen($m[0])) {
                $file = '...'.$m[0];
            }
            $file = strtr($file, '/', DIRECTORY_SEPARATOR);

            return self::formatHtml('<a href="%" title="%">%<b>%</b>%</a>',
                $editor,
                $file.($line ? ":$line" : ''),
                rtrim(dirname($file), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR,
                basename($file),
                $line ? ":$line" : ''
            );
        } else {
            return self::formatHtml('<span>%</span>', $file.($line ? ":$line" : ''));
        }
    }

    /**
     * Returns link to editor.
     *
     * @return string
     */
    public static function editorUri($file, $line = null)
    {
        if (Cheatsheet::$editor && $file && is_file($file)) {
            return strtr(Cheatsheet::$editor, ['%file' => rawurlencode($file), '%line' => $line ? (int) $line : '']);
        }
    }

    public static function formatHtml($mask)
    {
        $args = func_get_args();

        return preg_replace_callback('#%#', function () use (&$args, &$count) {
            return htmlspecialchars($args[++$count], ENT_IGNORE | ENT_QUOTES, 'UTF-8');
        }, $mask);
    }

    public static function sortByModifiers($data)
    {
        $modifiers = array_flip([
            'public static',
            'protected static',
            'private static',
            'final public static',
            'final protected static',
            'final private static',
            'public',
            'protected',
            'private',
            'final public',
            'final protected',
            'final private',
            'abstract public',
            'abstract protected',
            'abstract private',
        ]);

        usort($data, function ($a, $b) use ($modifiers) {
            if (is_object($a) === true) {
                $a = $a->toArray();
                $b = $b->toArray();
            }
            if ($modifiers[$a['modifiers']] == $modifiers[$b['modifiers']]) {
                return $a['name'] > $b['name'];
            }

            return $modifiers[$a['modifiers']] > $modifiers[$b['modifiers']];
        });

        return $data;
    }

    public static function varExport($var)
    {
        $export = trim(var_export($var, true));
        switch ($export) {
            case 'NULL':
            case 'TRUE':
            case 'FALSE':
                $export = strtolower($export);
                break;
        }

        return $export;
    }

    /**
     * Extract the classes in the given file.
     *
     * @param string $path The file to check
     *
     * @throws \RuntimeException
     *
     * @return array The found classes
     */
    public static function findClasses($path)
    {
        $extraTypes = PHP_VERSION_ID < 50400 ? '' : '|trait';
        if (defined('HHVM_VERSION') && version_compare(HHVM_VERSION, '3.3', '>=')) {
            $extraTypes .= '|enum';
        }
        try {
            $contents = @php_strip_whitespace($path);
            if (!$contents) {
                if (!file_exists($path)) {
                    throw new \Exception('File does not exist');
                }
                if (!is_readable($path)) {
                    throw new \Exception('File is not readable');
                }
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not scan for classes inside '.$path.": \n".$e->getMessage(), 0, $e);
        }
        // return early if there is no chance of matching anything in this file
        if (!preg_match('{\b(?:class|interface'.$extraTypes.')\s}i', $contents)) {
            return [];
        }
        // strip heredocs/nowdocs
        $contents = preg_replace('{<<<\s*(\'?)(\w+)\\1(?:\r\n|\n|\r)(?:.*?)(?:\r\n|\n|\r)\\2(?=\r\n|\n|\r|;)}s', 'null', $contents);
        // strip strings
        $contents = preg_replace('{"[^"\\\\]*+(\\\\.[^"\\\\]*+)*+"|\'[^\'\\\\]*+(\\\\.[^\'\\\\]*+)*+\'}s', 'null', $contents);
        // strip leading non-php code if needed
        if (substr($contents, 0, 2) !== '<?') {
            $contents = preg_replace('{^.+?<\?}s', '<?', $contents, 1, $replacements);
            if ($replacements === 0) {
                return [];
            }
        }
        // strip non-php blocks in the file
        $contents = preg_replace('{\?>.+<\?}s', '?><?', $contents);
        // strip trailing non-php code if needed
        $pos = strrpos($contents, '?>');
        if (false !== $pos && false === strpos(substr($contents, $pos), '<?')) {
            $contents = substr($contents, 0, $pos);
        }
        preg_match_all('{
            (?:
                 \b(?<![\$:>])(?P<type>class|interface'.$extraTypes.') \s++ (?P<name>[a-zA-Z_\x7f-\xff:][a-zA-Z0-9_\x7f-\xff:\-]*+)
               | \b(?<![\$:>])(?P<ns>namespace) (?P<nsname>\s++[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+(?:\s*+\\\\\s*+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+)*+)? \s*+ [\{;]
            )
        }ix', $contents, $matches);
        $classes = [];
        $namespace = '';
        for ($i = 0, $len = count($matches['type']); $i < $len; $i++) {
            if (!empty($matches['ns'][$i])) {
                $namespace = str_replace([' ', "\t", "\r", "\n"], '', $matches['nsname'][$i]).'\\';
            } else {
                $name = $matches['name'][$i];
                if ($name[0] === ':') {
                    // This is an XHP class, https://github.com/facebook/xhp
                    $name = 'xhp'.substr(str_replace(['-', ':'], ['_', '__'], $name), 1);
                } elseif ($matches['type'][$i] === 'enum') {
                    // In Hack, something like:
                    //   enum Foo: int { HERP = '123'; }
                    // The regex above captures the colon, which isn't part of
                    // the class name.
                    $name = rtrim($name, ':');
                }
                $classes[] = ltrim($namespace.$name, '\\');
            }
        }

        return $classes;
    }
}
