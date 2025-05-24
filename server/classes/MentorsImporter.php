<?php
require_once 'Importer.php';

class MentorsImporter extends SemesterImporter {
    public function import(): void {
        throw new Exception("Метод import() не используется. Используй importWithSemester().");
    }

    public function importWithSemester(string $semester, int $year): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");
        $response = [];

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                $response[] = [
                    'status' => 'error',
                    'message' => 'Пропущена строка из-за отсутствия обязательных данных.\n'
                ];
                continue;
            }
            
    
            $id_isu = (int)$row[0];
            $name_disp = trim($row[1]);
            $fio_teacher = trim($row[2]);
            $comment = $row[3];
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_mentor(:id_isu, :name_disp, :fio_teacher, :year, :semester, :comment)");
                $stmt->execute([
                    'id_isu' => $id_isu,
                    'name_disp' => $name_disp,
                    'fio_teacher' => $fio_teacher,
                    'year' => $year,
                    'semester' => $semester,
                    'comment' => $comment
                ]);
                
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Error importing $id_isu: " . $e->getMessage()
                ];
                continue;
            }
        }
    

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт mentors завершён успешно!'
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
