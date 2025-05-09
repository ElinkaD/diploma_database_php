
<div id="teacher-info">
  <a href="#" id="back-to-teachers">Назад к списку преподавателей</a>
  <h1 id="teacher-title">Преподаватель:</h1>

  <div>
  <div id="teacher-container"></div>

  <h3 id="workload-title">Нагрузка</h3>
  <div class="filters">
    <div class="filter-field">
        <label for="type">Фильтр нагрузки</label>
        <select id="type" required>
            <option value="">Все...</option>
            <option value="1">Следующий семестр</option>
            <option value="2">Текущий учебный год</option>
            <option value="3">Следующий учебный год</option>
            <option value="4">Весь период нового учебного плана с действующими планами</option>
        </select>
    </div>
    <div class="form-group hidden filter-field" id="plan-group">
        <label for="plan">Название учебного плана</label>
        <select id="plan">
            <option value="">Выберите учебный план</option>
        </select>
    </div>
    <button id="filter-button" style="margin-top: 24px;">Поиск</button>
  </div>
  <div id="workload-container"></div>

  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>

<script>
document.getElementById('back-to-teachers').addEventListener('click', () => {
    window.location.href = `?page=TeachersList`;
});
</script>

