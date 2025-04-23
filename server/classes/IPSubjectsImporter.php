<?php
require_once 'Importer.php';

class IPSubjectsImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");
        $response = [];

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[0])) {
                $response[] = [
                    'status' => 'error',
                    'message' => 'Пропущена строка из-за отсутствия обязательных данных ФИО'
                ];
                continue;
            }
            
            $id_isu = (int)$row[0];
            $name_disc = trim($row[1]);
            $semester = (int)$row[2];
    
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_ip(:id_isu, :name_disc, :semester)");
                $stmt->execute([
                    'id_isu' => $id_isu,
                    'name_disc' => $name_disc,
                    'semester' => $semester
                ]);
                
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Error importing $id_isu и $name_disc: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт indicidual_plans_subject завершён успешно!'
            ];
        } else {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт завершён с ошибками, проверьте сообщения выше.'
            ];
        }
        echo json_encode($response);
    }
}
