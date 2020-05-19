<?php

namespace App\Enums;

use ReflectionClass;

abstract class Enum
{
    private static $constCacheArray = NULL;

    const __default = NULL;

    static function getElements()
    {
        $class	= new ReflectionClass(get_called_class());
        $constants = $class->getConstants();

        $elements = array_map(function ($constant) {
            return [
                'id'   => $constant,
                'name' => self::description($constant),
            ];
        }, $constants);

        return collect($elements);
    }

    static function hasKey($key) {
        $foundKey = false;

        try {
            $enumClassName = get_called_class();
            new $enumClassName($key);
            $foundKey = true;
        } finally {
            return $foundKey;
        }
    }

    public static function description($value)
    {
        $nominals = static::getNominals();

        if (isset($nominals[$value])) {
            return $nominals[$value];
        }

        return $value;
    }

    abstract public static function getNominals() : array;

    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }
}

