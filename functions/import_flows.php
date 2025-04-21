<?php 
require 'db_connect.php';
require_once __DIR__ . '/helpers/SemesterAndYearHelper.php';

function importFlowsCSV($pdo, $filename, $semester_flag) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }

    // получаем год и семестр
    $semesterData = SemesterAndYearHelper::getSemesterAndYear($semester_flag);
    $semester = $semesterData['semester'];
    $year = $semesterData['year'];

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        $name_flow = trim($row[0]);
        $count_limit = (int)$row[1];
        $commentFlow = $row[2] ?? '';
        $id_rpd = (int)$row[3];
        $type_task = mb_strtolower($row[4] ?? '');
        $number_of_start_relize = (int)$row[5];
        $id_isu = (int)$row[6];

        $type_tasks_map = [
            'лабораторные занятия' => 'Лаб',
            'практические занятия' => 'Пр',
            'лекционные занятия' => 'Лек',
            'консультации' => 'К'
        ];

        $type_task_form = $type_tasks_map[$type_task] ?? null;
        
        
        if (!$type_tasks_map) {
            echo "⚠️ Неизвестный вид занятий $name_flow: '$type_task'\n";
            continue;
        }

        try {
            $stmt = $pdo->prepare("CALL insert_flow(:name, :year, :semester, :count_limit, :comment, :id_rpd, :type_task_form, :number_of_start_relize, :id_isu)");
            $stmt->execute([
                'name' => $name_flow,
                'year' => $year,
                'semester' => $semester,
                'count_limit' => $count_limit,
                'comment' => $commentFlow,
                'type_task_form' => $type_task_form,
                'id_rpd' => $id_rpd,
                'number_of_start_relize' => $number_of_start_relize,
                'id_isu' => $id_isu
            ]);
            
            // echo "Successfully imported: $id_isu - $fio\n";
        } catch (PDOException $e) {
            echo "Error importing flow $name_flow: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон потоки.csv';
importFlowsCSV($pdo,  $csvFile, 0);
?>
