const typeTasksMap = {
  'Лаб': 'Лабораторные занятия',
  'Пр': 'Практические занятия',
  'Лек': 'Лекционные занятия',
  'К': 'Консультации'
};

const flowsList = document.getElementById('flows-list');
// const flowSearchInput = document.getElementById('flow-search');
const noResultsMessage = document.getElementById('no-results-message'); 
const yearSelect = document.getElementById('year');
const semesterSelect = document.getElementById('semester');
const searchButton = document.getElementById('filter-button');

searchButton.addEventListener('click', async () => {
  let selectedYear = yearSelect.value;
  let selectedSemester = semesterSelect.value;

  if (!selectedYear && !selectedSemester) {
    const now = new Date();
    selectedYear = now.getFullYear();
    selectedSemester = now.getMonth() + 1 <= 6 ? 'весна' : 'осень'; 
  }

  try {
    const params = new URLSearchParams();
    if (selectedYear) params.append('year', selectedYear);
    if (selectedSemester) params.append('semester', selectedSemester);

    const res = await fetch(`./server/api/FlowsList.php?${params.toString()}`);
    const flows = await res.json();
    renderFlows(flows);
  } catch (err) {
    flowsList.innerHTML = 'Ошибка при фильтрации: ' + err.message;
  }
});


async function fetchFlows() {
  try {
    const res = await fetch('./server/api/FlowsList.php');
    const flows = await res.json();
    console.log('📥 Ответ:', flows);
    renderFlows(flows);
  } catch (err) {
    console.error('❌ Ошибка загрузки потоков:', err);
    showNotification('error', 'Ошибка загрузки потоков: ' + err.message);
  }
}


function renderFlows(flows) {
  flowsList.innerHTML = '';

  if (flows.length === 0) {
    noResultsMessage.classList.remove('hidden');
    return;
  }

  noResultsMessage.classList.add('hidden');

  const table = document.createElement('table');
  table.classList.add('data-table');

  table.innerHTML = `
    <thead>
      <tr>
        <th>ID потока</th>
        <th>Название потока</th>
        <th>Год</th>
        <th>Семестр</th>
        <th>Лимит</th>
        <th>Вид</th>
        <th>Комментарий</th>
        <th>ID РПД</th>
        <th>Название дисциплины</th>
      </tr>
    </thead>
    <tbody>
      ${flows.map(flow => `
        <tr>
          <td>${flow.id_isu}</td>
          <td class="flow-name clickable">${flow.name}</td>
          <td>${flow.year}</td>
          <td>${flow.semester}</td>
          <td>${flow.student_count} / ${flow.count_limit}</td>
          <td>${typeTasksMap[flow.type] || flow.type}</td>
          <td>${flow.comment || ''}</td>
          <td>${flow.rpd_id}</td>
          <td>${flow.discipline_name}</td>
        </tr>
      `).join('')}
    </tbody>
  `;

  flowsList.appendChild(table);

  table.querySelectorAll('.flow-name').forEach((el, idx) => {
    el.addEventListener('click', () => {
      const flow = flows[idx];
      window.location.href = `?page=FlowStudents&id_flow=${encodeURIComponent(flow.id_isu)}&name=${encodeURIComponent(flow.name)}`;
    });
  });
}


