const TeacherTitle = document.getElementById('teacher-title');
const TeacherContainer = document.getElementById('teacher-container');
const WorkloadContainer = document.getElementById('workload-container');

const noResultsMessage = document.getElementById('no-results-message'); 

const typeSelect = document.getElementById('type');
const planSelect = document.getElementById('plan');
const planGroup = document.getElementById('plan-group');
const filterButton = document.getElementById('filter-button');

const typesRequiringPlan = ['4'];
let studyPlans = [];
let currentTeacherId = null;
let  all_hours_for_header = null;
let  scenario_bool = false;


function getPeriodTitle(scenario) {
  switch (scenario) {
    case "1":
      return "следующий семестр";
    case "2":
      return "текущий учебный год";
    case "3":
      return "следующий учебный год";
    case "4":
      return "весь период нового учебного плана с действующими планами";
    default:
      return "текущий семестр";
  }
}

async function loadStudyPlans() {
  try {
    const res = await fetch('./server/api/get_study_plans.php');
    if (!res.ok) throw new Error('Не удалось загрузить учебные планы');
    studyPlans = await res.json();
    console.log(studyPlans);
    updatePlanSelect();
  } catch (err) {
    console.error(err);
    showNotification('error', 'Ошибка загрузки планов: ' + err.message);
  }
}

function updatePlanSelect() {
  planSelect.innerHTML = '<option value="">Выберите учебный план</option>';
  studyPlans.forEach(plan => {
    const option = document.createElement('option');
    option.value = plan.id_isu;
    option.textContent = `${plan.name} (${plan.year}г)`; 
    planSelect.appendChild(option);
  });
}

function togglePlanVisibility() {
  const selected = typeSelect.value;
  const needsPlan = typesRequiringPlan.includes(selected);
  planGroup.classList.toggle('hidden', !needsPlan);
}

typeSelect.addEventListener('change', togglePlanVisibility);

filterButton.addEventListener('click', async () => {
  const selectedType = typeSelect.value;
  const selectedPlan = planSelect.value;

  let url = `./server/api/Teacher.php?id_isu=${encodeURIComponent(currentTeacherId)}&scenario=${encodeURIComponent(selectedType)}`;

  if (typesRequiringPlan.includes(selectedType)) {
    if (!selectedPlan) {
      showNotification('warning', 'Выберите учебный план');
      return;
    }
    url += `&id_plan=${encodeURIComponent(selectedPlan)}`;
  }

  try {
    const res = await fetch(url);
    const teacherData = await res.json();
    all_hours_for_header = teacherData.total_hours_by_scenario;
    scenario_bool = true;
    renderWorkload(teacherData.workload.map(w => w.json_build_object));
  } catch (err) {
    console.error('Ошибка при получении данных:', err);
    showNotification('error', 'Ошибка при получении данных: ' + err.message);
  }
});

async function showTeacher(teacherId, teacherName) {
  currentTeacherId = teacherId;
  TeacherTitle.textContent = `Преподаватель: ${teacherName} ИСУ: ${teacherId}`;

  try {
    const res = await fetch(`./server/api/Teacher.php?id_isu=${encodeURIComponent(teacherId)}`);
    const teacherData = await res.json();
    renderTeacher(teacherData); 
    all_hours_for_header = teacherData.total_hours_by_scenario;
    renderWorkload(teacherData.workload.map(w => w.json_build_object));
  } catch (err) {
    showNotification('error', 'Ошибка загрузки: ' + err.message);
  }
}

function renderTeacher(data) {
  TeacherContainer.innerHTML = '';
  noResultsMessage.classList.add('hidden');

  const createTable = (title, headers, rows) => {
    const section = document.createElement('section');
    section.setAttribute('data-section', title);
    const heading = document.createElement('h3');
    heading.textContent = title;
    section.appendChild(heading);

    const table = document.createElement('table');
    table.classList.add('data-table');

    table.innerHTML = `
      <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
      <tbody>
        ${rows.map(row => `
          <tr>${headers.map(key => {
              const className =
                key === 'fio' && (title === 'Менторы' || title === 'ВКР') ? 'student-name clickable' : '';
              return `<td class="${className}">${row[key] ?? ''}</td>`;
            }).join('')}
          </tr>
        `).join('')}
      </tbody>
    `;

    section.appendChild(table);
    TeacherContainer.appendChild(section);
  };

  console.log(data);
  

  // Основная информация
  if (data.teacher_info) {
    if (Array.isArray(data.teacher_info.disciplines)) {
      data.teacher_info.disciplines = data.teacher_info.disciplines.join('<br>');
    }
    createTable('Основная информация', ['id_isu', 'fio', 're_election_year', 'term', 'comment', 'disciplines'], [data.teacher_info]);
  }
  

  // KPI
  if (data.kpi?.length) {
    createTable('KPI', ['kpi_type', 'value'], data.kpi);
  }

  // Salary
  if (data.salary?.length) {
    createTable('Ставка', ['year', 'semester', 'amount', 'position', 'comment'], data.salary);
  }

  // РПД Преподавателей
  if (data.rpds?.length) {
    const rpdsWithIndex = data.rpds.map((rpd, index) => {
      if (Array.isArray(rpd.co_authors)) {
        rpd.co_authors = rpd.co_authors.map(author => 
          `<span class='teacher-link clickable' data-id="${author.id_isu}" data-fio="${author.fio}">${author.fio}</span>`
        ).join('<br>');
      }
      return {
        ...rpd,
        '№': index + 1
      };
    });
    createTable('РПД Преподавателя', ['№', 'id_rpd', 'name', 'status', 'co_authors', 'comment'], rpdsWithIndex);

    const rpdSection = TeacherContainer.querySelector('[data-section="РПД Преподавателя"]');
    rpdSection.querySelectorAll('.teacher-link').forEach(el => {
      el.addEventListener('click', () => {
        const id = el.dataset.id;
        const fio = el.dataset.fio;
        window.location.href = `?page=Teacher&id_isu=${encodeURIComponent(id)}&fio=${encodeURIComponent(fio)}`;
      });
    });
  }

  // Менторы
  if (data.mentors?.length) {
    createTable('Менторы', ['id_isu',
      'discipline', 'fio', 'year', 'semester', 'student_status','comment_mentor', 'comment_discipline'], data.mentors);

    const mentorSection = TeacherContainer.querySelector('[data-section="Менторы"]');
    mentorSection.querySelectorAll('.student-name').forEach((el, idx) => {
      el.addEventListener('click', () => {
        const student = data.mentors[idx];
        window.location.href = `?page=Student&id_isu=${encodeURIComponent(student.id_isu)}&fio=${encodeURIComponent(student.fio)}`;
      });
    });
  }

  // ВКР
  if (data.vkr?.length) {
    createTable('ВКР', ['id_isu', 'fio', 'theme', 'type', 'comment_vkr', 'comment_student'], data.vkr);

    const vkrSection = TeacherContainer.querySelector('[data-section="ВКР"]');
    vkrSection.querySelectorAll('.student-name').forEach((el, idx) => {
      el.addEventListener('click', () => {
        const student = data.vkr[idx];
        window.location.href = `?page=Student&id_isu=${encodeURIComponent(student.id_isu)}&fio=${encodeURIComponent(student.fio)}`;
      });
    });
  }
}

function renderWorkload(workloadList) {
  console.log(workloadList);
  WorkloadContainer.innerHTML = '';

  if (!Array.isArray(workloadList) || workloadList.length === 0)  {
    noResultsMessage.classList.remove('hidden');
    return;
  }

  noResultsMessage.classList.add('hidden');
  const selectedScenario = typeSelect.value;
  const periodTitle = getPeriodTitle(selectedScenario);

  const table = document.createElement('table');
  table.classList.add('data-table');
  const thead = document.createElement('thead');

  thead.innerHTML = `
    <tr class="text-table-header">
      <th>ID RPD</th>
      <th>Название РПД</th>
      <th>Статус РПД</th>
      <th>Общая нагрузка за ${periodTitle}</th>
      <th><span class='text-bold'>${all_hours_for_header} ч</span></th>
      <th></th>
    </tr>
  `;
  table.appendChild(thead);

  const tbody = document.createElement('tbody');

  workloadList.forEach((r, index) => {
    const row = document.createElement('tr');
    row.classList.add('workload-row');

    row.innerHTML = `
      <td>${r.id_rpd ?? ''}</td>
      <td>${r.name ?? ''}</td>
      <td>${r.status ?? ''}</td>
      <td>${r.total_hours ?? 0}</td>
      <td>
        <button class="toggle-details" data-index="${index}" style="background:none; border:none; cursor:pointer;">
          <img src="./img/arrow-down.svg" alt="Развернуть" class="toggle-icon">
        </button>
      </td>    `;

    const detailRow = document.createElement('tr');
    detailRow.style.display = 'none';

    const rpdList = (r.workloads ?? []).map(w => {
      const flowsHtml = Array.isArray(w.flows)
      ? w.flows.map(f =>
          `<span class="clickable flow-span" data-id="${f.id}" data-name="${f.name}" style="font-weight:600; margin-right: 8px; cursor:pointer;">${f.name}</span>`
        ).join('')
      : (w.flows && typeof w.flows === 'object')
        ? `<span class="clickable flow-span" data-id="${w.flows.id}" data-name="${w.flows.name}" style="font-weight:600; cursor:pointer;">${w.flows.name}</span>`
        : '';
      return `
        <tr>
          <td>${w.discipline ?? ''}</td>
          <td>${w.year ?? ''}</td>
          <td>${w.semester ?? ''}</td>
          <td>${w.count_hours ?? ''}</td>
          <td>${w.workload_type ?? ''}</td>
          <td>${w.assessment_types ?? ''}</td>
          <td>${w.comment ?? ''}</td>
          <td>${flowsHtml}</td>
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
                  <th>Год</th>
                  <th>Семестр</th>
                  <th>Часы</th>
                  <th>Тип нагрузки</th>
                  <th>Тип контроля</th>
                  <th>Комментарий</th>
                  <th>Потоки</th>
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

    detailRow.querySelectorAll('.flow-span').forEach(span => {
      const id = span.dataset.id;
      const name = span.dataset.name;
    
      span.addEventListener('click', () => {
        window.location.href = `?page=FlowStudents&id_flow=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}`;
      });
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
  WorkloadContainer.appendChild(table);
}