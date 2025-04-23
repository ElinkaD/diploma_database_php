<?php
$currentYear = date('Y');
?>

<h2>Импорт данных</h2>

<form id="import-form">
    <label for="type">Тип импорта:</label>
    <select id="type" required>
        <option value="">Выберите...</option>
        <option value="portfolio">Портфолио</option>
        <option value="individua_plan_number">Учебные планы</option>
        <option value="flows">Потоки</option>
        <option value="st_in_flows">Студенты в потоке</option>
        <option value="group_track">Группа треков</option>
        <option value="debts">Долги</option>
        <option value="ip_subjects">ИП</option>
        <option value="nagruzka">Нагрузка</option>
        <option value="teachers">Преподаватели</option>
        <option value="rpd_teachers">РПД автор</option>
        <option value="vkr">ВКР</option>
        <option value="mentors">Менторы</option>
        <option value="kpi">KPI</option>
        <option value="salary">Ставки</option>
    </select><br><br>

    <div class="form-group hidden" id="semester-group">
        <label for="semester">Семестр:</label>
        <select id="semester">
            <option value="">Текущий</option>
            <option value="весна">Весна</option>
            <option value="осень">Осень</option>
        </select><br><br>
    </div>

    <div class="form-group hidden" id="year-group">
        <label for="year">Год:</label>
        <select id="year">
            <option value="">Текущий</option>
            <?php for ($i = $currentYear - 4; $i <= $currentYear + 4; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select><br><br>
    </div>

    <input type="file" id="file" required><br><br>

    <button type="submit">Импортировать</button>
</form>

<div id="import-response"></div>
