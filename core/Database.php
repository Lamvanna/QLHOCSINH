<?php
// core/Database.php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            
            try {
                // First try connecting directly to database
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                // If database does not exist (1049), connect without dbname and create it
                if ($e->getCode() == 1049) {
                    $dsnWithoutDb = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
                    $rootPdo = new PDO($dsnWithoutDb, $config['username'], $config['password'], $config['options']);
                    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    
                    // Now connect to the created database
                    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
                    self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                } else {
                    die("Database connection failed: " . $e->getMessage());
                }
            }
        }
        return self::$instance;
    }
}
