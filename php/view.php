<?php
require_once __DIR__ . '/../function/db_connect.php';

function showTable($pdo, $tableName)
{
    echo "<h2>Таблица: $tableName</h2>";
    $stmt = $pdo->query("SELECT * FROM \"$tableName\"");

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    $firstRow = true;
    foreach ($stmt as $row) {
        if ($firstRow) {
            echo "<tr>";
            foreach (array_keys($row) as $col) {
                echo "<th>" . htmlspecialchars($col) . "</th>";
            }
            echo "</tr>";
            $firstRow = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}


showTable($pdo, 'students');
showTable($pdo, 'student_statuses');
showTable($pdo, 'groups');
showTable($pdo, 'flows');
showTable($pdo, 'students_in_flows');
showTable($pdo, 'kpi');