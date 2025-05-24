<?php
session_start();

$page = $_GET['page'] ?? 'dashboard';
$publicPages = ['dashboard'];

if (!isset($_SESSION['token']) && !in_array($page, $publicPages)) {
    header('Location: index.php?page=dashboard');
    exit;
}

include 'partials/header.php';
include 'partials/menu.php';

echo '<main class="content" id="main-content">';
$componentPath = "./components/{$page}.php";
if (file_exists($componentPath)) {
    include $componentPath;
} else {
    echo '<p>Страница не найдена</p>';
}
echo '</main>';

echo '<div id="notifications-container" class="notifications-container"></div>';

include 'partials/footer.php';
