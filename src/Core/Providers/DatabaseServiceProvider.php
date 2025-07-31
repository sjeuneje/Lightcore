<?php

namespace Core\Providers;

use Core\Container;
use Core\Database\Connection;
use Core\Database\DB;
use Exception;

/**
 * Database services provider.
 *
 * Registers database connection and query builder components required for
 * data persistence and retrieval operations. This provider handles database
 * configuration, connection establishment, and service registration.
 *
 * Registered services:
 * - Database Connection (singleton)
 * - Query Builder Facade (DB)
 *
 * @package Core\Providers
 * @since 1.0.0
 *
 * @see Connection For database connection management
 * @see DB For query builder facade
 */
class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register database services into the container.
     *
     * @param Container $container The service container
     * @return void
     *
     * @throws Exception If database configuration is missing or invalid
     */
    public function register(Container $container): void
    {
        // Register database connection as singleton
        $container->singleton(Connection::class, function() {
            $config = [
                'DB_NAME' => $_ENV['DB_NAME'] ?? throw new Exception('DB_NAME not configured'),
                'DB_HOST' => $_ENV['DB_HOST'] ?? throw new Exception('DB_HOST not configured'),
                'DB_USER' => $_ENV['DB_USER'] ?? throw new Exception('DB_USER not configured'),
                'DB_PASS' => $_ENV['DB_PASS'] ?? '',
            ];

            $connection = new Connection($config);
            $connection->connect();

            return $connection;
        });
    }

    /**
     * Bootstrap database services.
     *
     * Initializes the database facade with the registered connection,
     * making the query builder available throughout the application
     * via the DB static facade.
     *
     * @param Container $container The service container
     * @return void
     *
     * @throws Exception If connection registration fails
     */
    public function boot(Container $container): void
    {
        $connection = $container->get(Connection::class);
        DB::setConnection($connection);
    }
}
