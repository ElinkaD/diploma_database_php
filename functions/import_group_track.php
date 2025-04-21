<?php 
require 'db_connect.php';

function importGroupTrackCSV($pdo, $filename) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        $group_number = $row[0];
        $name_track = trim($row[1]);

        // echo $group_number . ' ' . $name_track;
        
        try {
            $stmt = $pdo->prepare("CALL insert_group_flow(:group_number, :name_track)");
            $stmt->execute([
                'group_number' => $group_number,
                'name_track' => $name_track,
            ]);
            
        } catch (PDOException $e) {
            echo "Error importing $group_number: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - группа треков.csv';
importGroupTrackCSV($pdo,  $csvFile);
?>
