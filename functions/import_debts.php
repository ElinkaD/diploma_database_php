<?php 
require 'db_connect.php';
require_once __DIR__ . '/helpers/SemesterAndYearHelper.php';

function importDebtsCSV($pdo, $filename, $semester_flag) {
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
        $id_isu = (int)$row[0];
        $name_disp = trim($row[1]);
        $type_debt = trim($row[2]);
        $number_from_start = (int)$row[3];
        $type_control = trim($row[4]);
        $comment = $row[5] ?? '';

        $type_debt_map = [
            'академ разница' => 'академ_разница',
            'обычный' => 'обычный',
            'реструктуризация' => 'реструктуризация',
            'реструкт' => 'реструктуризация'
        ];

        $type_debt_form = $type_debt_map[$type_debt] ?? 'обычный';

        $type_control_map = [
            'Экзамен' => 'Экзамен',
            'Экзамён' => 'Экзамен',
            'Зачет' => 'Зачет',
            'Зачёт' => 'Зачет',
            'Дифференцированный зачёт' => 'Дифф. зачет',
            'Дифференцированный зачет' => 'Дифф. зачет',
            'Диф зачёт' => 'Дифф. зачет',
            'Диф зачет' => 'Дифф. зачет',
            'Курсовая работа' => 'Курсовая работа',
            'Курсовой проект' => 'Курсовой проект'
        ];
        
        $type_control_ass = $type_control_map[$type_control] ?? 'null';
        
        if (!in_array($type_control_ass, ['Экзамен', 'Зачет', 'Дифф. зачет', 'Курсовая работа', 'Курсовой проект'])) {
            echo "❌ Недопустимый тип контроля (enum): '$type_control_ass'\n";
            continue;
        }

        try {
            $stmt = $pdo->prepare("CALL insert_debt(:id_student, :name_disp, :type_debt_enum, :number_from_start, :assessment_type, :year, :semester, :comment)");
            $stmt->execute([
                'id_student' => $id_isu,
                'name_disp' => $name_disp,
                'type_debt_enum' => $type_debt_form,
                'number_from_start' => $number_from_start,
                'assessment_type' => $type_control_ass,
                'year' => $year,
                'semester' => $semester,
                'comment' => $comment
            ]);
            
        } catch (PDOException $e) {
            echo "Error importing student $id_isu: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон долги.csv';
importDebtsCSV($pdo,  $csvFile, 0);
?>
