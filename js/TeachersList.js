const teachersList = document.getElementById('teachers-list');
const noResultsMessage = document.getElementById('no-results-message'); 

const idInput = document.getElementById('id_isu');
const fioInput = document.getElementById('fio');
const disciplineInput = document.getElementById('discipline');
const idRpdInput = document.getElementById('id_rpd');
const statusSelect = document.getElementById('status_rpd');
const semesterSelect = document.getElementById('semester');
const yearSelect = document.getElementById('year');
const workloadTypeSelect = document.getElementById('workload_type');
const searchButton = document.getElementById('filter-button');

searchButton.addEventListener('click', async () => {
  let params = new URLSearchParams();

  if (idInput.value.trim()) params.append('id_isu', idInput.value.trim());
  if (fioInput.value.trim()) params.append('fio', fioInput.value.trim());
  if (disciplineInput.value.trim()) params.append('discipline', disciplineInput.value.trim());
  if (idRpdInput.value.trim()) params.append('id_rpd', idRpdInput.value.trim());
  if (statusSelect.value) params.append('status_rpd', statusSelect.value);
  if (semesterSelect.value) params.append('semester', semesterSelect.value);
  if (yearSelect.value) params.append('year', yearSelect.value);
  if (workloadTypeSelect.value) params.append('workload_type', workloadTypeSelect.value);

  try {
    const res = await fetch(`./server/api/TeachersList.php?${params.toString()}`);
    const teachers = await res.json();
    if (!Array.isArray(teachers)) {
      console.error('Ожидался массив, получено:', teachers);
      showNotification('warning', 'Ошибка: неверный формат данных');
      return;
    }
    renderTeachers(teachers);
  } catch (err) {
    // teachersList.innerHTML = 'Ошибка при фильтрации: ' + err.message;
    showNotification('error', 'Ошибка при фильтрации: ' + err.message);
  }
});


async function fetchTeachers() {
  try {
    const res = await fetch('./server/api/TeachersList.php');
    const teachers = await res.json();
    console.log('📥 Ответ:', teachers);
    renderTeachers(teachers);
  } catch (err) {
    // teachersList.innerHTML = 'Ошибка загрузки преподавателей: ' + err.message;
    showNotification('error', 'Ошибка загрузки преподавателей: ' + err.message);
  }
}


function renderTeachers(teachers) {
  teachersList.innerHTML = '';

  if (teachers.length === 0) {
    noResultsMessage.classList.remove('hidden');
    return;
  }

  noResultsMessage.classList.add('hidden');

  const table = document.createElement('table');
  table.classList.add('data-table');

  const thead = document.createElement('thead');
  thead.innerHTML = `
    <tr class="text-table-header">
      <th>Таб. номер</th>
      <th>Преподаватель</th>
      <th>Год переизбрания</th>
      <th>Срок</th>
      <th>Комментарий</th>
      <th>Общая нагрузка за текущий семестр</th>
      <th></th>
    </tr>
  `;
  table.appendChild(thead);

  const tbody = document.createElement('tbody');

  teachers.forEach((t, index) => {
    const row = document.createElement('tr');
    row.classList.add('teacher-row');

    row.innerHTML = `
      <td>${t.id_isu ?? ''}</td>
      <td>
        <span class="clickable" style="font-weight: 600;">
          ${t.fio}
        </span>
      </td>
      <td>${t.re_election_year ?? ''}</td>
      <td>${t.term ?? ''}</td>
      <td>${t.comment ?? ''}</td>
      <td>${t.total_hours ?? 0}</td>
      <td>
        <button class="toggle-details" data-index="${index}" style="background:none; border:none; cursor:pointer;">
          <img src="./img/arrow-down.svg" alt="Развернуть" class="toggle-icon">
        </button>
      </td>    `;

    const detailRow = document.createElement('tr');
    detailRow.style.display = 'none';

    const rpdList = (t.workload ?? []).map(r => {
    const isSpring = r.semester?.toLowerCase() === 'весна';
    const semesterClass = isSpring ? 'spring-semester' : '';
    
    return `
      <tr class="${semesterClass}">
        <td>${r.discipline ?? ''}</td>
        <td>${r.id_rpd ?? ''}</td>
        <td>${r.status_rpd ?? ''}</td>
        <td>${r.semester ?? ''}</td>
        <td>${r.year ?? ''}</td>
        <td>${r.count_hours ?? ''}</td>
        <td>${r.count_student ?? ''}</td>
        <td>${r.comment ?? ''}</td>
        <td>${r.workload_type ?? ''}</td>
        <td>${r.assessment_type ?? ''}</td>
      </tr>
    `;
  }).join('');


    detailRow.innerHTML = `
      <td colspan="7" style="padding: 0;">
        <div class="inner-table-container" style="display: flex; border-radius: 6px;">
          <div class="left-border"></div>
          <table class="inner-table">
            <thead>
                <tr class="text-table-header">
                  <th>Дисциплина</th>
                  <th>ID РПД</th>
                  <th>Статус РПД</th>
                  <th>Семестр</th>
                  <th>Год</th>
                  <th>Часы</th>
                  <th>Количество студентов</th>
                  <th>Комментарий</th>
                  <th>Тип нагрузки</th>
                  <th>Тип контроля</th>
                </tr>
              </thead>
            <tbody>
              ${rpdList}
            </tbody>
          </table>
        </div>
      </td>
    `;
    tbody.appendChild(row);
    tbody.appendChild(detailRow);

    row.querySelector('.clickable').addEventListener('click', () => {
      window.location.href = `?page=Teacher&id_isu=${encodeURIComponent(t.id_isu)}&fio=${encodeURIComponent(t.fio)}`;
    });

    const toggleButton = row.querySelector('.toggle-details');
    const toggleIcon = toggleButton.querySelector('.toggle-icon');

    toggleButton.addEventListener('click', (e) => {
      e.stopPropagation(); 
      const isOpen = detailRow.style.display === 'table-row';
      detailRow.style.display = isOpen ? 'none' : 'table-row';
      toggleIcon.src = isOpen 
        ? './img/arrow-down.svg' 
        : './img/arrow-up.svg';
      toggleIcon.alt = isOpen ? 'Развернуть' : 'Свернуть';
    });
  });

  table.appendChild(tbody);
  teachersList.appendChild(table);
}
