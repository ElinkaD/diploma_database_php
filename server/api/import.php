<?php
require_once '../db/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

$uploadDir = __DIR__ . '/../uploads/';

$allowedImports = [
    'group_track' => 'GroupTrackImporter',
    'flows' => 'FlowImporter',
    'debts' => 'DebtsImporter',
    'individua_plan_number' => 'IndividualPlanNumberImporter',
    'kpi' => 'KpiImporter',
    'mentors' => 'MentorsImporter',
    'nagruzka' => 'NagruzkaImporter',
    'portfolio' => 'PortfolioImporter',
    'salary' => 'SalaryImporter',
    'st_in_flows' => 'StudentsInFlowsImporter',
    'teachers' => 'TeachersImporter',
    'vkr' => 'VkrImporter',
    'ip_subjects' => 'IPSubjectsImporter',
    'rpd_teachers' => 'RpdTeachersImporter',
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается']);
    exit;
}

if (!isset($_FILES['file']) || !isset($_GET['type'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверные параметры запроса']);
    exit;
}

$type = $_GET['type'];
$semester = $_GET['semester'] ?? null;
$year = $_GET['year'] ?? null;

if (in_array($type, ['debts', 'flows', 'mentors', 'nagruzka', 'portfolio', 'salary'])) {
    if (!$semester || !$year) {
        echo json_encode(['error' => 'Отсутствуют обязательные параметры: semester или year']);
        exit;
    }
}


$fileName = basename($_FILES['file']['name']);
$fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
$targetFileName = 'import_' . $type . '.' . $fileExt; 
$targetFile = $uploadDir . $targetFileName;

if (file_exists($targetFile)) {
    if (!unlink($targetFile)) {
        http_response_code(500);
        echo json_encode(['error' => 'Не удалось удалить старый файл']);
        exit;
    }
}


$fileTmp = $_FILES['file']['tmp_name'];
if (!move_uploaded_file($fileTmp, $targetFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Не удалось сохранить файл']);
    exit;
}

require_once __DIR__ . "/../classes/{$allowedImports[$type]}.php";


$className = $allowedImports[$type];
$importer = new $className($pdo, $targetFile);

try {
    if (method_exists($importer, 'importWithSemester')) {
        $importer->importWithSemester($semester, $year);
    } elseif (method_exists($importer, 'import')) {
        $importer->import();
    } else {
        throw new Exception('Import is not possible: the import method is not defined.');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Import error: ' . $e->getMessage()]);
}