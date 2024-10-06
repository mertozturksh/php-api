<?php

namespace Core\Utils;

class StringUtils {

    /**
     * Sanitizes the given input by stripping HTML and PHP tags and converting special characters to HTML entities.
     * 
     * @param string $input The input string to be sanitized.
     * @return string The sanitized string, safe for output in HTML contexts.
     */
    public static function sanitize($input)
    {
        return htmlspecialchars(strip_tags($input));
    }

    /**
     * Generates a random string of a given length.
     * 
     * @param int $length The desired length of the generated string.
     * @return string The generated random string.
     */
    public static function generateRandomString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }

}