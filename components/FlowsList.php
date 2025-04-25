<?php
$currentYear = date('Y');
?> 

<div id="flows-list-component">
  <h1>Список потоков</h1>

  <!-- <input type="text" id="flow-search" placeholder="Поиск по названию потока..."> -->

  <div class="filters">
    <div class="filter-field">
      <label for="semester" class="text-base">Семестр</label>
      <select id="semester">
          <option value="">Текущий</option>
          <option value="весна">Весна</option>
          <option value="осень">Осень</option>
      </select>
    </div>

    <div class="filter-field">
      <label for="year" class="text-base">Год</label>
      <select id="year">
          <option value="">Текущий</option>
          <?php for ($i = $currentYear - 4; $i <= $currentYear + 4; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?></option>
          <?php endfor; ?>
      </select>
    </div>

    <button id="filter-button">Поиск</button>
  </div>

  <div id="flows-list"></div>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>
