<?php
require_once '../db/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');


try {
    $query = "SELECT id_isu, name FROM s335141.curricula ORDER BY year DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($plans);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>