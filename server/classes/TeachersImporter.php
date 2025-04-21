<?php
require_once 'Importer.php';

class GroupTrackImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3])) {
                echo "Пропущена строка из-за отсутствия обязательных данных.\n";
                continue;
            }
    
            $id_isu = (int)$row[0];
            $fio = trim($row[1]);
            $year = $row[2];
            $term = (int)$row[3];
            $comment = $row[4];
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_teacher(:id_isu, :fio, :year, :term, :comment)");
                $stmt->execute([
                    'id_isu' => $id_isu,
                    'fio' => $fio,
                    'year' => $year,
                    'term' => $term,
                    'comment' => $comment
                ]);
                
            } catch (PDOException $e) {
                echo "Error importing $fio: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($file);
        echo "Импорт группы/трека завершён!\n";
    }
}
