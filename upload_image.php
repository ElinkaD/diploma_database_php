<?php
// Путь до папки для загрузки
$uploadDir = './import_tables_csv/';

// Проверяем, был ли загружен файл
if (isset($_FILES['file'])) {
    $fileTmp = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $targetFile = $uploadDir . $fileName;

    // Проверка на ошибки при загрузке
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo "Ошибка при загрузке файла: " . $_FILES['file']['error'];
        exit;
    }

    // Проверка на тип файла (например, только изображения)
    $fileType = mime_content_type($fileTmp);
    if (strpos($fileType, 'image') === false) {
        echo "Файл не является изображением.";
        exit;
    }

    // Перемещаем файл в целевую папку
    if (move_uploaded_file($fileTmp, $targetFile)) {
        echo "Файл успешно загружен: <a href='$targetFile'>$fileName</a>";
    } else {
        echo "Не удалось сохранить файл.";
    }
} else {
    echo "Файл не был загружен.";
}
?>
