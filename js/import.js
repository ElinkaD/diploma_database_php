const form = document.getElementById('import-form');
const yearSelect = document.getElementById('year');
const semesterSelect = document.getElementById('semester');
const responseDiv = document.getElementById('import-response');
const typeSelect = document.getElementById('type');

const typesRequiringSemester = [
  'debts',
  'flows',
  'mentors',
  'nagruzka',
  'portfolio',
  'salary'
];

function fillYearSelect() {
  const currentYear = new Date().getFullYear();
  for (let i = currentYear - 4; i <= currentYear + 4; i++) {
    const option = document.createElement('option');
    option.value = i;
    option.textContent = i;
    yearSelect.appendChild(option);
  }
}

function toggleSemesterFields() {
  const selectedType = typeSelect.value;
  const needsSemester = typesRequiringSemester.includes(selectedType);

  const semesterGroup = semesterSelect.closest('.form-group');
  const yearGroup = yearSelect.closest('.form-group');

  if (semesterGroup && yearGroup) {
    semesterGroup.classList.toggle('hidden', !needsSemester);
    yearGroup.classList.toggle('hidden', !needsSemester);
  } else {
    console.error('Не удалось найти родительский элемент .form-group');
  }
}

window.onload = () => {
  const semesterGroup = semesterSelect.closest('.form-group');
  const yearGroup = yearSelect.closest('.form-group');
  
  if (semesterGroup && yearGroup) {
    semesterGroup.classList.add('hidden');
    yearGroup.classList.add('hidden');
  }

  fillYearSelect();  
  toggleSemesterFields();  
};

typeSelect.addEventListener('change', toggleSemesterFields); 

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const file = document.getElementById('file').files[0];
  const type = typeSelect.value;

  if (!file || !type) {
    responseDiv.textContent = 'Выберите тип и файл.';
    return;
  }

  console.log('Файл:', file);
  console.log('Тип импорта:', type);

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
  }

  try {
    const res = await fetch(url, {
      method: 'POST',
      body: formData
    });

    const result = await res.json();
    responseDiv.textContent = result.message || result.error;
  } catch (err) {
    responseDiv.textContent = 'Ошибка при импорте: ' + err.message;
  }
});
