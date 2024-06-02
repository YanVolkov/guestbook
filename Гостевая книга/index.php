<?php 

session_start();
require_once 'config.php';
require_once 'functions.php';

if($_POST['submit']){
    $stmt = addPost($_POST['name'], $_POST['email'], $_POST['msg']);

    if($stmt){
        $_SESSION['res'] = $stmt;
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }else{
        $_SESSION['name'] = clearDataClient($_POST['name']);
        $_SESSION['email'] = clearDataClient($_POST['email']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

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
        <div class="form-title">
            Гостевая книга
        </div>
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
        <div class="msgblock"></div>
            <?php
            $commentsPerPage = 5; // Количество комментариев на одной странице
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Установим начальное значение 1, если параметр не задан
            $start = ($page - 1) * $commentsPerPage; // Вычисляем смещение для SQL запроса
            $stmt = "SELECT id, name, email, post, LEFT(date, 16) AS date FROM post  ORDER BY date DESC LIMIT  $start, $commentsPerPage"; // Подготовим SQL запрос
            $result = $pdo->query($stmt); // Выполним запрос к базе данных
            $posts = $result->fetchAll();
            if(is_array($posts) && !empty($posts)){
                foreach($posts as $post){
                    ?>
                    <div class="msg_container">
                        <div class="msg_header">
                            <b><?php echo clearDataClient($post['name'])?> </b><?php echo clearDataClient($post['email'])?>
                        </div>
                        <div class="msg_body">
                            <?php echo nl2br(clearDataClient($post['post']))?>
                        </div>
                        <div class="msg_footer">
                            Коментарий добавлен: <?php echo $post ['date']?><b>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "Нет сообщений для отображения.";
            }
            // Получим общее количество комментариев
            $totalComments = $pdo->query("SELECT COUNT(*) FROM post")->fetchColumn();
            $pages = ceil($totalComments / $commentsPerPage); // Вычислим количество страниц

            // Выведем ссылки для пагинации
            for ($i = 1; $i <= $pages; $i++) {
                // Проверим, является ли текущая страница активной
                $active = $page == $i ? 'style="font-weight: bold;"' : '';
                echo "<a href='?page=$i' $active>$i</a> ";
            }
            
            
        ?>
    </div>
    </form>
    <?php
    
    unset ($_SESSION['res']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);

    ?>
    <script src="Script.js"></script>
    <script src="sweetalert.min.js"></script>
    
</body>
</html>