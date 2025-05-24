<?php
require_once 'Importer.php';

class TDImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");
        $response = [];

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[1])) {
                $response[] = [
                    'status' => 'error',
                    'message' => 'Пропущена строка из-за отсутствия обязательных данных'
                ];
            }
    
            $d = trim($row[1]);
            $id_teacher = (int)$row[2];

            
            try {
                $stmt = $this->pdo->prepare("CALL insert_td(:d, :id_teacher)");
                $stmt->execute([
                    'd' => $d,
                    'id_teacher' => $id_teacher
                ]);
                
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Error importing $d и $id_teacher: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт disciplines_of_teachers завершён успешно!'
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
