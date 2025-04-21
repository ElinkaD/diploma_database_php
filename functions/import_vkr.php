<?php 
require 'db_connect.php';

function importVkrCSV($pdo, $filename) {
    $file = fopen($filename, 'r');

    if (!$file) {
        die("Ошибка открытия файла");
    }

    fgetcsv($file, 0, "\t");

    while (($row = fgetcsv($file, 0, ",")) !== false) {
        if (empty($row[0])) {
            echo "Пропущена строка из-за отсутствия обязательных данных id_isu_student\n";
            continue;
        }

        $id_isu_st = (int)$row[0];
        $fio_sup = trim($row[1]);
        $theme = $row[2];
        $comment = $row[3];
        $fio_con = trim($row[4]);
        
        try {
            $stmt = $pdo->prepare("CALL insert_vkr(:id_isu_st, :fio_sup, :theme, :comment, :fio_con)");
            $stmt->execute([
                'id_isu_st' => $id_isu_st,
                'fio_sup' => $fio_sup,
                'theme' => $theme,
                'comment' => $comment,
                'fio_con' => $fio_con
            ]);
            
        } catch (PDOException $e) {
            echo "Error importing $id_isu_st: " . $e->getMessage() . "\n";
            continue;
        }
    }

    fclose($file);
    echo "Импорт завершен!";
}

$csvFile = '../import_tables_csv/Шаблон импорта - шаблон ВКР.csv';
importVkrCSV($pdo,  $csvFile);
?>
