document.querySelectorAll('select').forEach(select => {
  select.addEventListener('focus', () => {
    select.classList.add('select-open');
  });
  select.addEventListener('blur', () => {
    select.classList.remove('select-open');
  });
});

function showNotification(type, message) {
  const container = document.getElementById('notifications-container');

  const notif = document.createElement('div');
  notif.className = `notification ${type}`;

  let iconPath = '';
  switch (type) {
    case 'success':
      iconPath = './img/check-circle.svg';
      break;
    case 'error':
      iconPath = './img/error.svg';
      break;
    case 'warning':
      iconPath = './img/warning.svg';
      break;
    case 'system':
      iconPath = './img/info.svg';
      break;
    default:
      iconPath = './img/check-circle.svg'; 
  }

  notif.innerHTML = `
    <div class="notification-content">
      <img src="${iconPath}" alt="${type}" width="24" height="24" class="notification-icon" />
      <div class="message">${message}</div>
    </div>
    <svg class="close-btn" width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M4.35355 5.64645C4.15829 5.45118 3.84171 5.45118 3.64645 5.64645C3.45118 5.84171 3.45118 6.15829 3.64645 6.35355L7.29289 10L3.64645 13.6464C3.45118 13.8417 3.45118 14.1583 3.64645 14.3536C3.84171 14.5488 4.15829 14.5488 4.35355 14.3536L8 10.7071L11.6464 14.3536C11.8417 14.5488 12.1583 14.5488 12.3536 14.3536C12.5488 14.1583 12.5488 13.8417 12.3536 13.6464L8.70711 10L12.3536 6.35355C12.5488 6.15829 12.5488 5.84171 12.3536 5.64645C12.1583 5.45118 11.8417 5.45118 11.6464 5.64645L8 9.29289L4.35355 5.64645Z" fill="#5C5C5C"/>
    </svg>
  `;

  notif.querySelector('.close-btn').addEventListener('click', () => {
    notif.classList.add('fade-out');
    setTimeout(() => notif.remove(), 300);
  });
  
  container.appendChild(notif);
  
  if (type !== 'error') {
    setTimeout(() => {
      notif.classList.add('fade-out');
      setTimeout(() => notif.remove(), 300);
    }, 10000);
  }
}


window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const currentTab = urlParams.get('page');

    if (currentTab === 'dashboard') {
      showNotification('success', 'Сообщение об успешной работе чего-либо. Тут может быть много текста, который не умещается в одну или две строчки.');
  }

    if (currentTab === 'import') {
        const script = document.createElement('script');
        script.src = './js/import.js';
        document.body.appendChild(script);
    }

    if (currentTab === 'FlowsList') {
        const script = document.createElement('script');
        script.src = './js/FlowsList.js';
        script.onload = () => {
            if (typeof fetchFlows === 'function') {
              fetchFlows();
            }
          };
        document.body.appendChild(script);
    }

    if (currentTab === 'FlowStudents') {
      const script = document.createElement('script');
      script.src = './js/FlowStudents.js';
      script.onload = () => {
          const urlParams = new URLSearchParams(window.location.search);
          const flowId = urlParams.get('id_flow');
          const flowName = urlParams.get('name');
          
          if (typeof showFlowStudents === 'function' && flowId) {
              showFlowStudents(flowId, flowName);
          }
      };
      document.body.appendChild(script);
    }

    if (currentTab === 'StudentsList') {
      const script = document.createElement('script');
      script.src = './js/StudentsList.js';
      script.onload = () => {
          if (typeof fetchStudents === 'function') {
            fetchStudents();
            loadGroups(); 
          }
        };
      document.body.appendChild(script);
    }

    if (currentTab === 'Student') {
      const script = document.createElement('script');
      script.src = './js/Student.js';
      script.onload = () => {
        const urlParams = new URLSearchParams(window.location.search);
          const studentId = urlParams.get('id_isu');
          const studentName = urlParams.get('fio');

          if (typeof showStudent === 'function') {
            showStudent(studentId,studentName);
          }
        };
      document.body.appendChild(script);
    }

    if (currentTab === 'TeachersList') {
      const script = document.createElement('script');
      script.src = './js/TeachersList.js';
      script.onload = () => {
          if (typeof fetchTeachers === 'function') {
            fetchTeachers();
          }
        };
      document.body.appendChild(script);
    }

    if (currentTab === 'Teacher') {
      const script = document.createElement('script');
      script.src = './js/Teacher.js';
      script.onload = () => {
        const urlParams = new URLSearchParams(window.location.search);
          const teacherId = urlParams.get('id_isu');
          const teacherName = urlParams.get('fio');

          if (typeof showTeacher === 'function') {
            showTeacher(teacherId,teacherName);
          }
          loadStudyPlans();
          togglePlanVisibility();
        };
      document.body.appendChild(script);
    }
});
