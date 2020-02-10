<?php

namespace TikTok;

use PDO;

class Database
{
    /**
     * Connection variable
     *
     * @var Object
     */
    public static $connection;

    /**
     * Database name
     *
     * @var string
     */
    private $db;

    /**
     * Host name
     *
     * @var string
     */
    private $host;

    /**
     * Database port
     *
     * @var integer
     */
    private $port;

    /**
     * Database user name
     *
     * @var string
     */
    private $user;

    /**
     * Database password
     *
     * @var string
     */
    private $password;

    public function __construct() {
        $this->db       = $_ENV['DB_NAME'];
        $this->host     = $_ENV['DB_HOST'];
        $this->port     = $_ENV['DB_PORT'];
        $this->user     = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];

        self::$connection = new PDO(
            "mysql:host=$this->host;port=$this->port", 
            $this->user, 
            $this->password
        );
        self::$connection->setAttribute(
            PDO::ATTR_ERRMODE, 
            PDO::ERRMODE_EXCEPTION
        );
    }

    /**
     * Database init
     */
    public function initialize() : void {
        try {
            $this->createDb();

            self::$connection->exec("use $this->db");
            self::$connection->exec("SET NAMES utf8mb4");
    
            $this->createUsersTable();
            $this->createVideosTable();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Schema for db creation
     */
    private function createDb() : void {
        $sql = 
            "CREATE DATABASE IF NOT EXISTS $this->db 
             CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

        self::$connection->exec($sql);
    }

    /**
     * Schema for 'users' table
     */
    private function createUsersTable() : void {
        $sql = 
            "CREATE TABLE IF NOT EXISTS users (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci UNIQUE KEY,
                full_name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                description VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                thumbnail VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                verified TINYINT(2) NOT NULL DEFAULT 0,
                following INT(10) NOT NULL DEFAULT 0,
                fans VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
            );";

        self::$connection->exec($sql);
    }

    /**
     * Schema for 'videos' table
     */
    private function createVideosTable() : void {
        $sql = 
            "CREATE TABLE IF NOT EXISTS videos (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                video_id VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci UNIQUE KEY,
                name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                url VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                comments INT(10) NOT NULL DEFAULT 0,
                duration DECIMAL(12,2) NOT NULL DEFAULT 0
            );";

        self::$connection->exec($sql);
    }
}