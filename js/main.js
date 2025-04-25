document.querySelectorAll('select').forEach(select => {
  select.addEventListener('focus', () => {
    select.classList.add('select-open');
  });
  select.addEventListener('blur', () => {
    select.classList.remove('select-open');
  });
});

window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const currentTab = urlParams.get('page');

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
          if (typeof fetchStudents === 'function') {
            fetchStudents();
          }
        };
      document.body.appendChild(script);
    }
});
