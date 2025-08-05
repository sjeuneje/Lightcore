<?php

namespace Core;

/**
 * Configuration manager with dot notation support and lazy loading.
 *
 * This class provides a simple interface for accessing and setting
 * configuration values using dot notation (e.g., 'database.host').
 * Configuration files are loaded lazily and cached in memory for
 * performance optimization.
 */
class Config
{
    /**
     * Path to the configuration directory
     *
     * @var string
     */
    protected static string $configDirectoryPath = BASE_PATH . "/config";

    /**
     * Cache for loaded configuration files
     *
     * @var array<string, array>
     */
    protected static array $loadedConfigs = [];

    /**
     * Get a configuration value using dot notation.
     *
     * Supports unlimited nesting levels (e.g., 'database.connections.mysql.host').
     * Configuration files are loaded on-demand and cached in memory.
     *
     * @param string $config Configuration key in dot notation
     * @param mixed $default Default value if configuration key is not found
     * @return mixed Configuration value or default value
     */
    public static function get(string $config, mixed $default = null): mixed
    {
        $parts = explode('.', $config);
        $file = array_shift($parts);

        self::$loadedConfigs[$file] ??= include self::$configDirectoryPath . "/{$file}.php";

        return array_reduce($parts, fn($carry, $key) => $carry[$key] ?? null, self::$loadedConfigs[$file]) ?? $default;
    }

    /**
     * Set a configuration value using dot notation.
     *
     * Creates nested array structure if it doesn't exist and updates
     * the cached configuration in memory.
     *
     * @param string $config Configuration key in dot notation
     * @param mixed $value Value to set
     * @return void
     */
    public static function set(string $config, mixed $value): void
    {
        $parts = explode('.', $config);
        $file = array_shift($parts);
        self::$loadedConfigs[$file] ??= [];

        $current = &self::$loadedConfigs[$file];
        foreach ($parts as $key) {
            $current = &$current[$key];
        }

        $current = $value;
    }
}
