<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$params = [
    'p_id_isu' => !empty($_GET['id_isu']) ? (int)$_GET['id_isu'] : null,
    'p_fio' => !empty($_GET['fio']) ? trim($_GET['fio']) : null,
    'p_discipline' => !empty($_GET['discipline']) ? trim($_GET['discipline']) : null,
    'p_id_rpd' => !empty($_GET['id_rpd']) ? (int)$_GET['id_rpd'] : null,
    'p_status_rpd' => !empty($_GET['status_rpd']) ? trim($_GET['status_rpd']) : null,
    'p_semester' => !empty($_GET['semester']) ? trim($_GET['semester']) : null,
    'p_year' => !empty($_GET['year']) ? (int)$_GET['year'] : null,
    'p_workload_type' => !empty($_GET['workload_type']) ? trim($_GET['workload_type']) : null,
];

try {
    $stmt = $pdo->prepare("SELECT call_teachers(
        :p_id_isu, :p_fio, :p_discipline, :p_id_rpd,
        :p_status_rpd, :p_semester, :p_year, :p_workload_type
    )");
    $stmt->execute($params);
    $teachers = $stmt->fetchColumn();
    echo $teachers;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка при получении списка преподавателей: ' . $e->getMessage()]);
}
?>
