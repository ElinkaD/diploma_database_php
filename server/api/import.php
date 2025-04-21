<?php
require_once '../db/connect.php';

$uploadDir = '../uploads/';
$allowedImports = [
    'group_track' => 'GroupTrackImporter',
    // добавь сюда другие импорты позже
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['csv']) || !isset($_POST['type'])) {
        http_response_code(400);
        echo 'Неверный запрос';
        exit;
    }

    $type = $_POST['type'];
    $fileTmp = $_FILES['csv']['tmp_name'];
    $targetFile = $uploadDir . basename($_FILES['csv']['name']);

    if (!move_uploaded_file($fileTmp, $targetFile)) {
        http_response_code(500);
        echo 'Не удалось сохранить файл';
        exit;
    }

    require_once "../classes/$allowedImports[$type].php";

    $className = $allowedImports[$type];
    $importer = new $className($pdo, $targetFile);

    try {
        $importer->import();
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Ошибка импорта: ' . $e->getMessage();
    }
}
