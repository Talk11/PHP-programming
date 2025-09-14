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

// Проверяем, передан ли ID задачи
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$taskId = $_GET['id'];

// Получаем задачу
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->execute(['id' => $taskId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: index.php');
    exit;
}

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'pending';

    if (!empty($title)) {
        $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id");
        $stmt->execute(['title' => $title, 'description' => $description, 'status' => $status, 'id' => $taskId]);
        header('Location: index.php');
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
    <title>Редактировать задачу</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Редактировать задачу</h1>
    <form method="POST">
        <label>Заголовок: <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>"></label><br>
        <label>Описание: <textarea name="description"><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea></label><br>
        <label>Статус:
            <select name="status">
                <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>В процессе</option>
                <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Выполнено</option>
            </select>
        </label><br>
        <button type="submit">Сохранить</button>
    </form>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <p><a href="index.php">Вернуться к списку задач</a></p>
</body>
</html>