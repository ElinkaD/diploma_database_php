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
            $group_number = $row[0];
            $name_track = trim($row[1]);

            try {
                $stmt = $this->pdo->prepare("CALL insert_group_flow(:group_number, :name_track)");
                $stmt->execute([
                    'group_number' => $group_number,
                    'name_track' => $name_track,
                ]);
            } catch (PDOException $e) {
                echo "Ошибка при импорте $group_number: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($file);
        echo "Импорт группы/трека завершён!\n";
    }
}
