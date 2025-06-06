
<div id="student-info">
  <a href="#" id="back-to-students">Назад к списку студентов</a>
  <h1 id="student-title">Студент:</h1>

  <div>
  <div id="student-container"></div>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
  <div id="individual-plan">
    <h3>Индивидуальный план</h3>
  </div>
</div>

<script>
document.getElementById('back-to-students').addEventListener('click', () => {
    window.location.href = `?page=StudentsList`;
});
</script>

