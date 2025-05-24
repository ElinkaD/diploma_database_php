<?php
require_once 'Importer.php';

class RpdTeachersImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");
        $response = [];

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[0]) || empty($row[1])) {
                $response[] = [
                    'status' => 'error',
                    'message' => 'Пропущена строка из-за отсутствия обязательных данных ФИО или id_rpd'
                ];
                continue;
            }
            
            $fio = trim($row[0]);
            $id_rpd = (int)$row[1];
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_rpd_teachers(:fio, :id_rpd)");
                $stmt->execute([
                    'fio' => $fio,
                    'id_rpd' => $id_rpd
                ]);
                
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Error importing $fio: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт rpd_teachers завершён успешно!'
            ];
        } else {
            $response[] = [
                'status' => 'warning',
                'message' => 'Импорт завершён с ошибками, проверьте сообщения выше.'
            ];
        }
        echo json_encode($response);
    }
}
