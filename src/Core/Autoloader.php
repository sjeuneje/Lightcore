<?php

namespace Core;

/**
 * PSR-4 compliant autoloader for the framework.
 *
 * Handles automatic class loading with proper namespace-to-directory mapping
 * and provides robust error handling for missing classes.
 */
class Autoloader
{
    /**
     * Namespace to directory mappings
     */
    private static array $namespaceMap = [
        'App\\' => '../app/',
        'Core\\' => '../src/Core/',
    ];

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
     * @param string $fullyQualifiedClassName The complete namespace and class name
     * @throws \Exception When the class file cannot be found
     */
    public static function load(string $fullyQualifiedClassName): void
    {
        $normalizedClassName = self::normalizeClassName($fullyQualifiedClassName);
        $filePath = self::resolveFilePath($normalizedClassName);

        if (!file_exists($filePath)) {
            self::logAutoloadFailure($fullyQualifiedClassName, $normalizedClassName, $filePath);
            throw new \Exception("Class file not found: {$fullyQualifiedClassName}");
        }

        require_once $filePath;
    }

    /**
     * Add a new namespace mapping.
     *
     * @param string $namespace The namespace prefix (with trailing backslash)
     * @param string $directory The directory path (with trailing slash)
     */
    public static function addNamespace(string $namespace, string $directory): void
    {
        self::$namespaceMap[$namespace] = $directory;
    }

    /**
     * Normalize class name to handle case inconsistencies and common issues.
     */
    private static function normalizeClassName(string $className): string
    {
        // Handle lowercase 'app\' -> 'App\'
        if (str_starts_with($className, 'app\\')) {
            return 'App\\' . substr($className, 4);
        }

        // Handle forward slashes (if any)
        $className = str_replace('/', '\\', $className);

        return $className;
    }

    /**
     * Resolve the file path for a given fully qualified class name.
     */
    private static function resolveFilePath(string $className): string
    {
        // Try each namespace mapping
        foreach (self::$namespaceMap as $namespace => $directory) {
            if (str_starts_with($className, $namespace)) {
                $relativePath = substr($className, strlen($namespace));
                $relativePath = str_replace('\\', '/', $relativePath);
                return $directory . $relativePath . '.php';
            }
        }

        // Default fallback for unmapped namespaces
        $relativePath = str_replace('\\', '/', $className);
        return '../src/' . $relativePath . '.php';
    }

    /**
     * Log detailed information when autoloading fails.
     */
    private static function logAutoloadFailure(string $original, string $normalized, string $filePath): void
    {
        $logMessage = sprintf(
            "Autoloader failure:\n  Original: %s\n  Normalized: %s\n  File path: %s\n  File exists: %s",
            $original,
            $normalized,
            $filePath,
            file_exists($filePath) ? 'YES' : 'NO'
        );

        error_log($logMessage);

        // Debug: List available namespace mappings
        error_log("Available namespace mappings: " . print_r(self::$namespaceMap, true));
    }

    /**
     * Get all registered namespace mappings (useful for debugging).
     */
    public static function getNamespaceMap(): array
    {
        return self::$namespaceMap;
    }

    /**
     * Check if a namespace is registered.
     */
    public static function hasNamespace(string $namespace): bool
    {
        return isset(self::$namespaceMap[$namespace]);
    }
}
