<?php
require_once '../db/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

$id_isu = $_GET['id_isu'] ?? null;

if (!$id_isu) {
    http_response_code(400);
    echo json_encode(['error' => 'Не передан табельный номер']);
    exit;
}

try {
    $query = "SELECT ss.id AS id_status, ss.education_form, ss.status, ss.semester, ss.year, g.group_number, g.year_enter, 
                    t.name AS track_name, c.name AS plan_name, c.year AS plan_year, ss.comment  
                FROM students s
                JOIN student_statuses ss ON s.id_isu = ss.id_student
                JOIN s335141.tracks t ON ss.id_track = t.id
                LEFT JOIN s335141.sections sec ON t.id_section = sec.id
                LEFT JOIN s335141.curricula c ON sec.id_curricula = c.id_isu
                JOIN groups g ON ss.id_group = g.id
                WHERE s.id_isu = :id_isu
                ORDER BY ss.year DESC, ss.semester;";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_isu' => $id_isu]);
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($statuses);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>
