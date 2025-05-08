<?php 
$currentYear = date('Y');
?> 

<div id="students-list-component">
  <h1>Список преподавателей</h1>

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
      <label for="discipline">Название дисциплины</label>
      <input type="text" id="discipline">
    </div>

    <div class="filter-field">
      <label for="id_rpd">ID RPD</label>
      <input type="number" id="id_rpd">
    </div>

    <div class="filter-field">
      <label for="status_rpd">Статус РПД</label>
      <select id="status_rpd">
        <option value="">Любой</option>
        <option value="новая">Новая</option>
        <option value="черновик">Черновик</option>
        <option value="в работе">В работе</option>
        <option value="на доработке">На доработке</option>
        <option value="одобрена">Одобрена</option>
        <option value="на экспертизе">На экспертизе</option>
        <option value="на подписи">На подписи</option>
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
        <?php for ($i = $currentYear - 4; $i <= $currentYear + 10; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="filter-field">
      <label for="workload_type">Тип нагрузки</label>
      <select id="workload_type">
        <option value="">Любой</option>
        <option value="Лек">Лекции</option>
        <option value="Пр">Практические</option>
        <option value="Лаб">Лабораторные</option>
        <option value="К">Консультации</option>
        <option value="У">УСРС</option>
      </select>
    </div>

    <button id="filter-button" style="margin-top: 24px;">Поиск</button>
  </div>

  <div id="teachers-list"></div>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>
