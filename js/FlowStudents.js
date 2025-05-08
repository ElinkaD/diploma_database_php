const flowStudentsComponent = document.getElementById('flow-students');
const flowStudentsTitle = document.getElementById('flow-students-title');
const flowStudentsContainer = document.getElementById('flow-students-container');
const educationFormFilter = document.getElementById('education-form-filter');
const filterButton = document.getElementById('filter-button');
const noResultsMessage = document.getElementById('no-results-message'); 

let allFlowStudents = [];

async function showFlowStudents(flowId, flowName) {
  flowStudentsTitle.textContent = `Студенты потока: ${flowName}`;

  try {
    const res = await fetch(`./server/api/FlowStudents.php?flow_id=${encodeURIComponent(flowId)}`);
    allFlowStudents = await res.json();
    renderFlowStudents(allFlowStudents); 
  } catch (err) {
    flowStudentsContainer.textContent = 'Ошибка загрузки: ' + err.message;
  }
}

function renderFlowStudents(students) {
  flowStudentsContainer.innerHTML = '';
  if (students.length === 0) {
    noResultsMessage.classList.remove('hidden');
    return;
  }

  let i = 0;

  noResultsMessage.classList.add('hidden');

  const table = document.createElement('table');
  table.classList.add('data-table');

  table.innerHTML = `
    <thead>
      <tr>
        <th></th>
        <th>ID ИСУ</th>
        <th>ФИО</th>
        <th>Форма обучения</th>
        <th>Группа</th>
        <th>Трек</th>
        <th>Гражданство</th>
        <th>Комментарий к студенту</th>
        <th>Комментарий к статусу</th>
      </tr>
    </thead>
    <tbody>
      ${students.map(s => `
        <tr>
          <td>${i = i + 1}</td>
          <td>${s.id_isu}</td>
          <td>${s.fio}</td>
          <td>${s.education_form ?? ''}</td>
          <td>${s.group_number ?? ''}</td>
          <td>${s.track_name ?? ''}</td>
          <td>${s.citizenship ?? ''}</td>
          <td>${s.comment ?? ''}</td>
          <td>${s.status_comment ?? ''}</td>
        </tr>`).join('')}
    </tbody>
  `;

  flowStudentsContainer.appendChild(table);
}

filterButton.addEventListener('click', async () => {
  const flowId = new URLSearchParams(window.location.search).get('id_flow');
  const educationForm = educationFormFilter.value;
  
  try {
      const res = await fetch(`./server/api/FlowStudents.php?flow_id=${encodeURIComponent(flowId)}&education_form=${encodeURIComponent(educationForm)}`);
      allFlowStudents = await res.json();
      renderFlowStudents(allFlowStudents);
  } catch (err) {
      flowStudentsContainer.textContent = 'Ошибка фильтрации: ' + err.message;
  }
});
