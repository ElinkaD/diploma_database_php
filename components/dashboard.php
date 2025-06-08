<!-- <h1 style="margin-bottom: 0;">Главная страница</h1> -->

<?php
if (!isset($_SESSION['token'])):
?>

    <h1>Добро пожаловать!</h1>
    <p>Пожалуйста, войдите в систему, чтобы получить доступ к дополнительным возможностям.</p>

    <form method="post" action="auth.php" id="auth-form">
        <input type="text" name="login" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>

    <script>
        const form = document.getElementById('auth-form');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const response = await fetch('./server/api/auth.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                location.reload();
            } else {
                alert(result.message);
            }
        });
    </script>

<?php else: ?>
    <div class="kabinet">
        <h1>Добро пожаловать, <?= htmlspecialchars($_SESSION['login']) ?>!</h1>
        <button><a href="./server/api/logout.php">Выйти из аккаунта</a></button>
    </div>
<?php endif; ?>


<footer>
    <p>Данная веб-разработка выполнена в&nbsp;рамках выпускной квалификационной работы.</p>

    <p><strong>Тема:</strong> &laquo;Проектирование базы данных и&nbsp;разработка инструмента визуализации<br>расчёта нагрузки преподавателей с&nbsp;использованием веб-технологий&raquo;.</p>
    <p><strong>Автор:</strong> Дусаева Элина</p>
    <p><strong>Год выпуска:</strong> 2025</p>
    <p><strong>Факультет:</strong> Программной инженерии и&nbsp;компьютерных технологий</p>
    <p><strong>Направление подготовки:</strong> Компьютерные технологии в&nbsp;дизайне</p>

    <p>Ссылка на&nbsp;первую часть проекта, посвящённую учебным планам:<br>
        <a href="https://se.ifmo.ru/~s335141/dev/frontend/dist" target="_blank">https://se.ifmo.ru/~s335141/dev/frontend/dist</a>
    </p>

    <p><a href="https://t.me/linaroon" target="_blank">Связаться с&nbsp;автором</a></p>
</footer>
