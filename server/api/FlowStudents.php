<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$flowId = $_GET['flow_id'] ?? null;
$educationForm = $_GET['education_form'] ?? null;

if (!$flowId) {
    http_response_code(400);
    echo json_encode(['error' => 'Не указан ID потока']);
    exit;
}

try {
    $query = "
        SELECT 
          s.id_isu, s.fio, s.citizenship, s.comment, 
          ss.education_form, ss.status, ss.comment AS status_comment, 
          g.group_number, t.name AS track_name
        FROM students_in_flows sif 
        JOIN students s ON sif.id_student = s.id_isu
        JOIN flows f ON sif.id_flow = f.id
        JOIN student_statuses ss ON s.id_isu = ss.id_student 
          AND f.semester = ss.semester AND f.year = ss.year
        JOIN groups g ON ss.id_group = g.id
        JOIN s335141.tracks t ON ss.id_track = t.id
        WHERE f.id_isu = :flowId
    ";

    if ($educationForm !== null) {
        $query .= " AND ss.education_form = :educationForm";
    }
    $stmt = $pdo->prepare($query);
    $params = ['flowId' => $flowId];
    if ($educationForm !== null) {
        $params['educationForm'] = $educationForm;
    }

    $stmt->execute($params);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка загрузки студентов: ' . $e->getMessage()]);
}
?>
