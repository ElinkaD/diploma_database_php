<?php
require_once 'Importer.php';

class StudentsInFlowsImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            $id_isu = (int)$row[0];
            $id_flow = (int)$row[1];
            $name_flow = trim($row[2]);
    
            // echo $id_flow . ' ' . $name_flow;
            
            try {
                $stmt = $this->pdo->prepare("CALL insert_student_in_flow(:id_isu, :id_flow, :name_flow)");
                $stmt->execute([
                    'id_isu' => $id_isu,
                    'id_flow' => $id_flow,
                    'name_flow' => $name_flow,
                ]);
                
            } catch (PDOException $e) {
                echo "Error importing student $id_isu: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($file);
        echo "Импорт группы/трека завершён!\n";
    }
}
