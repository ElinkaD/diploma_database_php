<?php
require_once 'Importer.php';

class KpiImporterImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            if (empty($row[0])) {
                echo "Пропущена строка из-за отсутствия обязательных данных ФИО\n";
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
                echo "Error importing $fio: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($file);
        echo "Импорт kpi завершён!\n";
    }
}
