const statusList = document.getElementById('status-list');
const noResultsMessage = document.getElementById('no-results-message'); 

const idInput = document.getElementById('id_isu');
const searchButton = document.getElementById('filter-button');

let selectedStatusId = null;

searchButton.addEventListener('click', async () => {
  const id_isu = idInput.value.trim();
  if (!id_isu) return alert('Введите табельный номер');
  try {
    const res = await fetch(`./server/api/GetStudentStatuses.php?id_isu=${encodeURIComponent(id_isu)}`);
    const statuses = await res.json();
    if (!Array.isArray(statuses)) {
      console.error('Ожидался массив, получено:', statuses);
      showNotification('warning', 'Ошибка: неверный формат данных');
      return;
    }
    renderStatuses(statuses);
  } catch (err) {
    showNotification('error', 'Ошибка при получении статусов: ' + err.message);
  }
});


function renderStatuses(statuses) {
  statusList.innerHTML = '';
  if (!Array.isArray(statuses) || statuses.length === 0) {
    statusList.innerHTML = '<div>Статусы не найдены</div>';
    return;
  }

  const table = document.createElement('table');
  table.classList.add('data-table');

  const thead = document.createElement('thead');
  thead.innerHTML = `
    <tr class="text-table-header">
      <th>Форма обучения</th>
      <th>Статус</th>
      <th>Семестр</th>
      <th>Год</th>
      <th>Группа</th>
      <th>Год поступления</th>
      <th>Трек</th>
      <th>УП</th>
      <th>Год УП</th>
      <th>Комментарий</th>
      <th>Выбрать</th>
    </tr>
  `;
  table.appendChild(thead);

  const tbody = document.createElement('tbody');
  statuses.forEach(status => {
    const row = document.createElement('tr');

    row.innerHTML = `
      <td>${status.education_form ?? ''}</td>
      <td>${status.status ?? ''}</td>
      <td>${status.semester ?? ''}</td>
      <td>${status.year ?? ''}</td>
      <td>${status.group_number ?? ''}</td>
      <td>${status.year_enter ?? ''}</td>
      <td>${status.track_name ?? ''}</td>
      <td>${status.plan_name ?? ''}</td>
      <td>${status.plan_year ?? ''}</td>
      <td>${status.comment ?? ''}</td>
      <td>
        <button class="select-status-btn" data-status-id="${status.id_status}">Выбрать</button>
      </td>
    `;
    tbody.appendChild(row);
  });
  table.appendChild(tbody);
  statusList.appendChild(table);

  document.querySelectorAll('.select-status-btn').forEach(button => {
    button.addEventListener('click', async (e) => {
      const id_status = e.target.dataset.statusId;
      selectedStatusId = id_status;
      await loadPlansAndTracks(selectedStatusId);
    });
  });
}


async function loadPlansAndTracks(id_status) {
  try {
    const res = await fetch(`./server/api/GetCurriculaTracks.php?id_status=${encodeURIComponent(id_status)}`);
    const tracks = await res.json();

    if (!Array.isArray(tracks)) {
      showNotification('warning', 'Неверный формат данных учебных планов');
      return;
    }

    renderPlanSelect(tracks);
  } catch (err) {
    showNotification('error', 'Ошибка загрузки планов: ' + err.message);
  }
}

function renderPlanSelect(tracks) {
  const statusesContainer = document.getElementById('statuses-container');
  const select = document.getElementById('status-select');
  select.innerHTML = '';

  const placeholder = document.createElement('option');
  placeholder.disabled = true;
  placeholder.selected = true;
  placeholder.textContent = 'Выберите необходимый трек';
  select.appendChild(placeholder);

  tracks.forEach(track => {
    const option = document.createElement('option');
    option.value = track.id_track; 
    option.textContent = `${track.curricula_name} (${track.curricula_year}) – ${track.track_name} (${track.track_number})`;
    select.appendChild(option);
  });

  statusesContainer.classList.remove('hidden');

  select.addEventListener('change', async (e) => {
    const id_track = e.target.value;
    try {
      const res = await fetch(`./server/api/Akadem.php?id_status=${encodeURIComponent(selectedStatusId)}&id_track=${encodeURIComponent(id_track)}`);
      const result = await res.json();
      renderAcademicDifference(result);
      showNotification('success', 'Расчет академ. разницы завершён');
      console.log(result); 
    } catch (err) {
      showNotification('error', 'Ошибка расчета: ' + err.message);
    }
  });
}

function renderAcademicDifference(data) {
  const akademList = document.getElementById('akadem-list');
  akademList.innerHTML = '';

  const createTable = (title, headers, rows, fields) => {
    const section = document.createElement('section');
    const heading = document.createElement('h3');
    heading.textContent = title;
    section.appendChild(heading);

    const table = document.createElement('table');
    table.classList.add('data-table');

    table.innerHTML = `
      <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
      <tbody>
        ${rows.map(row => {
          const trClass = row.has_debt ? ' class="has-debt"' : '';
          return `
            <tr${trClass}>
              ${fields.map(field => {
                let value = row[field];

                if (field.includes('teachers')) {
                  if (Array.isArray(value) && value.length > 0) {
                    const validTeachers = value.filter(t => t?.id_isu && t?.fio);
                    console.log(field);
                    if (validTeachers.length > 0) {
                      return `<td>
                        <ul class="teachers-list">
                          ${validTeachers.map(t => `
                            <span class='teacher-link clickable' data-id="${t.id_isu}" data-fio="${t.fio}">${t.fio}</span>
                          `).join('')}
                        </ul>
                      </td>`;
                    }
                  }
                  return '<td>—</td>';
                }
                if (field === 'has_debt') {
                  return `<td>${value ? 'Да' : 'Нет'}</td>`;
                }
                return `<td>${value ?? '—'}</td>`;
              }).join('')}
            </tr>`;
        }).join('')}
      </tbody>
    `;

    section.appendChild(table);
    akademList.appendChild(section);

    akademList.querySelectorAll('.teacher-link').forEach(el => {
      el.addEventListener('click', () => {
        const id = el.dataset.id;
        const fio = el.dataset.fio;
        window.location.href = `?page=Teacher&id_isu=${encodeURIComponent(id)}&fio=${encodeURIComponent(fio)}`;
      });
    });
  };

  // 1. Перезачет
  if (data.transferable?.length) {
    createTable(
      'Дисциплины для перезачета',
      ['id rpd', 'Название', 'Реализатор', 'З.Е.', 'Форма контроля', 'Семестр'],
      data.transferable,
      ['id_rpd', 'discipline_name', 'implementer', 'credits', 'assessment_type', 'semester_number']
    );
  }

  // 2. Согласование
  if (data.negotiable?.length) {
    createTable(
      'Дисциплины для согласования',
      ['id rpd', 'Название', 'Текущий реализатор', 'Новый реализатор', 'Старые З.Е.', 'Новые З.Е.', 'Старая форма', 'Новая форма', 'Преподаватели', 'Семестр'],
      data.negotiable,
      ['id_rpd', 'discipline_name', 'current_implementer', 'new_implementer', 'current_credits', 'new_credits', 'current_assessment_type', 'new_assessment_type', 'teachers', 'semester_number']
    );
  }

  // 3. Для сдачи
  if (data.new_disciplines?.length) {
    createTable(
      'Дисциплины для сдачи',
      ['id rpd', 'Название', 'Реализатор', 'З.Е.', 'Форма контроля', 'Преподаватели', 'Есть долг'],
      data.new_disciplines,
      ['id_rpd', 'discipline_name', 'implementer', 'credits', 'assessment_type', 'teachers', 'has_debt']
    );
  }
}
