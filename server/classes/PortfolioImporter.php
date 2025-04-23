<?php
require_once 'Importer.php';
function insertGroup($pdo, $groupNumber, $course)
{
    $stmt = $pdo->prepare("SELECT insert_group(:groupNumber, :course) AS group_id");
    $stmt->execute(['groupNumber' => $groupNumber, 'course' => $course]);
    $result = $stmt->fetch();

    return $result['group_id'];
}

function insertStudent($pdo, $id_isu, $fio, $citizenship, $comment)
{
    $stmt = $pdo->prepare("CALL insert_student(:id_isu, :fio, :citizenship, :comment)");
    $stmt->execute([
        'id_isu' => $id_isu,
        'fio' => $fio,
        'citizenship' => $citizenship,
        'comment' => $comment
    ]);
}
class PortfolioImporter extends SemesterImporter {
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
            if (empty($row[1]) || empty($row[4]) || empty($row[11])) {
                $response[] = [
                    'status' => 'warning',
                    'message' => "Пропущена строка из-за отсутствия обязательных данных (id_isu, group, status)."
                ];
                continue;
            }
    
            $id_isu = (int)$row[1];
            $fio = $row[2];
            $citizenship = $row[3];
            $group = $row[4];
            $course = (int)$row[5];
            $education_form_raw = mb_strtolower($row[10] ?? '');
            $status_raw = mb_strtolower(trim($row[11]));
            $commentStudent = $row[18] ?? '';
            $commentStatus = $row[19] ?? '';
            $trackName = $row[20] ?? 'test'; 
    
            $education_form_map = [
                'бюджет' => 'бюджет',
                'б' => 'бюджет',
                'контракт' => 'контракт',
                'к' => 'контракт'
            ];
    
            $education_form = $education_form_map[$education_form_raw] ?? null;
    
            $status_map = [
                'обучается' => 'обучается',
                'мд' => 'мд',
                'академический отпуск' => 'академ',
                'академ' => 'академ',
                'отчислен' => 'отчислен',
                'перевелся от нас' => 'перевёлся_от_нас',
                'перевелся к нам' => 'перевёлся_к_нам',
                'перевёлся от нас' => 'перевёлся_от_нас',
                'перевёлся к нам' => 'перевёлся_к_нам'
            ];
            
            $status = $status_map[$status_raw] ?? null;
            
            if (!$status) {
                $response[] = [
                    'status' => 'error',
                    'message' => "⚠️ Неизвестный статус студента $id_isu (ФИО: $fio): '$status_raw'"
                ];
            }
    
            try {
                insertStudent($this->pdo, $id_isu, $fio, $citizenship, $commentStudent);
                $group_id = insertGroup($this->pdo, $group, $course);
    
                $stmt = $this->pdo->prepare("CALL insert_student_status(:id_student, :status, :education_form, :comment, :id_group, :track_name, :semester, :year)");
                $stmt->execute([
                    'id_student' => $id_isu,
                    'status' => $status,
                    'education_form' => $education_form,
                    'comment' => $commentStatus,
                    'id_group' => $group_id,
                    'track_name' => $trackName,
                    'semester' => $semester,
                    'year' => $year
                ]);
                
                // echo "Successfully imported: $id_isu - $fio\n";
            } catch (PDOException $e) {
                $response[] = [
                    'status' => 'error',
                    'message' => "Ошибка импорта студента $id_isu (ФИО: $fio): " . $e->getMessage()
                ];
                continue;
            }
        }

        fclose($file);
        if (empty($response)) {
            $response[] = [
                'status' => 'success',
                'message' => 'Импорт студентов завершён успешно.'
            ];
        } else {
            $response[] = [
                'status' => 'error',
                'message' => 'Импорт студентов завершён с ошибками. Проверьте сообщения выше.'
            ];
        }

        echo json_encode($response);
    }
}