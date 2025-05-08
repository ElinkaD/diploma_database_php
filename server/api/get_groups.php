<?php
require_once '../db/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');


try {
    $query = "SELECT id, group_number, year_enter FROM groups ORDER BY year_enter DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($groups);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>