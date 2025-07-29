<?php
namespace App\Database;

use PDO;
use PDOException;

class ConnectionFactory {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                $host = 'localhost';  
                $db = 'coworking_space';    
                $user = 'root';       
                $pass = '';    

                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

                // PDO options for error handling, fetch mode, and prepared statement emulation
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch results as associative arrays
                    PDO::ATTR_EMULATE_PREPARES   => false,                   // Use native prepared statements
                ];

                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$connection;
    }
}
?>
