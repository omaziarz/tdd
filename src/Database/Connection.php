<?php


namespace App\Database;

use PDO;
use Throwable;

class Connection
{
    private static $instance;

    public static function getInstance() {
        if (is_null(self::$instance)) {
            try {
                $pdo = new PDO(
                    'mysql:dbname='.getenv('MYSQL_DATABASE').';host=database',
                    getenv('MYSQL_USER'),
                    getenv('MYSQL_PASSWORD'));
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