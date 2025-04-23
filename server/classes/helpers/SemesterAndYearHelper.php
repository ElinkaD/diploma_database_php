<?php
class SemesterAndYearHelper {
    public static function getSemesterAndYear(int $flag): array {
        $month = (int)date('n'); 
        $year = (int)date('Y');
        $currentSemester = ($month >= 1 && $month <= 6) ? 'весна' : 'осень'; 
        
        if ($flag === -1) { // предыдущий семестр
            if ($currentSemester === 'весна') {
                return ['semester' => 'осень', 'year' => $year - 1];
            } else {
                return ['semester' => 'весна', 'year' => $year];
            }
        } elseif ($flag === 1) { // следующий семестр
            if ($currentSemester === 'осень') {
                return ['semester' => 'весна', 'year' => $year + 1];
            } else {
                return ['semester' => 'осень', 'year' => $year];
            }
        }

        // текущий семестр
        return ['semester' => $currentSemester, 'year' => $year];
    }
}

