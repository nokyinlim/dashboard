const dateInput = document.getElementById('dateInput');
const calendarContainer = document.getElementById('calendarContainer');
const monthYear = document.getElementById('monthYear');
const calendarDates = document.getElementById('calendarDates');
const prevMonth = document.getElementById('prevMonth');
const nextMonth = document.getElementById('nextMonth');

let currentDate = new Date();

function renderCalendar() {
    // Clear previous dates
    calendarDates.innerHTML = '';

    // Set the month and year
    monthYear.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });

    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const today = new Date();

    // Fill in the empty days at the start
    for (let i = 0; i < firstDay.getDay(); i++) {
        const emptyDiv = document.createElement('div');
        calendarDates.appendChild(emptyDiv);
    }

    // Fill in the days
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const dateDiv = document.createElement('div');
        dateDiv.textContent = day;
        dateDiv.classList.add('date');

        if (day === today.getDate() && 
            currentDate.getMonth() === today.getMonth() && 
            currentDate.getFullYear() === today.getFullYear()) {
            dateDiv.classList.add('today');
        }

        dateDiv.addEventListener('click', () => {
            dateInput.value = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            calendarContainer.style.display = 'none';
        });

        calendarDates.appendChild(dateDiv);
    }
}

dateInput.addEventListener('click', () => {
    calendarContainer.style.display = calendarContainer.style.display === 'block' ? 'none' : 'block';
    renderCalendar();
});

prevMonth.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

nextMonth.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

// Initial render
renderCalendar();