<?php

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        echo "<pre>";

        foreach ($vars as $var) {
            var_dump($var);
        }

        echo "</pre>";
        die();
    }
}

if (!function_exists('sanitize')) {
    function sanitize($value)
    {
        if (is_string($value)) {
            $value = trim(
                mb_convert_encoding(
                    strip_tags(
                        preg_replace(
                            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',
                            '',
                            $value
                        )
                    ),
                    'UTF-8',
                    'UTF-8'
                )
            );
        }

        return $value;
    }
}