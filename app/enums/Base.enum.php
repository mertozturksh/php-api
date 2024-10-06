<?php

namespace App\Enums;

class BaseEnum {
    
    public static function getConstants()
    {
        $reflection = new \ReflectionClass(get_called_class());
        return $reflection->getConstants();
    }

    public static function getValues()
    {
        return array_values(self::getConstants());
    }

    public static function getKeys()
    {
        return array_keys(self::getConstants());
    }

    public static function isValidValue($value)
    {
        return in_array($value, self::getValues(), true);
    }

    public static function isValidKey($key)
    {
        return array_key_exists($key, self::getConstants());
    }
}