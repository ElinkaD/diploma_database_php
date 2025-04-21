<?php
require_once 'Importer.php';
require_once __DIR__ . '/../helpers/SemesterAndYearHelper.php';

class NagruzkaImporter extends SemesterImporter {
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
            if (empty($row[0]) || empty($row[3])) {
                echo "Пропущена строка из-за отсутствия обязательных данных (name_discipline, fio).\n";
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
                $stmt = $this->pdo->prepare("CALL insert_nagruzka(:name_discipline, :id_rpd, :semester_table, :fio, :lection, :pract, :lab, :ysrs,  :semester, :year, :comment)");
                $stmt->execute([
                    'name_discipline' => $name_discipline,
                    'id_rpd' => $id_rpd,
                    'semester_table' => $semester_table,
                    'fio' => $fio,
                    'lection' => $lection,
                    'pract' => $pract,
                    'lab' => $lab,
                    'ysrs' => $ysrs,
                    'semester' => $semester,
                    'year' => $year,
                    'comment' => $comment
                ]);
                
                // echo "Successfully imported: $id_isu - $fio\n";
            } catch (PDOException $e) {
                echo "Error importing student $name_discipline и $fio: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($file);
        echo "Импорт долгов завершён!\n";
    }
}
