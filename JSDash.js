document.addEventListener("DOMContentLoaded", () => {
    const maxBookings = 5;  // Set max bookings per date
    const bookingCounts = bookedDates || {};  // Get booked date counts from PHP

    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('closeModal');
    const modalMessage = document.getElementById('modalMessage');
    const monthYear = document.getElementById('monthYear');
    const calendar = document.getElementById('calendar');
    const sidebar = document.querySelector('.sidebar');
    const toggleSidebarButton = document.getElementById('toggleSidebar');

 // Toggle sidebar visibility
 toggleSidebarButton.addEventListener('mouseenter', () => {
    sidebar.style.display = 'block';
});

toggleSidebarButton.addEventListener('mouseleave', () => {
    sidebar.style.display = 'none';
});

    // Create calendar with current month and year
    function createCalendar(month, year) {
        calendar.innerHTML = ''; // Clear previous days

        // Add day labels
        const dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const firstDay = new Date(year, month, 1).getDay();
        const offset = firstDay === 0 ? 6 : firstDay - 1; // Adjust for Monday as the first day
        dayLabels.forEach(day => {
            const label = document.createElement('div');
            label.classList.add('day-label');
            label.innerText = day;
            calendar.appendChild(label);
        });

        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const datePrefix = `${year}-${month + 1}`; // Format as YYYY-MM

        // Get today's date for comparison
        const today = new Date();
        const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        // Add empty slots before the first day
        for (let i = 0; i < offset; i++) {
            const emptyCell = document.createElement('div');
            calendar.appendChild(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const formattedDate = `${datePrefix}-${day < 10 ? '0' + day : day}`;  // Add leading zero if day is single digit

            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');
            dayElement.innerText = day;

            // Check if the day is before today or a weekend
            const dayOfWeek = date.getDay();
            if (date <= todayDate || dayOfWeek === 0 || dayOfWeek === 6) {
                dayElement.classList.add('disabled');
                dayElement.classList.add('weekend');  // Add 'disabled' and 'weekend' classes
            } else {
                // Check if the day is fully booked
                if (bookingCounts[formattedDate] >= maxBookings) {
                    dayElement.classList.add('booked');
                }

                // Add click event for non-disabled dates
                dayElement.addEventListener('click', () => {
                    if (!dayElement.classList.contains('disabled') && !dayElement.classList.contains('booked')) {
                        openModal(formattedDate); // Call openModal with the selected date
                    } else {
                        alert('This date is not available for booking.');
                    }
                });
            }

            calendar.appendChild(dayElement);
        }

        monthYear.innerText = `${getMonthName(month)} ${year}`;
    }

    function getMonthName(month) {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return monthNames[month];
    }

    // Modal open and close
    function openModal(date) {
        console.log('Modal opened for date:', date);
        modal.style.display = 'block';
        modalMessage.innerText = `You've selected ${date} as the pickup date for your document/s. Click submit to proceed.`;
        
        // Ensure the hidden input is populated with the selected date
        const selectedDateInput = document.getElementById('selectedDate');
        selectedDateInput.value = date;  // Set the selected date value
    }

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Initialize current month and year
    const today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    createCalendar(currentMonth, currentYear);

    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonth = currentMonth === 0 ? 11 : currentMonth - 1;
        currentYear = currentMonth === 11 ? currentYear - 1 : currentYear;
        createCalendar(currentMonth, currentYear);
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonth = currentMonth === 11 ? 0 : currentMonth + 1;
        currentYear = currentMonth === 0 ? currentYear + 1 : currentYear;
        createCalendar(currentMonth, currentYear);
    });
});


