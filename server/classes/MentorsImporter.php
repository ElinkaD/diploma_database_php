<?php
require_once 'Importer.php';
require_once __DIR__ . '/../helpers/SemesterAndYearHelper.php';

class MentorsImporter extends SemesterImporter {
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
                echo "Error importing $id_isu: " . $e->getMessage() . "\n";
                continue;
            }
        }
    

        fclose($file);
        echo "Импорт менторов завершён!\n";
    }
}
