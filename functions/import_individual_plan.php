<?php 
require 'db_connect.php';

function updateIndividualPlansFromFile($pdo, $filename) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        $columnС = $row[2];

        
        if (preg_match('/Студент:\s*([А-Яа-яЁё\s\-]+)\s*Группа:\s*([A-Za-z0-9]+)/u', $columnС, $matches)) {
            $studentName = trim($matches[1]);
            $studentGroup = trim($matches[2]);

            $planId = $row[7];
            
            if ($studentName && $studentGroup && $planId) {
                if ($planId !== null) {
                    $stmt = $pdo->prepare("CALL update_individual_plan(:studentName, :studentGroup, :planId)");
                    $stmt->execute([
                        ':studentName' => $studentName,
                        ':studentGroup' => $studentGroup,
                        ':planId' => $planId
                    ]);
                } 
            }
        }
    }

    fclose($file);
    echo "Импорт индивидуальных планов завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон учебные_планы.csv';
updateIndividualPlansFromFile($pdo,  $csvFile);
?>
