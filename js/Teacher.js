const TeacherTitle = document.getElementById('teacher-title');
const TeacherContainer = document.getElementById('teacher-container');
const noResultsMessage = document.getElementById('no-results-message'); 


async function showTeacher(teacherId, teacherName) {
  TeacherTitle.textContent = `Преподаватель: ${teacherName} ИСУ: ${teacherId}`;

  try {
    const res = await fetch(`./server/api/Teacher.php?id_isu=${encodeURIComponent(teacherId)}`);
    const teacherData = await res.json();
    renderTeacher(teacherData); 
  } catch (err) {
    TeacherContainer.textContent = 'Ошибка загрузки: ' + err.message;
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