<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>


<?php
require 'db.php';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');

    // Простая валидация
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description) VALUES (:title, :description)");
        $stmt->execute(['title' => $title, 'description' => $description]);
        header('Location: index.php'); // Перенаправление, чтобы избежать повторной отправки формы
        exit;
    } else {
        $error = "Заголовок задачи обязателен!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">

    <form method="GET">
    <label>Фильтр по статусу:
        <select name="status">
            <option value="">Все</option>
            <option value="pending">В процессе</option>
            <option value="completed">Выполнено</option>
        </select>
    </label>
    <button type="submit">Фильтровать</button>
</form>


    <title>Список задач</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; }
        .form { margin-bottom: 20px; }
        .task { border: 1px solid #ccc; padding: 10px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Список задач</h1>

    <!-- Форма для добавления задачи -->
    <div class="form">
        <form method="POST">
            <label>Заголовок: <input type="text" name="title"></label><br>
            <label>Описание: <textarea name="description"></textarea></label><br>
            <button type="submit">Добавить задачу</button>
        </form>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>

    <!-- Список задач -->
    <h2>Задачи</h2>
    <?php
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tasks) {
        foreach ($tasks as $task) {
            echo "<div class='task'>";
            echo "<h3>" . htmlspecialchars($task['title']) . "</h3>";
            echo "<p>" . htmlspecialchars($task['description'] ?? '') . "</p>";
            echo "<p><small>Создано: " . $task['created_at'] . "</small></p>";
            echo "</div>";
            echo "<p><a href='edit.php?id={$task['id']}'>Редактировать</a></p>";
            echo "<p><a href='delete.php?id={$task['id']}' onclick='return confirm(\"Вы уверены?\");'>Удалить</a></p>";
        }
    } else {
        echo "<p>Задач пока нет.</p>";
    }
    ?>
    <p><a href="logout.php">Выйти</a></p>
</body>
</html>