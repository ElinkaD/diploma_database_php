<?php
require_once '../db/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

$id_status = $_GET['id_status'] ?? null;
$id_track = $_GET['id_track'] ?? null;

if (!$id_status || !$id_track) {
    http_response_code(400);
    echo json_encode(['error' => 'Не переданы обязательные параметры: id_status и id_track']);
    exit;
}

try {
    $query = "SELECT calculate_academic_difference(:id_status, :id_track)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id_status' => $id_status, ':id_track' => $id_track]);
    $akadem_diff = $stmt->fetchColumn();
    echo $akadem_diff;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>
