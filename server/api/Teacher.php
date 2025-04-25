<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$studentId = $_GET['id_isu'] ?? null;

if (!$studentId) {
    http_response_code(400);
    echo json_encode(['error' => 'Не указан ID студента']);
    exit;
}

try {
    $query = "SELECT get_student_full_info(:studentId) AS student_info";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['studentId' => $studentId]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['student_info']) {
        echo $result['student_info']; 
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Студент не найден']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка загрузки студентов: ' . $e->getMessage()]);
}
?>
