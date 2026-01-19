/**
 * Calendar Widget for Date Navigation
 * Allows users to select dates for viewing lottery results
 */

class CalendarWidget {
    constructor(elementId, options = {}) {
        this.element = document.getElementById(elementId);
        if (!this.element) return;

        this.options = {
            maxDate: new Date(), // Don't allow future dates
            onDateSelect: options.onDateSelect || this.defaultOnDateSelect.bind(this),
            ...options
        };

        this.currentDate = new Date();
        this.selectedDate = options.selectedDate ? new Date(options.selectedDate) : new Date();

        this.init();
    }

    init() {
        this.render();
    }

    render() {
        // Clear existing content
        while (this.element.firstChild) {
            this.element.removeChild(this.element.firstChild);
        }

        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();

        // Create calendar container
        const container = document.createElement('div');
        container.className = 'calendar-widget bg-white rounded-lg shadow-lg p-4 border border-gray-200';

        // Create header
        const header = this.createHeader(year, month);
        container.appendChild(header);

        // Create day names
        const dayNames = this.createDayNames();
        container.appendChild(dayNames);

        // Create dates grid
        const datesGrid = this.createDatesGrid(year, month);
        container.appendChild(datesGrid);

        // Create today button
        const todayButton = this.createTodayButton();
        container.appendChild(todayButton);

        this.element.appendChild(container);
    }

    createHeader(year, month) {
        const header = document.createElement('div');
        header.className = 'calendar-header flex items-center justify-between mb-4';

        // Previous month button
        const prevButton = document.createElement('button');
        prevButton.type = 'button';
        prevButton.className = 'prev-month px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded transition-colors';
        prevButton.setAttribute('aria-label', 'Previous month');
        prevButton.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
        prevButton.addEventListener('click', () => this.previousMonth());

        // Month/year title
        const title = document.createElement('h3');
        title.className = 'text-lg font-bold text-gray-800';
        title.textContent = `Tháng ${month + 1}/${year}`;

        // Next month button
        const nextButton = document.createElement('button');
        nextButton.type = 'button';
        nextButton.className = 'next-month px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded transition-colors';
        nextButton.setAttribute('aria-label', 'Next month');
        nextButton.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
        nextButton.addEventListener('click', () => this.nextMonth());

        header.appendChild(prevButton);
        header.appendChild(title);
        header.appendChild(nextButton);

        return header;
    }

    createDayNames() {
        const container = document.createElement('div');
        container.className = 'calendar-days grid grid-cols-7 gap-1 mb-2';

        const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        dayNames.forEach(name => {
            const dayCell = document.createElement('div');
            dayCell.className = 'text-center text-xs font-semibold text-gray-600 py-2';
            dayCell.textContent = name;
            container.appendChild(dayCell);
        });

        return container;
    }

    createDatesGrid(year, month) {
        const container = document.createElement('div');
        container.className = 'calendar-dates grid grid-cols-7 gap-1';

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();

        // Empty cells for days before month starts
        for (let i = 0; i < startingDayOfWeek; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'calendar-date-cell p-2';
            container.appendChild(emptyCell);
        }

        // Calendar dates
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dateCell = this.createDateCell(date);
            container.appendChild(dateCell);
        }

        return container;
    }

    createDateCell(date) {
        const isToday = this.isToday(date);
        const isSelected = this.isSelected(date);
        const isFuture = date > this.options.maxDate;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'calendar-date-cell p-2 text-center rounded transition-colors ';

        if (isFuture) {
            button.className += 'text-gray-300 cursor-not-allowed';
            button.disabled = true;
        } else if (isSelected) {
            button.className += 'bg-[#4a7c2c] text-white font-bold cursor-pointer';
        } else if (isToday) {
            button.className += 'bg-blue-100 text-blue-800 font-semibold hover:bg-blue-200 cursor-pointer';
        } else {
            button.className += 'text-gray-700 hover:bg-green-50 cursor-pointer';
        }

        button.textContent = date.getDate();

        if (!isFuture) {
            button.addEventListener('click', () => this.selectDate(date));
        }

        return button;
    }

    createTodayButton() {
        const container = document.createElement('div');
        container.className = 'mt-4 text-center';

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'today-button px-4 py-2 bg-[#4a7c2c] text-white rounded-lg hover:bg-[#5a8c3c] transition-colors text-sm font-medium';
        button.textContent = 'Hôm nay';
        button.addEventListener('click', () => this.goToToday());

        container.appendChild(button);
        return container;
    }

    previousMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
        this.render();
    }

    nextMonth() {
        const nextMonth = new Date(this.currentDate);
        nextMonth.setMonth(nextMonth.getMonth() + 1);

        // Don't go beyond current month if max date is today
        if (nextMonth > this.options.maxDate) {
            return;
        }

        this.currentDate = nextMonth;
        this.render();
    }

    goToToday() {
        this.selectDate(new Date());
    }

    selectDate(date) {
        this.selectedDate = date;
        this.currentDate = new Date(date);
        this.render();
        this.options.onDateSelect(date);
    }

    defaultOnDateSelect(date) {
        // Default behavior: update date input if exists
        const dateInput = document.querySelector('input[name="date"]');
        if (dateInput) {
            dateInput.value = this.formatDate(date);
            // Trigger change event
            dateInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    isToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    }

    isSelected(date) {
        return date.toDateString() === this.selectedDate.toDateString();
    }

    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
}

// Initialize calendar widgets on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize any element with class 'calendar-widget-container'
    const containers = document.querySelectorAll('.calendar-widget-container');
    containers.forEach(container => {
        const selectedDate = container.getAttribute('data-selected-date');
        new CalendarWidget(container.id, {
            selectedDate: selectedDate || new Date()
        });
    });
});

// Export for manual initialization
window.CalendarWidget = CalendarWidget;
