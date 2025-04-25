<?php
$currentPage = $_GET['page'] ?? 'dashboard';

$menuItems = [
    'dashboard' => ['label' => 'Главная', 'icon' => 'expulsion.svg'],
    'import' => ['label' => 'Импорт данных', 'icon' => 'upload-file.svg'],
    'FlowsList' => ['label' => 'Потоки', 'icon' => 'cross.svg'],
    'StudentsList' => ['label' => 'Студенты', 'icon' => 'users.svg'],
    'TeachersList' => ['label' => 'Преподаватели', 'icon' => 'lecture.svg'],
    'other' => ['label' => 'Другое', 'icon' => 'menu-services.svg'],
  ];
?>  

<nav class="sidebar">
  <ul>
    <?php foreach ($menuItems as $menuPage => $data): ?>
    <li>
        <a 
        href="?page=<?= $menuPage ?>" 
        data-tab="<?= $menuPage ?>" 
        class="text-big-regular <?= $currentPage === $menuPage ? 'active' : '' ?>"
        >
        <img 
            src="img/<?= $data['icon'] ?>" 
            alt="<?= $data['label'] ?>" 
            class="<?= $currentPage === $menuPage ? 'active' : '' ?>"
        >
        <?= $data['label'] ?>
        </a>
    </li>
    <?php endforeach; ?>
  </ul>
</nav>