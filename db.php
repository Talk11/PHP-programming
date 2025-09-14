<?php
$host = 'localhost';
$dbname = 'number1';
$username = 'root'; // Логин для MySQL (по умолчанию в XAMPP — root)
$password = ''; // Пароль (по умолчанию в XAMPP — пустой)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit;
}
?>