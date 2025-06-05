<div id="subject-list-component">
  <h1>Форма для расчета академ разницы</h1>

  <div class="filters">
    <div class="filter-field">
      <label for="id_isu">Таб. номер</label>
      <input type="number" id="id_isu">
    </div>

    <button id="filter-button" style="margin-top: 24px;">Поиск</button>
  </div>

  <div id="status-list"></div>
  <div id="statuses-container" class="hidden" style="margin-top: 20px;">
    <select id="status-select"></select>
  </div>
  <div id="akadem-list"></div>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>
