<?php
require_once '../db/db_connect.php';

header('Content-Type: application/json');

$year = $_GET['year'] ?? null;
$semester = $_GET['semester'] ?? null;

$where = [];
$params = [];

if (!is_null($year) && $year !== '') {
    $where[] = "f.year = :year";
    $params['year'] = $year;
}
if (!is_null($semester) && $semester !== '') {
    $where[] = "f.semester = :semester";
    $params['semester'] = $semester;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    $stmt = $pdo->prepare(" 
        SELECT 
            f.id_isu, 
            f.name, 
            f.year, 
            f.semester, 
            (
                SELECT COUNT(*) 
                FROM Students_in_Flows sif 
                WHERE sif.id_flow = f.id
            ) AS student_count,
            f.count_limit, 
            w.type, 
            f.comment,
            r.id_isu AS rpd_id, 
            d.name AS discipline_name
        FROM Flows f
        JOIN s335141.workloads w ON f.id_workload = w.id
        JOIN s335141.semester_rpd sr ON w.id_sem = sr.id
        JOIN s335141.rpd r ON sr.id_rpd = r.id_isu
        JOIN s335141.disciplines d ON r.id_discipline = d.id
        $whereClause
        ORDER BY f.year DESC, f.semester DESC, f.name ASC
    ");

    $stmt->execute($params);
    $flows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($flows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка при получении списка потоков: ' . $e->getMessage()]);
}
?>
