<?php
require_once 'Importer.php';

class VkrImporter extends Importer {
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
                    'message' => 'Пропущена строка из-за отсутствия обязательных данных id_isu_student'
                ];
            }
    
            $id_isu_st = (int)$row[0];
            $fio_sup = trim($row[1]);
            $theme = $row[2];
            $comment = $row[3];
            $fio_con = trim($row[4]);
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_vkr(:id_isu_st, :fio_sup, :theme, :comment, :fio_con)");
                $stmt->execute([
                    'id_isu_st' => $id_isu_st,
                    'fio_sup' => $fio_sup,
                    'theme' => $theme,
                    'comment' => $comment,
                    'fio_con' => $fio_con
                ]);
                
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Error importing $id_isu_st: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт vkr завершён успешно!'
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
