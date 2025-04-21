<?php 
require 'db_connect.php';

function importStudentsInFlowsCSV($pdo, $filename) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        $id_isu = (int)$row[0];
        $id_flow = (int)$row[1];
        $name_flow = trim($row[2]);

        // echo $id_flow . ' ' . $name_flow;
        
        try {
            $stmt = $pdo->prepare("CALL insert_student_in_flow(:id_isu, :id_flow, :name_flow)");
            $stmt->execute([
                'id_isu' => $id_isu,
                'id_flow' => $id_flow,
                'name_flow' => $name_flow,
            ]);
            
        } catch (PDOException $e) {
            echo "Error importing student $id_isu: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон студенты в потоке.csv';
importStudentsInFlowsCSV($pdo,  $csvFile);
?>
