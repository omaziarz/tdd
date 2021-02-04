<?php

namespace App\Database;

use PDO;
use Throwable;

class Connection
{
    private static $instance;

    public static function getInstance(): PDO
    {
        if (is_null(self::$instance)) {
            try {
                $pdo = new PDO(
                    'mysql:dbname=tddtestoliwier;host=db4free.net',
                    'oliwier',
                    'oliwier123'
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

                self::$instance = $pdo;
            } catch (Throwable $throwable) {
                die('connection failed ' . $throwable . PHP_EOL);
            }
        }
        return self::$instance;
    }
}
