<?php

include 'config.php';

if (!class_exists('guestbook')){
class guestbook {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Очистка данных
    private function clearData($var) {
        return trim(htmlspecialchars($var, ENT_QUOTES));
    }

    // Добавление сообщения
    public function addPost($name, $email, $msg) {
        global $pdo;
        $name = $this->clearData($name);
        $email = $this->clearData($email);
        $msg = $this->clearData($msg);
        $stmt = $this->pdo->prepare("INSERT INTO post (name, email, post) VALUES (:name, :email, :msg)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':msg', $msg);
        $stmt->execute();
    }

    // Выборка сообщений
    public function selectAll() {
        global $pdo;
        $stmt = $this->pdo->query('SELECT id, name, email, post, LEFT(date, 16) AS date FROM post ORDER BY date DESC');
        return $stmt->fetchAll();
    }
}
}

// Использование
$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getPdo();

$guestBook = new guestbook($pdo);

// Пример добавления сообщения
// $guestBook->addPost('Имя', 'email@example.com', 'Сообщение');

// Пример выборки всех сообщений
// $posts = $guestBook->selectAll();

?>