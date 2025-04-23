<?php
require_once 'Importer.php';

class GroupTrackImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");
        $response = [];

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            $group_number = $row[0];
            $name_track = trim($row[1]);
            if (empty($group_number) || empty($name_track)) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Пропущена строка из-за отсутствия обязательных данных (group_number или name_track)."
                ];
                continue;
            }

            try {
                $stmt = $this->pdo->prepare("CALL insert_group_flow(:group_number, :name_track)");
                $stmt->execute([
                    'group_number' => $group_number,
                    'name_track' => $name_track,
                ]);
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Ошибка при импорте группы $group_number: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт группы/трека завершён успешно.'
            ];
        } else {
            $response[] = [
                'status' => 'warning',
                'message' => 'Импорт завершён с ошибками. Проверьте сообщения выше.'
            ];
        }
        echo json_encode($response);
    }
}
