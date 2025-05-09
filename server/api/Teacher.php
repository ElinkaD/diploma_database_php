<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$teacherId = $_GET['id_isu'] ?? null;
$scenario = $_GET['scenario'] ?? null;
$planId = $_GET['id_plan'] ?? null;

if (!$teacherId) {
    http_response_code(400);
    echo json_encode(['error' => 'Не указан ID студента']);
    exit;
}

try {
    if ($scenario == 4 && !$planId) {
        http_response_code(400);
        echo json_encode(['error' => 'Для сценария 4 необходимо указать ID учебного плана']);
        exit;
    }

    if ($scenario && $planId) {
        $query = "SELECT get_teacher_full_info(:teacherId, :scenario, :planId) AS teacher_info";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'teacherId' => $teacherId,
            'scenario' => $scenario,
            'planId' => $planId
        ]);
    } elseif ($scenario) {
        $query = "SELECT get_teacher_full_info(:teacherId, :scenario) AS teacher_info";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'teacherId' => $teacherId,
            'scenario' => $scenario
        ]);
    } else {
        $query = "SELECT get_teacher_full_info(:teacherId) AS teacher_info";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['teacherId' => $teacherId]);
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['teacher_info']) {
        $data = json_decode($result['teacher_info']);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo json_encode($data);
        } else {
            echo $result['teacher_info'];
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Преподаватель не найден']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка загрузки преподавателей: ' . $e->getMessage()]);
}
?>
