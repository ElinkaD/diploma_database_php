<?php
$currentYear = date('Y');
?>

<h1>Импорт данных</h1>

<p>
    <a href="https://docs.google.com/spreadsheets/d/10RlD4E3SFGWekg3DRQa0tFkAyisB0pYNCPGDVai09_E/edit?usp=sharing" target="_blank">Таблица с шаблонами импорта</a><br>
    <a href="https://docs.google.com/document/d/1e9HsgtZnxr0KgyiZzjaCPtEF5QXcvugv8PakfwDVvHk/edit?usp=sharing" target="_blank">Инструкция по работе с шаблонами</a>
</p>

<form id="import-form">
    <div class="filter-field">
        <label for="type">Тип импорта</label>
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
            <option value="disciplines_of_teachers">Дисциплины преподавателей</option>
        </select>
    </div>

    <div class="form-group hidden filter-field" id="plan-group">
        <label for="plan">Название учебного плана</label>
        <select id="plan">
            <option value="">Выберите учебный план</option>
        </select>
    </div>


    <div class="form-group hidden filter-field" id="semester-group">
        <label for="semester">Семестр</label>
        <select id="semester">
            <option value="">Текущий</option>
            <option value="весна">Весна</option>
            <option value="осень">Осень</option>
        </select>
    </div>

    <div class="form-group hidden filter-field" id="year-group">
        <label for="year">Год</label>
        <select id="year">
            <option value="">Текущий</option>
            <?php for ($i = $currentYear - 4; $i <= $currentYear + 4; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="filter-field">
        <label for="file">Загрузка файла</label>

        <div id="drop-zone" class="default">
            <input type="file" id="file" required />
            <div class="upload" id="upload-hint">
                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 4.75208C9.19057 4.75208 6.79026 6.46931 5.77366 8.90634C3.1937 9.46817 1.25 11.7443 1.25 14.5021C1.25 17.6773 3.82479 20.2521 7 20.2521H18C20.6232 20.2521 22.75 18.1253 22.75 15.5021C22.75 13.1217 20.9988 11.1501 18.7145 10.8055C18.3659 7.40461 15.493 4.75208 12 4.75208ZM12 6.25208C9.69501 6.25208 7.73911 7.74021 7.034 9.81174C6.94247 10.0806 6.70689 10.2748 6.42546 10.3132C4.3475 10.5969 2.75 12.3576 2.75 14.5021C2.75 16.8489 4.65321 18.7521 7 18.7521H18C19.7948 18.7521 21.25 17.2969 21.25 15.5021C21.25 13.7073 19.7948 12.2521 18 12.2521C17.5858 12.2521 17.25 11.9163 17.25 11.5021C17.25 8.60229 14.8998 6.25208 12 6.25208Z" fill="#5C5C5C"/>
                    <path d="M12.5303 10.4717C12.2374 10.1789 11.7626 10.1789 11.4697 10.4717L9.30267 12.6387C9.00978 12.9316 9.00978 13.4065 9.30267 13.6994C9.59556 13.9923 10.0704 13.9923 10.3633 13.6994L11.25 12.8127V16.0021C11.25 16.4163 11.5858 16.7521 12 16.7521C12.4142 16.7521 12.75 16.4163 12.75 16.0021V12.8127L13.6367 13.6994C13.9296 13.9923 14.4044 13.9923 14.6973 13.6994C14.9902 13.4065 14.9902 12.9316 14.6973 12.6387L12.5303 10.4717Z" fill="#5C5C5C"/>
                </svg>
                <p>Перетащите или <span id="browse">выберите</span> файл</p>
            </div>
            
            <div id="uploaded" class="upload hidden">
                <div class="upload">
                    <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect y="0.207458" width="32" height="32" rx="6" fill="#EDF0FB"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8333 11.5408C10.8333 10.528 11.6539 9.70746 12.6667 9.70746H17.448C17.9341 9.70746 18.4006 9.90091 18.7442 10.2446L20.6296 12.1299C20.9732 12.4736 21.1667 12.94 21.1667 13.4261V20.8741C21.1667 21.8869 20.3462 22.7075 19.3333 22.7075H12.6667C11.6539 22.7075 10.8333 21.8869 10.8333 20.8741V11.5408ZM11.8333 11.5408C11.8333 11.0803 12.2062 10.7075 12.6667 10.7075H16.8333V12.8741C16.8333 13.5183 17.3559 14.0408 18 14.0408H20.1667V20.8741C20.1667 21.3347 19.7939 21.7075 19.3333 21.7075H12.6667C12.2062 21.7075 11.8333 21.3347 11.8333 20.8741V11.5408ZM19.9225 12.837L18.0371 10.9517C17.9766 10.8912 17.9078 10.841 17.8333 10.8021V12.8741C17.8333 12.966 17.9082 13.0408 18 13.0408H20.072C20.0332 12.9663 19.9829 12.8975 19.9225 12.837Z" fill="#1846C7"/>
                    </svg>
                    <a id="uploaded-file" href="#" target="_blank"></a>
                </div>
                <button type="button" id="remove-file" title="Удалить файл"><svg data-v-581db1f2="" viewBox="0 0 24 24" width="1.5rem" height="1.5rem" focusable="false" role="img" aria-label="bin" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-bin text-danger b-icon bi"><g data-v-581db1f2=""><path d="M9.18634 3.74625H14.8137C15.021 3.74625 15.1891 3.91435 15.1891 4.12172V5.62265H8.81087V4.12172C8.81087 3.91435 8.97897 3.74625 9.18634 3.74625ZM16.6891 5.62265V4.12172C16.6891 3.08592 15.8495 2.24625 14.8137 2.24625H9.18634C8.15054 2.24625 7.31087 3.08592 7.31087 4.12172V5.62265H3.99667C3.58246 5.62265 3.24667 5.95844 3.24667 6.37265C3.24667 6.78687 3.58246 7.12265 3.99667 7.12265H4.55267L5.46501 18.983C5.58528 20.5465 6.889 21.7537 8.4571 21.7537H15.5429C17.111 21.7537 18.4147 20.5465 18.535 18.983L19.4473 7.12265H20.0033C20.4176 7.12265 20.7533 6.78687 20.7533 6.37265C20.7533 5.95844 20.4176 5.62265 20.0033 5.62265H16.6891ZM6.0571 7.12265H17.9429L17.0394 18.8679C16.9793 19.6499 16.3272 20.2537 15.5429 20.2537H8.4571C7.67281 20.2537 7.02074 19.6499 6.96059 18.8679L6.0571 7.12265ZM10.0304 10.1245C10.4447 10.1245 10.7804 10.4603 10.7804 10.8745V16.5019C10.7804 16.9161 10.4447 17.2519 10.0304 17.2519C9.61622 17.2519 9.28044 16.9161 9.28044 16.5019V10.8745C9.28044 10.4603 9.61622 10.1245 10.0304 10.1245ZM13.9696 10.1245C14.3838 10.1245 14.7196 10.4603 14.7196 10.8745V16.5019C14.7196 16.9161 14.3838 17.2519 13.9696 17.2519C13.5554 17.2519 13.2196 16.9161 13.2196 16.5019V10.8745C13.2196 10.4603 13.5554 10.1245 13.9696 10.1245Z" fill="currentColor"></path></g></svg></button>
            </div>
        </div>
    </div>

    <button type="submit">Импортировать</button>
</form>

<!-- <div id="import-response"></div> -->
