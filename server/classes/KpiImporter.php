<?php
require_once 'Importer.php';

class KpiImporterImporter extends Importer {
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
                    'message' => "Пропущена строка из-за отсутствия обязательных данных (ФИО)."
                ];
                continue;
            }
            
            $fio = trim($row[0]);
            $type_kpi = trim($row[1]);
            $value = (int)str_replace(' ', '', trim($row[2]));
    
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_kpi(:fio, :type_kpi, :value)");
                $stmt->execute([
                    'fio' => $fio,
                    'type_kpi' => $type_kpi,
                    'value' => $value
                ]);
                
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Ошибка импорта KPI для $fio: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт KPI завершён успешно.'
            ];
        } else {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт KPI завершён с ошибками. Проверьте сообщения выше.'
            ];
        }

        echo json_encode($response);
    }
}
