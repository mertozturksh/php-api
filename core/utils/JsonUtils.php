<?php

namespace Core\Utils;
use Exception;

class JsonUtils {
    /**
     * Converts Array or Json object to Json string.
     * 
     * @param mixed $data => Array or Object
     * @return string Json formatted string
     */
    public static function jsonEncode(string $data)
    {
        $result = json_encode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON encode error: ' . json_last_error_msg());
        }
        return $result;
    }
    /**
     * Converts string data to JSON object.
     * 
     * @param string $jsonString => string as Json format
     * @param bool $asArray => if true returns array else returns object
     * @return mixed
     */
    public static function jsonDecode(string $jsonString, bool $asArray = false)
    {
        $result = json_decode($jsonString, $asArray);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }
        return $result;
    }
}