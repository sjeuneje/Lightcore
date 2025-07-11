<?php

namespace Core;

/**
 * PSR-4 compliant autoloader for the framework.
 */
class Autoloader
{
    /**
     * Root directory containing source files.
     */
    private static string $sourceRoot = "../src/";

    /**
     * PHP file extension.
     */
    private static string $extension = ".php";

    /**
     * Register the autoloader with PHP's SPL autoloader stack.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    /**
     * Convert PSR-4 namespace to file path and include the class file.
     *
     * Transforms namespace separators to directory separators and attempts
     * to load the corresponding PHP file from the source root.
     *
     * @param string $fullyQualifiedClassName The complete namespace and class name
     * @throws \Exception When the class file cannot be found
     */
    public static function load(string $fullyQualifiedClassName): void
    {
        $relativePath = str_replace("\\", "/", $fullyQualifiedClassName);
        $filePath = self::$sourceRoot . $relativePath . self::$extension;

        if (!file_exists($filePath)) {
            error_log("Autoloader: Failed to locate class file at {$filePath}");
            throw new \Exception("Class file not found: {$fullyQualifiedClassName}");
        }

        require_once $filePath;
    }
}
