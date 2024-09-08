<?php

session_start();
require_once 'config.php';
require_once 'functions.php';

class guestbook {
    private $pdo;
    private $commentsPerPage = 5;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function handleRequest() {
        if ($_POST['submit']) {
            $this->processForm();
        }
    }

    private function processForm() {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $msg = $_POST['msg'];

        $stmt = $this->addPost($name, $email, $msg);

        if ($stmt) {
            $_SESSION['res'] = $stmt;
        } else {
            $_SESSION['name'] = $this->clearDataClient($name);
            $_SESSION['email'] = $this->clearDataClient($email);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    private function addPost($name, $email, $msg) {
        // Implement the logic to add a post to the database
        // Return true on success or false on failure
    }

    private function clearDataClient($data) {
        // Implement the logic to sanitize user input
        return htmlspecialchars(trim($data), ENT_QUOTES);
    }

    public function displayPosts() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $start = ($page - 1) * $this->commentsPerPage;
        $stmt = "SELECT id, name, email, post, LEFT(date, 16) AS date FROM post ORDER BY date DESC LIMIT $start, $this->commentsPerPage";
        $result = $this->pdo->query($stmt);
        $posts = $result->fetchAll();

        if (is_array($posts) && !empty($posts)) {
            foreach ($posts as $post) {
                echo '<div class="msg_container">';
                echo '<div class="msg_header"><b>' . $this->clearDataClient($post['name']) . '</b> ' . $this->clearDataClient($post['email']) . '</div>';
                echo '<div class="msg_body">' . nl2br($this->clearDataClient($post['post'])) . '</div>';
                echo '<div class="msg_footer">Коментарий добавлен: ' . $post['date'] . '</div>';
                echo '</div>';
            }
        } else {
            echo "Нет сообщений для отображения.";
        }

        $this->displayPagination($page);
    }

    private function displayPagination($currentPage) {
        $totalComments = $this->pdo->query("SELECT COUNT(*) FROM post")->fetchColumn();
        $pages = ceil($totalComments / $this->commentsPerPage);

        for ($i = 1; $i <= $pages; $i++) {
            $active = $currentPage == $i ? 'style="font-weight: bold;"' : '';
            echo "<a href='?page=$i' $active>$i</a> ";
        }
    }
}

$guestBook = new guestbook($pdo);
$guestBook->handleRequest();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Гостевая книга</title>
</head>
<body>
    <form action="index.php" method="POST" id="gb_container">
        <div class="login-form">
            <div class="form-title">Гостевая книга</div>
            <div class="form-input">
                <label for="username">Имя</label>
                <input type="text" id="username" name="name" required>
            </div>
            <div class="form-input">
                <label for="E-Mail">E-Mail</label>
                <input type="text" id="E-Mail" name="email" required>
            </div>
            <div class="captcha">
                <label for="captcha-input">Введите капчу</label>
                <div class="preview"></div>
                <div class="captcha-form">
                    <input type="text" id="captcha-form" placeholder="Введите капчу" required>
                    <button class="captcha-refresh">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
                <div class="form-input">
                    <button id="login-btn">Проверить капчу</button>
                </div>
            </div>
            <div class="msg">
                <textarea class="msg" name="msg" required></textarea>
            </div>
            <div class="send">
                <input type="submit" name="submit" value="Отправить" />
            </div>
            <div class="msgblock">
                <?php $guestBook->displayPosts(); ?>
            </div>
        </div>
    </form>
    
    <script src="Script.js"></script>
    <script src="sweetalert.min.js"></script>
</body>
</html>