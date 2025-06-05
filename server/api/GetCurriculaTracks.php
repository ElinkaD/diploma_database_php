<?php
require_once '../db/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

$id_status = $_GET['id_status'] ?? null;

if (!$id_status) {
    http_response_code(400);
    echo json_encode(['error' => 'Не передан  статус']);
    exit;
}

try {
    $query = "SELECT get_available_tracks(:id_status) as result;";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id_status' => $id_status]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $tracks = json_decode($result['result'], true);
    
    echo json_encode($tracks ?: []);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>
