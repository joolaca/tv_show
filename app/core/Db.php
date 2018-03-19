<?php
class Db
{
    private $connection;
    private static $instance;
    private $host = DB_HOST;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $database = DB_NAME;

    private function __construct()
    {

        if (!defined('DB_HOST')) {
            require APP . '/config/config.php';
        }

        try {
            $this->connection  = new \PDO(
                "mysql:host=$this->host;dbname=$this->database",
                $this->username,
                $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "\n\n\n Pls set /app/config/config.php  \n\n\n";
            throw new Exception($e->getMessage());
        }
    }

    /*
    Get an instance of the Database
    @return Instance
    */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone()
    {
    }
    // Get mysql pdo connection
    public function getConnection()
    {
        return $this->connection;
    }
}

