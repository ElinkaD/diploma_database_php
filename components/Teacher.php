
<div id="teacher-info">
  <a href="#" id="back-to-teachers">Назад к списку преподавателей</a>
  <h1 id="teacher-title">Преподаватель:</h1>

  <div>
  <div id="teacher-container"></div>
  <div id="no-results-message" class="hidden">Ничего не найдено</div>
</div>

<script>
document.getElementById('back-to-teachers').addEventListener('click', () => {
    window.location.href = `?page=TeachersList`;
});
</script>

