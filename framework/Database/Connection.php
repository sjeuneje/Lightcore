<?php

namespace Lightcore\Framework\Database;

use PDO;
use PDOException;

abstract class Connection
{
    protected string $dbname;
    protected string $host;
    protected string $username;
    protected string $password;
}