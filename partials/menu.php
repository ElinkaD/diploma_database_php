<?php
$currentPage = $_GET['page'] ?? 'dashboard';

$menuItems = [
    'dashboard' => ['label' => 'Главная', 'icon' => 'menu-home.svg'],
    'FlowsList' => ['label' => 'Потоки', 'icon' => 'cross.svg'],
    'StudentsList' => ['label' => 'Студенты', 'icon' => 'users.svg'],
    'TeachersList' => ['label' => 'Преподаватели', 'icon' => 'lecture.svg'],
    'Akadem' => ['label' => 'Академ', 'icon' => 'pencil-ruler.svg'],
    'other' => ['label' => 'Другое', 'icon' => 'menu-services.svg'],
  ];

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
if ($isAdmin) {
    $menuItems['import'] = ['label' => 'Импорт данных', 'icon' => 'upload-file.svg'];
    $menuItems['Adminer'] = [
        'label' => 'Adminer',
        'icon' => 'settings.svg',
        'external' => 'https://se.ifmo.ru/~s338859/diploma_database_php/adminer/adminer-5.2.1.php?pgsql=helios.cs.ifmo.ru&username=s338859&db=studs'
    ];
}
?>  

<nav class="sidebar">
  <img src="img/logo_itmo.png" alt="Логотип ИТМО" class="logo">
  <ul>
    <?php foreach ($menuItems as $menuPage => $data): ?>
      <li>
        <?php if (isset($data['external'])): ?>
          <a 
            href="<?= htmlspecialchars($data['external']) ?>" 
            target="_blank" 
            class="text-big-regular"
          >
            <img src="img/<?= $data['icon'] ?>" alt="<?= $data['label'] ?>">
            <?= $data['label'] ?>
          </a>
        <?php else: ?>
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
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>