<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$id_isu = $_GET['id_isu'] ?? null;
$fio = $_GET['fio'] ?? null;
$year = $_GET['year'] ?? null;
$semester = $_GET['semester'] ?? null;
$status = $_GET['status'] ?? null;
$education_form = $_GET['education_form'] ?? null;
$track = $_GET['track'] ?? null;
$citizenship = $_GET['citizenship'] ?? null;

$where = [];
$params = [];

if (!empty($id_isu)) {
    $where[] = "s.id_isu = :id_isu";
    $params['id_isu'] = $id_isu;
}
if (!empty($fio)) {
    $where[] = "s.fio ILIKE :fio";
    $params['fio'] = '%' . $fio . '%';
}
if (!empty($status)) {
    $where[] = "rs.status = :status";
    $params['status'] = $status;
}
if (!empty($education_form)) {
    $where[] = "rs.education_form = :education_form";
    $params['education_form'] = $education_form;
}
if (!empty($_GET['group_id'])) {
    $where[] = "g.id = :group";
    $params['group'] = $_GET['group_id'];
}
if (!empty($_GET['plan_id'])) {
    $where[] = "c.id_isu = :plan";
    $params['plan'] = $_GET['plan_id'];
}
if (!empty($track)) {
    $where[] = "t.number = :track";
    $params['track'] = $track;
}
if (!empty($citizenship)) {
    $where[] = "s.citizenship ILIKE :citizenship";
    $params['citizenship'] = '%' . $citizenship . '%';
}

$filterByYearSemester = !empty($year) || !empty($semester);

if ($filterByYearSemester) {
    if (!empty($year)) {
        $where[] = "rs.year = :year";
        $params['year'] = $year;
    }
    if (!empty($semester)) {
        $where[] = "rs.semester = :semester";
        $params['semester'] = $semester;
    }
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    if ($filterByYearSemester) {
        $query = "
            SELECT 
                s.id_isu, 
                s.fio, 
                s.id_individual_plan_isu, 
                s.citizenship, 
                s.comment, 
                rs.education_form, 
                rs.status, 
                rs.semester, 
                rs.year, 
                rs.comment AS status_comment, 
                g.group_number, 
                g.year_enter,
                c.name AS plan_name,
                c.year AS plan_year,
                t.number AS track_name
            FROM Students s
            JOIN student_statuses rs ON s.id_isu = rs.id_student
            LEFT JOIN groups g ON rs.id_group = g.id
            LEFT JOIN s335141.tracks t ON rs.id_track = t.id
            LEFT JOIN s335141.sections sec ON t.id_section = sec.id
            LEFT JOIN s335141.curricula c ON sec.id_curricula  = c.id_isu
            $whereClause
            ORDER BY s.fio ASC, rs.year DESC, rs.semester DESC";
    } else {
        $query = "
            WITH ranked_statuses AS (
            SELECT ss.*,
                ROW_NUMBER() OVER (PARTITION BY id_student ORDER BY year DESC,
                CASE WHEN semester = 'весна' THEN 1 ELSE 2 END DESC) AS rn
            FROM student_statuses ss
        )
        SELECT 
            s.id_isu, 
            s.fio, 
            s.id_individual_plan_isu, 
            s.citizenship, 
            s.comment, 
            rs.education_form, 
            rs.status, 
            rs.semester, 
            rs.year, 
            rs.comment AS status_comment, 
            g.group_number, 
            g.year_enter,
            c.name AS plan_name,
            c.year AS plan_year,
            t.number AS track_name
        FROM Students s
        JOIN ranked_statuses rs ON s.id_isu = rs.id_student AND rs.rn = 1
        LEFT JOIN groups g ON rs.id_group = g.id
        LEFT JOIN s335141.tracks t ON rs.id_track = t.id
        LEFT JOIN s335141.sections sec ON t.id_section = sec.id
        LEFT JOIN s335141.curricula c ON sec.id_curricula  = c.id_isu
        $whereClause
        ORDER BY s.fio ASC";
    }
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка при получении списка студентов: ' . $e->getMessage()]);
}
?>
