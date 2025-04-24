
<div id="flow-students">
  <a href="#" id="back-to-flows">Назад к списку потоков</a>
  <h2 id="flow-students-title">Студенты потока:</h2>

  <div>
    <!-- <input type="text" id="student-search" placeholder="Поиск по ФИО..."> -->
    <select id="education-form-filter">
      <option value="бюджет">Бюджет</option>
      <option value="контракт">Контракт</option>
    </select>

    <button id="filter-button">Поиск</button>
  </div>
  

  <ul id="flow-students-container"></ul>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>

<script>
document.getElementById('back-to-flows').addEventListener('click', () => {
    window.location.href = `?page=FlowsList`;
});
</script>