<?php
require_once 'Importer.php';

class DebtsImporter extends SemesterImporter {
    public function import(): void {
        throw new Exception("Метод import() не используется. Используй importWithSemester().");
    }

    public function importWithSemester(string $semester, int $year): void {
        $file = fopen($this->filename, 'r');
        if (!$file) {
            throw new Exception("Ошибка открытия файла");
        }

        fgetcsv($file, 0, "\t");

        while (($row = fgetcsv($file, 0, ",")) !== false) {
            $id_isu = (int)$row[0];
            $name_disp = trim($row[1]);
            $type_debt = trim($row[2]);
            $number_from_start = (int)$row[3];
            $type_control = trim($row[4]);
            $comment = $row[5] ?? '';

            $type_debt_map = [
                'академ разница' => 'академ_разница',
                'обычный' => 'обычный',
                'реструктуризация' => 'реструктуризация',
                'реструкт' => 'реструктуризация'
            ];

            $type_control_map = [
                'Экзамен' => 'Экзамен',
                'Экзамён' => 'Экзамен',
                'Зачет' => 'Зачет',
                'Зачёт' => 'Зачет',
                'Дифференцированный зачёт' => 'Дифф. зачет',
                'Дифференцированный зачет' => 'Дифф. зачет',
                'Диф зачёт' => 'Дифф. зачет',
                'Диф зачет' => 'Дифф. зачет',
                'Курсовая работа' => 'Курсовая работа',
                'Курсовой проект' => 'Курсовой проект'
            ];

            $type_control_ass = $type_control_map[$type_control] ?? 'null';

            if($type_debt == 'удалить'){
                $stmt = $this->pdo->prepare("CALL delete_debt(:id_student, :name_disp, :number_from_start, :assessment_type)");
                $stmt->execute([
                    'id_student' => $id_isu,
                    'name_disp' => $name_disp,
                    'number_from_start' => $number_from_start,
                    'assessment_type' => $type_control_ass,
                ]);
                continue;
            }


            $type_debt_form = $type_debt_map[$type_debt] ?? 'обычный';

            if (!in_array($type_control_ass, ['Экзамен', 'Зачет', 'Дифф. зачет', 'Курсовая работа', 'Курсовой проект'])) {
                $response[] = [
                    'status' => 'error',
                    'message' => "❌ Invalid control type '$type_control_ass' for student $id_isu (Flow: $name_disp)."
                ];
                continue;
            }

            try {
                $stmt = $this->pdo->prepare("CALL insert_debt(:id_student, :name_disp, :type_debt_enum, :number_from_start, :assessment_type, :year, :semester, :comment)");
                $stmt->execute([
                    'id_student' => $id_isu,
                    'name_disp' => $name_disp,
                    'type_debt_enum' => $type_debt_form,
                    'number_from_start' => $number_from_start,
                    'assessment_type' => $type_control_ass,
                    'year' => $year,
                    'semester' => $semester,
                    'comment' => $comment
                ]);
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Error importing student $id_isu: " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт debts завершён успешно!'
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
