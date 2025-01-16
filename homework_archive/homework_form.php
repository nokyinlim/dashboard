<?php
function renderCalendar() {
    ob_start(); // Start output buffering
    ?>
    <div class="homework-main">
        <form method="post" action="/homework/index.php" class="task-form">
            <h2>Create An Assignment</h2>
            <?php if (isset($_SESSION["error_message"])):
                echo '<p style="color: red;">' . htmlspecialchars($_SESSION["error_message"]) . '</p>';
                unset($_SESSION["error_message"]); // Clear the message after displaying it
            endif; ?>
            
            <input type="text" placeholder="Assignment Title" name="title" class="login-input">
            
            <input type="text" placeholder="Select a date" id="dateInput" name="due_date">
            <div class="calendar">
                
                
                <div class="calendar-container" id="calendarContainer">
                    <div class="calendar-header">
                        <button id="prevMonth">&lt;</button>
                        <h3 id="monthYear"></h3>
                        <button id="nextMonth">&gt;</button>
                    </div>
                    <div class="calendar-days">
                        <div class="day">Sun</div>
                        <div class="day">Mon</div>
                        <div class="day">Tue</div>
                        <div class="day">Wed</div>
                        <div class="day">Thu</div>
                        <div class="day">Fri</div>
                        <div class="day">Sat</div>
                    </div>
                    <div class="calendar-dates" id="calendarDates"></div>
                </div>
            </div>

            <input type="text" placeholder="Additional Notes and Details" name="notes">

            <input type="url" placeholder="Links and Resources" name="links">

            <input type="text" placeholder="Assignment Subject" name="subject">

            <input type="submit" name="submit" value="Create Item">
        </form>
    </div>

    <script>
        function formatFriendlyDate(inputDate) {
            const today = new Date(); // Current date
            const targetDate = new Date(inputDate); // Date from input
            const timeDiff = targetDate - today;
            const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

            const isTomorrow = daysDiff === 0;
            const isYesterday = daysDiff === -2;

            if (isTomorrow) {
                return "Due Tomorrow";
            } else if (isYesterday) {
                return "Due Yesterday";
            } else if (daysDiff === -1) {
                return "Due Today";
            } else if (daysDiff === 1) {
                return "Due the day after tomorrow";
            } else if (daysDiff > 1 && daysDiff < 7) {
                return "Due This " + targetDate.toLocaleString('default', { weekday: 'long' });
            } else if (daysDiff >= 7 && daysDiff < 13) {
                return "Due Next " + targetDate.toLocaleString('default', { weekday: 'long' });
            } else if (daysDiff >= 14 && daysDiff < 30) {
                const weeks = Math.floor(daysDiff / 7);
                return "Due In " + (weeks === 1 ? "one week" : `${weeks} weeks`);
            } else if (daysDiff >= 30 && daysDiff <= 364) {
                const months = Math.floor(daysDiff / 30);
                return "Due In " + (months === 1 ? "one month" : `${months} months`);
            } else if (daysDiff >= 365 && daysDiff <= 3650) {
                return `Due In ${Math.floor(daysDiff / 365)}`;
            } else if (daysDiff >= 3651) {
                return "Due in a very long time!";
            } else if (daysDiff === -3) {
                return "Due the day before Yesterday";
            } else if (daysDiff >= -7) {
                return "Due last week";
            } else if (daysDiff >= -30) {
                return "Due around last month";
            } else if (daysDiff >= -365) {
                return "Due last year";
            } else {
                return "Due a very long time ago!";
            }


            return targetDate.toISOString().split('T')[0]; // Fallback to the date string
        }
        const dateInput = document.getElementById('dateInput');
        const calendarContainer = document.getElementById('calendarContainer');
        const monthYear = document.getElementById('monthYear');
        const calendarDates = document.getElementById('calendarDates');
        const prevMonth = document.getElementById('prevMonth');
        const nextMonth = document.getElementById('nextMonth');

        let currentDate = new Date();
        let dateString = new String();

        function renderCalendar() {
            calendarDates.innerHTML = '';
            monthYear.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });

            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const today = new Date();

            for (let i = 0; i < firstDay.getDay(); i++) {
                const emptyDiv = document.createElement('div');
                calendarDates.appendChild(emptyDiv);
            }

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
                    const selectedDate = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                    // Use the newly defined JavaScript function to format the date
                    const formattedMessage = formatFriendlyDate(selectedDate);

                    // Update the date input value
                    dateInput.value = selectedDate + ` - ${formattedMessage}`; // Keep the date input value
                    // alert(formattedMessage); // debug
                    calendarContainer.style.display = 'none'; // Hide calendar
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

        renderCalendar();
    </script>
    <?php
    return ob_get_clean(); // Return the buffered content
}
?>