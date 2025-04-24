<?php
$currentYear = date('Y');
?> 

<div id="flows-list-component">
  <h2>Список потоков</h2>

  <!-- <input type="text" id="flow-search" placeholder="Поиск по названию потока..."> -->

  <div>
    <label for="semester">Семестр:</label>
    <select id="semester">
        <option value="">Текущий</option>
        <option value="весна">Весна</option>
        <option value="осень">Осень</option>
    </select><br><br>

    <label for="year">Год:</label>
    <select id="year">
        <option value="">Текущий</option>
        <?php for ($i = $currentYear - 4; $i <= $currentYear + 4; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select><br><br>

    <button id="filter-button">Поиск</button>
  </div>

  <ul id="flows-list"></ul>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>
