<?php
require_once 'Importer.php';

class SalaryImporter extends SemesterImporter {
    public function import(): void {
        throw new Exception("Метод import() не используется. Используй importWithSemesterFlag().");
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
                    'message' => "Пропущена строка из-за отсутствия обязательных данных (ФИО, сумма, должность)."
                ];
                continue;
            }
            
            $fio = trim($row[0]);
            $amount = (int)str_replace(' ', '', trim($row[1]));
            $position = trim($row[2]);
            $comment = $row[3];
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_salary(:fio, :amount, :position, :year, :semester, :comment)");
                $stmt->execute([
                    'fio' => $fio,
                    'amount' => $amount,
                    'position' => $position,
                    'year' => $year,
                    'semester' => $semester,
                    'comment' => $comment
                ]);
                
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Ошибка импорта зарплаты для $fio: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт зарплат завершён успешно.'
            ];
        } else {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт зарплат завершён с ошибками. Проверьте сообщения выше.'
            ];
        }

        echo json_encode($response);
    }
}
