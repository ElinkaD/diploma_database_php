<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$id_isu = $_GET['id_isu'] ?? null;
$fio = $_GET['fio'] ?? null;
$year = $_GET['year'] ?? null;
$semester = $_GET['semester'] ?? null;
$status = $_GET['status'] ?? null;
$education_form = $_GET['education_form'] ?? null;
$group = $_GET['group'] ?? null;
$track = $_GET['track'] ?? null;
$citizenship = $_GET['citizenship'] ?? null;

$where = [];
$params = [];

if (!empty($id_isu)) {
    $where[] = "s.id_isu = :id_isu";
    $params['id_isu'] = $id_isu;
}
if (!empty($fio)) {
    $where[] = "s.fio = :fio";
    $params['fio'] = $fio;
}
if (!empty($year)) {
    $where[] = "latest_ss.year = :year";
    $params['year'] = $year;
}
if (!empty($semester)) {
    $where[] = "latest_ss.semester = :semester";
    $params['semester'] = $semester;
}
if (!empty($status)) {
    $where[] = "latest_ss.status = :status";
    $params['status'] = $status;
}
if (!empty($education_form)) {
    $where[] = "latest_ss.education_form = :education_form";
    $params['education_form'] = $education_form;
}
if (!empty($group)) {
    $where[] = "g.group_number ILIKE :group";
    $params['group'] = '%' . $group . '%';
}
if (!empty($track)) {
    $where[] = "t.name ILIKE :track";
    $params['track'] = '%' . $track . '%';
}
if (!empty($citizenship)) {
    $where[] = "s.citizenship ILIKE :citizenship";
    $params['citizenship'] = '%' . $citizenship . '%';
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.id_isu, 
            s.fio, 
            s.id_individual_plan_isu, 
            s.citizenship, 
            s.comment, 
            latest_ss.education_form, 
            latest_ss.status, 
            latest_ss.semester, 
            latest_ss.year, 
            latest_ss.comment AS status_comment, 
            g.group_number, 
            t.name AS track_name
        FROM 
            Students s
        JOIN (
            SELECT 
                ss1.* 
            FROM student_statuses ss1 
            JOIN (
                SELECT id_student, MAX(id) AS max_id 
                FROM student_statuses 
                GROUP BY id_student
            ) ss2 ON ss1.id = ss2.max_id
        ) latest_ss ON s.id_isu = latest_ss.id_student
        LEFT JOIN groups g ON latest_ss.id_group = g.id
        LEFT JOIN s335141.tracks t ON latest_ss.id_track = t.id
        $whereClause
        ORDER BY latest_ss.year DESC, latest_ss.semester DESC, s.fio ASC
    ");

    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка при получении списка студентов: ' . $e->getMessage()]);
}
?>
