<?php
require_once 'Importer.php';

class NagruzkaImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");
        $response = [];

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[0]) || empty($row[3])) {
                $response[] = [
                    'status' => 'error',
                    'message' => 'Пропущена строка из-за отсутствия обязательных данных ФИО или дисциплина'
                ];
                continue;
            }
    
            $name_discipline = trim($row[0]);
            $id_rpd = (int)$row[1];
            $semester_table = (int)$row[2];
            $fio = trim($row[3]);
            $lection = (int)$row[5];
            $pract = (int)$row[6];
            $lab = (int)$row[7];
            $ysrs = (int)$row[8];
    
            $comment = $row[9];
    
    
            try {
                $stmt = $this->pdo->prepare("CALL insert_nagruzka(:name_discipline, :id_rpd, :semester_table, :fio, :lection, :pract, :lab, :ysrs, :comment)");
                $stmt->execute([
                    'name_discipline' => $name_discipline,
                    'id_rpd' => $id_rpd,
                    'semester_table' => $semester_table,
                    'fio' => $fio,
                    'lection' => $lection,
                    'pract' => $pract,
                    'lab' => $lab,
                    'ysrs' => $ysrs,
                    'comment' => $comment
                ]);
                
                // echo "Successfully imported: $id_isu - $fio\n";
            } catch (PDOException $e) {
                echo "Error importing student $name_discipline и $fio: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт nagruzka завершён успешно!'
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
