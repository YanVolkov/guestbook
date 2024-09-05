<?php

if (!class_exists('DatabaseConnection')) {
    class DatabaseConnection {
        private $host = '127.0.0.1';
        private $port = '3306';
        private $dbname = 'guestbook';
        private $charset = 'utf8mb4';
        private $username = 'root';
        private $password = 'root';
        private $pdo;

        public function __construct() {
            $this->connect();
        }

        private function connect() {
            try {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
                $this->pdo = new PDO($dsn, $this->username, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }

        public function getPdo() {
            return $this->pdo;
        }
    }
}

?>