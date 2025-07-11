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