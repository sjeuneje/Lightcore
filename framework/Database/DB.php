<?php

namespace Lightcore\Framework\Database;

use Exception;
use Lightcore\Framework\IoC\Container;
use PDO;

/**
 * @method static table(string $string)
 */
class DB
{
    private static ?QueryBuilder $instance = null;
    private static Container $container;

    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    /**
     * @throws Exception
     */
    public static function getInstance(): ?QueryBuilder
    {
        if (!self::$instance) {
            $pdo = self::$container->resolve(PDO::class);
            self::$instance = new QueryBuilder($pdo);
        }

        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        $instance = self::getInstance();

        return call_user_func_array([$instance, $method], $args);
    }
}