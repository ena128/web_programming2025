<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config {

    // Check for environment variable, otherwise use default
    public static function get_env($name, $default = null){
        return isset($_ENV[$name]) && trim($_ENV[$name]) !== "" ? $_ENV[$name] : $default;
    }

    public static function DB_NAME() {
        return self::get_env("DB_NAME");
    }

    public static function DB_PORT() {
        return self::get_env("DB_PORT");
    }

    public static function DB_USER() {
        return self::get_env("DB_USER");
    }

    public static function DB_PASSWORD() {
        return self::get_env("DB_PASSWORD");
    }

    public static function DB_HOST() {
        return self::get_env("DB_HOST");
    }

   public static function JWT_SECRET()
    {
        return 'enaslipicevic0303';
    }
}

class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . Config::DB_HOST() . 
                       ";port=" . Config::DB_PORT() . 
                       ";dbname=" . Config::DB_NAME() . 
                       ";charset=utf8mb4";

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ];

                self::$connection = new PDO($dsn, Config::DB_USER(), Config::DB_PASSWORD(), $options);

            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>
