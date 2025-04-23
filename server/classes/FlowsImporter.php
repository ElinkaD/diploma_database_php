<?php
require_once 'Importer.php';

class FlowsImporter extends SemesterImporter {
    public function import(): void {
        throw new Exception("Метод import() не используется. Используй importWithSemesterFlag().");
    }

    public function importWithSemester(string $semester, int $year): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            die("Ошибка открытия файла");
        }
    
    
        fgetcsv($file, 0, "\t");
        $response = [];
    
        while (($row = fgetcsv($file, 0, ",")) !== false) {
            $name_flow = trim($row[0]);
            $count_limit = (int)$row[1];
            $commentFlow = $row[2] ?? '';
            $id_rpd = (int)$row[3];
            $type_task = mb_strtolower($row[4] ?? '');
            $number_of_start_relize = (int)$row[5];
            $id_isu = (int)$row[6];
    
            $type_tasks_map = [
                'лабораторные занятия' => 'Лаб',
                'практические занятия' => 'Пр',
                'лекционные занятия' => 'Лек',
                'консультации' => 'К'
            ];
    
            $type_task_form = $type_tasks_map[$type_task] ?? null;
            
            
            if (!$type_task_form) {
                $response[] = [
                    'status' => 'error',
                    'message' => "⚠️ Unknown task type for flow $name_flow: '$type_task'"
                ];
                continue;
            }
    
            try {
                $stmt = $this->pdo->prepare("CALL insert_flow(:name, :year, :semester, :count_limit, :comment, :id_rpd, :type_task_form, :number_of_start_relize, :id_isu)");
                $stmt->execute([
                    'name' => $name_flow,
                    'year' => $year,
                    'semester' => $semester,
                    'count_limit' => $count_limit,
                    'comment' => $commentFlow,
                    'type_task_form' => $type_task_form,
                    'id_rpd' => $id_rpd,
                    'number_of_start_relize' => $number_of_start_relize,
                    'id_isu' => $id_isu
                ]);
                
                // echo "Successfully imported: $id_isu - $fio\n";
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Error importing flow $name_flow: " . $e->getMessage()
                ];
                continue;
            }
        }
    
        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт flows завершён успешно!'
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