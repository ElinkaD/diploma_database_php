<?php
require_once 'Importer.php';
function insertGroup($pdo, $groupNumber, $course)
{
    $stmt = $pdo->prepare("SELECT insert_group(:groupNumber, :course) AS group_id");
    $stmt->execute(['groupNumber' => $groupNumber, 'course' => $course]);
    $result = $stmt->fetch();

    return $result['group_id'];
}

function insertStudent($pdo, $id_isu, $fio, $citizenship, $comment, $code)
{
    $stmt = $pdo->prepare("CALL insert_student(:id_isu, :fio, :citizenship, :comment, :code)");
    $stmt->execute([
        'id_isu' => $id_isu,
        'fio' => $fio,
        'citizenship' => $citizenship,
        'comment' => $comment,
        'code' => $code
    ]);
}
class PortfolioImporter extends SemesterImporter {
    public function import(): void {
        throw new Exception("Метод import() не используется. Используй importWithSemester().");
    }

    public function importWithSemester(string $semester, int $year): void {
        throw new Exception("Метод importWithSemester() не используется. Используй importWithSemesterPortfolio().");
    }

    public function importWithSemesterPortfolio(string $semester, int $year, int $plan_id): void {
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
            $code = mb_strtolower(trim($row[6]));
            $education_form_raw = mb_strtolower($row[10] ?? '');
            $status_raw = mb_strtolower(trim($row[11]));
            $commentStudent = $row[18] ?? '';
            $commentStatus = $row[19] ?? '';
            $trackNumber = (int)$row[20]; 
    
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

            $specialtyRaw = trim($row[6] ?? '');
            $code = preg_match('/^(\d{2}\.\d{2}\.\d{2})/', $specialtyRaw, $matches) 
                ? $matches[1]  
                : null;

            if (empty($code)) {
                $response[] = [
                    'status' => 'warning',
                    'message' => "Не удалось распарсить код специальности для студента $id_isu: '$specialtyRaw'"
                ];
            }
    
            try {
                insertStudent($this->pdo, $id_isu, $fio, $citizenship, $commentStudent, $code);
                $group_id = insertGroup($this->pdo, $group, $course);
    
                $stmt = $this->pdo->prepare("CALL insert_student_status(:plan_id, :id_student, :status, :education_form, :comment, :id_group, :track_number, :semester, :year)");
                $stmt->execute([
                    'plan_id' => $plan_id,
                    'id_student' => $id_isu,
                    'status' => $status,
                    'education_form' => $education_form,
                    'comment' => $commentStatus,
                    'id_group' => $group_id,
                    'track_number' => $trackNumber,
                    'semester' => $semester,
                    'year' => $year
                ]);
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
                'status' => 'warning',
                'message' => 'Импорт студентов завершён с ошибками. Проверьте сообщения выше.'
            ];
        }

        echo json_encode($response);
    }
}