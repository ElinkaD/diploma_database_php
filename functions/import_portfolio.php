<?php 
require 'db_connect.php';

function insertGroup($pdo, $groupNumber, $course)
{
    $stmt = $pdo->prepare("SELECT insert_group(:groupNumber, :course) AS group_id");
    $stmt->execute(['groupNumber' => $groupNumber, 'course' => $course]);
    $result = $stmt->fetch();

    return $result['group_id'];
}

function insertStudent($pdo, $id_isu, $fio, $citizenship, $comment)
{
    $stmt = $pdo->prepare("CALL insert_student(:id_isu, :fio, :citizenship, :comment)");
    $stmt->execute([
        'id_isu' => $id_isu,
        'fio' => $fio,
        'citizenship' => $citizenship,
        'comment' => $comment
    ]);
}

// вычисление семестра и года по флагу
function getSemesterAndYear($flag) {
    $month = (int)date('n'); 
    $year = (int)date('Y');
    $currentSemester = ($month >= 1 && $month <= 6) ? 'весна' : 'осень'; 
    
    if ($flag === -1) { // previous semester
        if ($currentSemester == 'весна') {
            return ['semester' => 'осень', 'year' => $year - 1];
        } else {
            return ['semester' => 'весна', 'year' => $year];
        }
    } elseif ($flag === 1) { // next semester
        if ($currentSemester === 'осень') {
            return ['semester' => 'весна', 'year' => $year + 1];
        } else {
            return ['semester' => 'осень', 'year' => $year];
        }
    }
    // current semester (flag 0)
    return ['semester' => $currentSemester, 'year' => $year];
}

function importCSV($pdo, $filename, $semester_flag) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }

    // получаем год и семестр
    $semesterData = getSemesterAndYear($semester_flag);
    $semester = $semesterData['semester'];
    $year = $semesterData['year'];

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        if (empty($row[1]) || empty($row[4]) || empty($row[11])) {
            echo "Пропущена строка из-за отсутствия обязательных данных (id_isu, group, status).\n";
            continue;
        }

        $id_isu = (int)$row[1];
        $fio = $row[2];
        $citizenship = $row[3];
        $group = $row[4];
        $course = (int)$row[5];
        $education_form_raw = mb_strtolower($row[10] ?? '');
        $status_raw = mb_strtolower(trim($row[11]));
        $commentStudent = $row[18] ?? '';
        $commentStatus = $row[19] ?? '';
        $trackName = $row[20] ?? 'test'; 

        $education_form_map = [
            'бюджет' => 'бюджет',
            'б' => 'бюджет',
            'контракт' => 'контракт',
            'к' => 'контракт'
        ];

        $education_form = $education_form_map[$education_form_raw] ?? null;

        $status_map = [
            'обучается' => 'обучается',
            'мд' => 'мд',
            'академический отпуск' => 'академ',
            'академ' => 'академ',
            'отчислен' => 'отчислен',
            'перевелся от нас' => 'перевёлся_от_нас',
            'перевелся к нам' => 'перевёлся_к_нам',
            'перевёлся от нас' => 'перевёлся_от_нас',
            'перевёлся к нам' => 'перевёлся_к_нам'
        ];
        
        $status = $status_map[$status_raw] ?? null;
        
        if (!$status) {
            echo "⚠️ Неизвестный статус студента $id_isu: '$status_raw'\n";
            continue;
        }

        try {
            insertStudent($pdo, $id_isu, $fio, $citizenship, $commentStudent);
            $group_id = insertGroup($pdo, $group, $course);

            $stmt = $pdo->prepare("CALL insert_student_status(:id_student, :status, :education_form, :comment, :id_group, :track_name, :semester, :year)");
            $stmt->execute([
                'id_student' => $id_isu,
                'status' => $status,
                'education_form' => $education_form,
                'comment' => $commentStatus,
                'id_group' => $group_id,
                'track_name' => $trackName,
                'semester' => $semester,
                'year' => $year
            ]);
            
            // echo "Successfully imported: $id_isu - $fio\n";
        } catch (PDOException $e) {
            echo "Error importing student $id_isu: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон портфолио.csv';
importCSV($pdo,  $csvFile, 0);
?>
