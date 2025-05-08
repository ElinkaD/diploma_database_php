const studentsList = document.getElementById('students-list');
const noResultsMessage = document.getElementById('no-results-message'); 
const yearSelect = document.getElementById('year');
const semesterSelect = document.getElementById('semester');
const statusSelect = document.getElementById('status');
const educationFormSelect = document.getElementById('education-form');
const groupSelect = document.getElementById('group');
const trackInput = document.getElementById('track');
const citizenshipInput = document.getElementById('citizenship');
const idInput = document.getElementById('id_isu');
const fioInput = document.getElementById('fio');

const searchButton = document.getElementById('filter-button');

let groupsList = [];

async function loadGroups() {
  try {
      const response = await fetch('./server/api/get_groups.php');
      if (!response.ok) throw new Error('Ошибка загрузки списка групп');
      groupsList = await response.json();
      updateGroupSelect();
  } catch (error) {
      console.error('Ошибка:', error);
      noResultsMessage.textContent = 'Ошибка загрузки списка групп';
      noResultsMessage.classList.remove('hidden');
  }
}

function updateGroupSelect() {
  groupSelect.innerHTML = '<option value="">Все группы</option>';
  
  groupsList.forEach(group => {
      const option = document.createElement('option');
      option.value = group.id; 
      option.textContent = `${group.group_number} (${group.year_enter}г)`;  
      groupSelect.appendChild(option);
  });
}

searchButton.addEventListener('click', async () => {
  let params = new URLSearchParams();

  if (idInput.value.trim()) params.append('id_isu', trackInput.value.trim());
  if (fioInput.value.trim()) params.append('fio', citizenshipInput.value.trim());
  if (yearSelect.value) params.append('year', yearSelect.value);
  if (semesterSelect.value) params.append('semester', semesterSelect.value);
  if (statusSelect.value) params.append('status', statusSelect.value);
  if (educationFormSelect.value) params.append('education_form', educationFormSelect.value);
  if (groupSelect.value) params.append('group_id', groupSelect.value);
  if (trackInput.value.trim()) params.append('track', trackInput.value.trim());
  if (citizenshipInput.value.trim()) params.append('citizenship', citizenshipInput.value.trim());

  try {
    const res = await fetch(`./server/api/StudentsList.php?${params.toString()}`);
    const students = await res.json();
    renderStudents(students);
  } catch (err) {
    studentsList.innerHTML = 'Ошибка при фильтрации: ' + err.message;
  }
});


async function fetchStudents() {
  try {
    const res = await fetch('./server/api/StudentsList.php');
    const students = await res.json();
    console.log('📥 Ответ:', students);
    renderStudents(students);
  } catch (err) {
    studentsList.innerHTML = 'Ошибка загрузки студентов: ' + err.message;
  }
}


function renderStudents(students) {
  studentsList.innerHTML = '';

  if (students.length === 0) {
    noResultsMessage.classList.remove('hidden');
    return;
  }

  noResultsMessage.classList.add('hidden');

  const table = document.createElement('table');
  table.classList.add('data-table');

  table.innerHTML = `
    <thead>
      <tr>
        <th>ID ИСУ</th>
        <th>ФИО</th>
        <th>ID Индивидуального плана</th>
        <th>Форма обучения</th>
        <th>Группа</th>
        <th>Год поступления</th>
        <th>Трек</th>
        <th>Статус</th>
        <th>Семестр</th>
        <th>Год</th>
        <th>Гражданство</th>
        <th>Комментарий к студенту</th>
        <th>Комментарий к статусу</th>
      </tr>
    </thead>
    <tbody>
      ${students.map(s => `
        <tr>
          <td>${s.id_isu}</td>
          <td class="student-name clickable">${s.fio}</td>
          <td>${s.id_individual_plan_isu ?? ''}</td>
          <td>${s.education_form ?? ''}</td>
          <td>${s.group_number ?? ''}</td>
          <td>${s.year_enter ?? ''}</td>
          <td>${s.track_name ?? ''}</td>
          <td>${s.status}</td>
          <td>${s.semester}</td>
          <td>${s.year}</td>
          <td>${s.citizenship ?? ''}</td>
          <td>${s.comment ?? ''}</td>
          <td>${s.comment_status ?? ''}</td>
        </tr>
      `).join('')}
    </tbody>
  `;

  studentsList.appendChild(table);

  table.querySelectorAll('.student-name').forEach((el, idx) => {
    el.addEventListener('click', () => {
      const student = students[idx];
      window.location.href = `?page=Student&id_isu=${encodeURIComponent(student.id_isu)}&fio=${encodeURIComponent(student.fio)}`;
    });
  });
}


