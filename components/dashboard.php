<?php
if (!isset($_SESSION['token'])):
?>

    <h2>Добро пожаловать!</h2>
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

    <h2>Личный кабинет</h2>
    <p>Добро пожаловать, <?= htmlspecialchars($_SESSION['login']) ?>!</p>

    <p><a href="./server/api/logout.php">Выйти из аккаунта</a></p>

<?php endif; ?>
