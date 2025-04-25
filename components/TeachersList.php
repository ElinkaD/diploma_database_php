<?php 
$currentYear = date('Y');
?> 

<div id="students-list-component">
  <h1>Список студентов</h1>

  <div class="filters">
    <div class="filter-field">
      <label for="id_isu">Таб. номер</label>
      <input type="number" id="id_isu">
    </div>

    <div class="filter-field">
      <label for="fio">ФИО</label>
      <input type="text" id="fio">
    </div>

    <div class="filter-field">
      <label for="group">Группа</label>
      <input type="text" id="group">
    </div>

    <div class="filter-field">
      <label for="track">Трек</label>
      <input type="text" id="track">
    </div>

    <div class="filter-field">
      <label for="status">Статус</label>
      <select id="status">
        <option value="">Любой</option>
        <option value="обучается">Обучается</option>
        <option value="мд">МД</option>
        <option value="академ">Академ</option>
        <option value="отчислен">Отчислен</option>
        <option value="перевёлся_от_нас">Перевёлся от нас</option>
        <option value="перевёлся_к_нам">Перевёлся к нам</option>
      </select>
    </div>

    <div class="filter-field">
      <label for="education-form">Форма обучения</label>
      <select id="education-form">
        <option value="">Любая</option>
        <option value="бюджет">Бюджет</option>
        <option value="контракт">Контракт</option>
      </select>
    </div>

    <div class="filter-field">
      <label for="semester">Семестр</label>
      <select id="semester">
        <option value="">Любой</option>
        <option value="весна">Весна</option>
        <option value="осень">Осень</option>
      </select>
    </div>

    <div class="filter-field">
      <label for="year">Год</label>
      <select id="year">
        <option value="">Любой</option>
        <?php for ($i = $currentYear - 4; $i <= $currentYear + 4; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="filter-field">
      <label for="citizenship">Гражданство</label>
      <input type="text" id="citizenship">
    </div>

    <button id="filter-button" style="margin-top: 24px;">Поиск</button>
  </div>

  <div id="students-list"></div>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>
