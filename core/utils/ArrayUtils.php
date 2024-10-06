<?php

namespace Core\Utils;

class ArrayUtils {

    /**
     * Flattens a multi-dimensional array into a single level array.
     * 
     * @param array $array The multi-dimensional array.
     * @return array The flattened array.
     */
    public static function arrayFlatten(array $array)
    {
        $result = [];
        array_walk_recursive($array, function ($a) use (&$result) {
            $result[] = $a;
        });
        return $result;
    }
    
}