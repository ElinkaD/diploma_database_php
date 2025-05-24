<?php
session_start();
require_once '../db/db_connect.php';

$login = $_POST['login'] ?? null;
$password = $_POST['password'] ?? null;

if (!$login || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Введите логин и пароль']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT s338859.auth_diploma(:login, :password)');
    $stmt->execute(['login' => $login, 'password' => $password]);

    $response = json_decode($stmt->fetchColumn(), true);

    if ($response['status'] === 'success') {
        $_SESSION['token'] = $response['token'];
        $_SESSION['login'] = $response['login'];
        $_SESSION['role'] = $response['role'];
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $response['result_message']]);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка на сервере']);
}
