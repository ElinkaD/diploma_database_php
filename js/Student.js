const StudentsTitle = document.getElementById('student-title');
const StudentsContainer = document.getElementById('student-container');
const Plan = document.getElementById('individual-plan');
const noResultsMessage = document.getElementById('no-results-message'); 


async function showStudent(studentId, studentName) {
  StudentsTitle.textContent = `Студент: ${studentName} ИСУ: ${studentId}`;

  try {
    const res = await fetch(`./server/api/Student.php?id_isu=${encodeURIComponent(studentId)}`);
    const studentData = await res.json();
    renderStudent(studentData);
  } catch (err) {
    showNotification('error', 'Выберите тип и файл для импорта');
  }
}

function renderStudent(data) {
  StudentsContainer.innerHTML = '';
  noResultsMessage.classList.add('hidden');

  const createTable = (title, headers, rows) => {
    const section = document.createElement('section');
    let heading;
    if(title.startsWith('Семестр')){
      heading = document.createElement('h4');
    }
    else{
     heading = document.createElement('h3');
    }
    heading.textContent = title;
    section.appendChild(heading);

    const table = document.createElement('table');
    table.classList.add('data-table');

    table.innerHTML = `
      <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
      <tbody>
      ${rows.map(row => {
        const isChangable = row.changable === true || row.changable === "true"; 
        return `
          <tr${isChangable ? ' class="changable-highlight"' : ''}>
            ${headers.map(key => {
              const value = row[key] ?? '';
              if (key === 'teacher_name' && row.teacher_id && row.teacher_name) {
                return `<td><a href="?page=Teacher&id_isu=${encodeURIComponent(row.teacher_id)}&fio=${encodeURIComponent(row.teacher_name)}" class="teacher-link clickable" data-fio="${row.teacher_name}">${row.teacher_name}</a></td>`;
              }
              if (key === 'supervisor_name' && row.supervisor_id && row.supervisor_name) {
                return `<td><a href="?page=Teacher&id_isu=${encodeURIComponent(row.supervisor_id)}&fio=${encodeURIComponent(row.supervisor_name)}" class="teacher-link clickable" data-fio="${row.supervisor_name}">${row.supervisor_name}</a></td>`;
              }
              if (key === 'consultant_name' && row.consultant_id && row.consultant_name) {
                return `<td><a href="?page=Teacher&id_isu=${encodeURIComponent(row.consultant_id)}&fio=${encodeURIComponent(row.consultant_name)}" class="teacher-link clickable" data-fio="${row.consultant_name}">${row.consultant_name}</a></td>`;
              }
              return `<td>${value}</td>`;
            }).join('')}
          </tr>`;
      }).join('')}
    </tbody>
    `;

    section.appendChild(table);
    if(title.startsWith('Семестр')){
      Plan.appendChild(section);
    }
    else{
      StudentsContainer.appendChild(section);
    }
  };


  // Основная информация
  if (data.student_info) {
    createTable('Основная информация', ['id_isu', 'fio', 'citizenship', 'comment'], [data.student_info]);
  }
  

  // Статусы
  if (data.statuses?.length) {
    createTable('Статусы', ['education_form', 'group_number', 'plan_name_year', 'track_name', 'status', 'year', 'semester', 'comment'], data.statuses);
  }

  // Индивидуальный план
  if (data.plan?.length) {
    data.plan.forEach(semester_group => {
      if(semester_group.disciplines != null && semester_group.disciplines.length > 0){
        createTable( `Семестр ${semester_group.Семестр}`, ['id_rpd', 'name', 'implementer', 'credits', 'assessment_type'], semester_group.disciplines || []
      );
      }
    });
  }


  console.log('data:', data);  
  // ВКР
  if (data.vkr) {
    createTable('ВКР', ['student_name', 'theme', 'supervisor_name', 'consultant_name'], [data.vkr]);
  }

  // Долги
  if (data.debts?.length) {
    createTable('Долги', ['rpd_id', 'discipline_name', 'year', 'semester', 'debt_type', 'comment', 'assessment_type'], data.debts);
  }
  
  // Менторы
  if (data.mentor?.length) {
    createTable('Ментор', ['discipline_name', 'year', 'semester', 'teacher_name', 'comment'], data.mentor);
  }
}