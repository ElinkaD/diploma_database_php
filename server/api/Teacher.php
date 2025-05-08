<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$teacherId = $_GET['id_isu'] ?? null;

if (!$teacherId) {
    http_response_code(400);
    echo json_encode(['error' => 'Не указан ID студента']);
    exit;
}

try {
    $query = "SELECT get_teacher_full_info(:teacherId) AS teacher_info";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['teacherId' => $teacherId]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['teacher_info']) {
        echo $result['teacher_info']; 
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Преподаватель не найден']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка загрузки преподавателей: ' . $e->getMessage()]);
}
?>
