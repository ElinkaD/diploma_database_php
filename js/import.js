const form = document.getElementById('import-form');
const yearSelect = document.getElementById('year');
const semesterSelect = document.getElementById('semester');
const planInput = document.getElementById('plan');
// const responseDiv = document.getElementById('import-response');
const typeSelect = document.getElementById('type');

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file');
const fileInfoBefore = document.getElementById('upload-hint');
const fileInfo = document.getElementById('uploaded');
const fileNameSpan = document.getElementById('uploaded-file');
const removeFileBtn = document.getElementById('remove-file');

const typesRequiringSemester = [
  'debts',
  'flows',
  'mentors',
  'nagruzka',
  'portfolio',
  'salary'
];

const typesRequiringPlan = ['portfolio'];
let studyPlans = [];

async function loadStudyPlans() {
  try {
      const response = await fetch('./server/api/get_study_plans.php');
      if (!response.ok) throw new Error('Ошибка загрузки учебных планов');
      studyPlans = await response.json();
      updatePlanSelect();
  } catch (error) {
     console.error('Ошибка:', error);
     showNotification('error', 'Не удалось загрузить учебные планы: ' + error.message);
  }
}

function updatePlanSelect() {
  const planSelect = document.getElementById('plan');
  planSelect.innerHTML = '<option value="">Выберите учебный план</option>';
  
  studyPlans.forEach(plan => {
      const option = document.createElement('option');
      option.value = plan.id_isu;
      option.textContent = plan.name;
      planSelect.appendChild(option);
  });
}

function toggleSemesterFields() {
  const selectedType = typeSelect.value;
  const needsSemester = typesRequiringSemester.includes(selectedType);
  const needsPlan = typesRequiringPlan.includes(selectedType);

  const semesterGroup = document.getElementById('semester-group'); 
  const yearGroup = document.getElementById('year-group'); 
  const planGroup = document.getElementById('plan-group'); 

  if (semesterGroup && yearGroup && planGroup) {
    semesterGroup.classList.toggle('hidden', !needsSemester);
    yearGroup.classList.toggle('hidden', !needsSemester);
    planGroup.classList.toggle('hidden', !needsPlan);
  } else {
    console.error('Не удалось найти элементы формы');
    showNotification('error', 'Ошибка при обновлении формы');
  }
}

window.onload = async () => {
  toggleSemesterFields();
  await loadStudyPlans();
};

typeSelect.addEventListener('change', toggleSemesterFields); 

// === Drag & Drop ===
dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', e => {
  e.preventDefault();
  dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
  dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', e => {
  e.preventDefault();
  dropZone.classList.remove('dragover');
  if (e.dataTransfer.files.length > 0) {
    fileInput.files = e.dataTransfer.files;
    showFileInfo();
  }
});

fileInput.addEventListener('change', showFileInfo);

function showFileInfo() {
  if (fileInput.files.length > 0) {
    fileNameSpan.textContent = fileInput.files[0].name;
    fileInfoBefore.classList.add('hidden');
    fileInfo.classList.remove('hidden');
    dropZone.style.border = 'none';
  } else {
    clearFile();
  }
}

removeFileBtn.addEventListener('click', clearFile);

function clearFile() {
  fileInput.value = '';
  fileInfo.classList.add('hidden');
  fileInfoBefore.classList.remove('hidden');
  fileNameSpan.textContent = '';
  dropZone.style.border = '';
}

function resetForm() {
  fileInput.value = '';  
  yearSelect.value = '';  
  semesterSelect.value = '';  
  planInput.value = ''; 
  typeSelect.value = ''; 

  document.getElementById('plan-group').classList.add('hidden');
  document.getElementById('semester-group').classList.add('hidden');
  document.getElementById('year-group').classList.add('hidden');
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  // responseDiv.textContent = ''; 

  const file = fileInput.files[0];
  const type = typeSelect.value;

  if (!file || !type) {
    showNotification('warning', 'Выберите тип и файл для импорта');
    return;
  }

  const formData = new FormData();
  formData.append('file', file);

  let url = `./server/api/import.php?type=${encodeURIComponent(type)}`;

  if (typesRequiringSemester.includes(type)) {
    let semester = semesterSelect.value;
    let year = yearSelect.value;

    if (!semester || !year) {
      const now = new Date();
      semester = now.getMonth() < 6 ? 'весна' : 'осень'; 
      year = now.getFullYear(); 
    }

    url += `&semester=${encodeURIComponent(semester)}&year=${encodeURIComponent(year)}`;

    if (type==='portfolio') {
      const planId = document.getElementById('plan').value;
      if (planId) {
        url += `&plan_id=${encodeURIComponent(planId)}`;
      } else {
        showNotification('warning', 'Для импорта портфолио выберите учебный план');
        return;
      }
    }
  }

  try {
    const res = await fetch(url, {
      method: 'POST',
      body: formData
    });

    const result = await res.json();
      if (Array.isArray(result)) {
        result.forEach(item => {
          showNotification(item.status || 'system', item.message);
        });
      } else {
        showNotification(result.status ?? (res.ok ? 'success' : 'error'), 
                        result.message || result.error || 'Операция выполнена');
      }

    clearFile();

    if (res.ok && (!Array.isArray(result) || result.some(r => r.status === 'success'))) {
      resetForm();
    }
  } catch (err) {
     console.error('Ошибка при импорте:', err);
     showNotification('error', 'Ошибка при импорте: ' + err.message);
  }
});


