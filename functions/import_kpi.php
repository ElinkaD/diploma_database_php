<?php 
require 'db_connect.php';


function importKpiCSV($pdo, $filename) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        if (empty($row[0])) {
            echo "Пропущена строка из-за отсутствия обязательных данных ФИО\n";
            continue;
        }
        
        $fio = trim($row[0]);
        $type_kpi = trim($row[1]);
        $value = (int)str_replace(' ', '', trim($row[2]));

        
        try {
            $stmt = $pdo->prepare("CALL insert_kpi(:fio, :type_kpi, :value)");
            $stmt->execute([
                'fio' => $fio,
                'type_kpi' => $type_kpi,
                'value' => $value
            ]);
            
        } catch (PDOException $e) {
            echo "Error importing $fio: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон KPI.csv';
importKpiCSV($pdo,  $csvFile);
?>
