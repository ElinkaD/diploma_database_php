<?php
require_once 'Importer.php';
require_once __DIR__ . '/../helpers/SemesterAndYearHelper.php';

class SalaryImporter extends SemesterImporter {
    public function import(): void {
        throw new Exception("Метод import() не используется. Используй importWithSemesterFlag().");
    }

    public function importWithSemesterFlag(int $semester_flag): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        $semesterData = SemesterAndYearHelper::getSemesterAndYear($semester_flag);
        $semester = $semesterData['semester'];
        $year = $semesterData['year'];

        fgetcsv($file, 0, "\t");

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                echo "Пропущена строка из-за отсутствия обязательных данных.\n";
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
                echo "Error importing $fio: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($file);
        echo "Импорт долгов завершён!\n";
    }
}
