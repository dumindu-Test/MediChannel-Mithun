<?php
/**
 * MySQL Database connection and utility functions
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $host = defined('MYSQL_HOST') ? MYSQL_HOST : 'localhost';
            $port = defined('MYSQL_PORT') ? MYSQL_PORT : '3306';
            $dbname = defined('MYSQL_DATABASE') ? MYSQL_DATABASE : 'eChannelling';
            $username = defined('MYSQL_USERNAME') ? MYSQL_USERNAME : 'root';
            $password = defined('MYSQL_PASSWORD') ? MYSQL_PASSWORD : '';
            
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
        } catch (Exception $e) {
            error_log("MySQL connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Query failed: " . $e->getMessage());
            throw new Exception("Query execution failed");
        }
    }
    
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Execute failed: " . $e->getMessage());
            throw new Exception("Query execution failed");
        }
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
}
?>