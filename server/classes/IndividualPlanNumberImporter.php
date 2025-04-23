<?php
require_once 'Importer.php';

class IndividualPlanNumberImporter extends Importer {
    public function import(): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");
        $response = [];

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            $columnС = $row[2];
            
            if (preg_match('/Студент:\s*([А-Яа-яЁё\s\-]+)\s*Группа:\s*([A-Za-z0-9]+)/u', $columnС, $matches)) {
                $studentName = trim($matches[1]);
                $studentGroup = trim($matches[2]);

                $planId = $row[7];
                
                if ($studentName && $studentGroup && $planId) {
                    try {
                        $stmt = $this->pdo->prepare("CALL update_individual_plan(:studentName, :studentGroup, :planId)");
                        $stmt->execute([
                            ':studentName' => $studentName,
                            ':studentGroup' => $studentGroup,
                            ':planId' => $planId
                        ]);
                    } catch (PDOException $e) {
                        $response[] = [
                            'status' => 'error',
                            'message' => "Ошибка при обновлении плана для студента $studentName: " . $e->getMessage()
                        ];
                    }
                }
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт индивидуальных планов завершен успешно.'
            ];
        } else {
            $response[] = [
                'status' => 'error',
                'message' => 'Импорт завершен с ошибками. Проверьте сообщения выше.'
            ];
        }

        echo json_encode($response);
    }
}
