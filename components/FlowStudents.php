
<div id="flow-students">
  <a href="#" id="back-to-flows">Назад к списку потоков</a>
  <h1 id="flow-students-title">Студенты потока:</h1>

  <div class="filters">
    <!-- <input type="text" id="student-search" placeholder="Поиск по ФИО..."> -->
    <select id="education-form-filter">
      <option value="бюджет">Бюджет</option>
      <option value="контракт">Контракт</option>
    </select>

    <button id="filter-button">Поиск</button>
  </div>
  

  <div id="flow-students-container"></div>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>

<script>
document.getElementById('back-to-flows').addEventListener('click', () => {
    window.location.href = `?page=FlowsList`;
});
</script>