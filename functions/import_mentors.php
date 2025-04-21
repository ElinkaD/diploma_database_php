<?php 
require 'db_connect.php';
require_once __DIR__ . '/helpers/SemesterAndYearHelper.php';


function importMentorsCSV($pdo, $filename, $semester_flag) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }
    $semesterData = SemesterAndYearHelper::getSemesterAndYear($semester_flag);
    $semester = $semesterData['semester'];
    $year = $semesterData['year'];

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            echo "Пропущена строка из-за отсутствия обязательных данных.\n";
            continue;
        }
        

        $id_isu = (int)$row[0];
        $name_disp = trim($row[1]);
        $fio_teacher = trim($row[2]);
        $comment = $row[3];
        
        try {
            $stmt = $pdo->prepare("CALL insert_mentor(:id_isu, :name_disp, :fio_teacher, :year, :semester, :comment)");
            $stmt->execute([
                'id_isu' => $id_isu,
                'name_disp' => $name_disp,
                'fio_teacher' => $fio_teacher,
                'year' => $year,
                'semester' => $semester,
                'comment' => $comment
            ]);
            
        } catch (PDOException $e) {
            echo "Error importing $id_isu: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон менторы.csv';
importMentorsCSV($pdo,  $csvFile, 0);
?>
