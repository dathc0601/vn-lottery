/**
 * Tab Switching Functionality
 * Handles province tabs and day-of-week tabs
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeDayTabs();
});

/**
 * Initialize province/content tabs
 */
function initializeTabs() {
    const tabButtons = document.querySelectorAll('[role="tab"]');

    if (tabButtons.length === 0) return;

    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('data-tab-target');
            if (!targetId) return;

            // Remove active state from all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-[#5a8c3c]', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
                btn.setAttribute('aria-selected', 'false');
            });

            // Add active state to clicked tab
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('active', 'bg-[#5a8c3c]', 'text-white');
            this.setAttribute('aria-selected', 'true');

            // Hide all tab panels
            const tabPanels = document.querySelectorAll('[role="tabpanel"]');
            tabPanels.forEach(panel => {
                panel.classList.add('hidden');
                panel.setAttribute('aria-hidden', 'true');
            });

            // Show target tab panel
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.remove('hidden');
                targetPanel.setAttribute('aria-hidden', 'false');

                // Smooth scroll to panel
                targetPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
}

/**
 * Initialize day-of-week tabs
 */
function initializeDayTabs() {
    const dayButtons = document.querySelectorAll('.day-tab');

    if (dayButtons.length === 0) return;

    dayButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const day = this.getAttribute('data-day');
            if (!day) return;

            // Update active state
            dayButtons.forEach(btn => {
                btn.classList.remove('bg-[#4a7c2c]', 'text-white', 'font-bold');
                btn.classList.add('bg-white', 'text-gray-700');
            });

            this.classList.remove('bg-white', 'text-gray-700');
            this.classList.add('bg-[#4a7c2c]', 'text-white', 'font-bold');

            // Fetch results for selected day (via AJAX or page reload)
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('day', day);

            // For now, reload page with day parameter
            window.location.href = currentUrl.toString();
        });
    });
}

/**
 * Keyboard navigation for tabs
 */
document.addEventListener('keydown', function(e) {
    const activeTab = document.querySelector('[role="tab"][aria-selected="true"]');
    if (!activeTab) return;

    const allTabs = Array.from(document.querySelectorAll('[role="tab"]'));
    const currentIndex = allTabs.indexOf(activeTab);

    let newIndex = currentIndex;

    // Arrow key navigation
    if (e.key === 'ArrowLeft') {
        newIndex = currentIndex > 0 ? currentIndex - 1 : allTabs.length - 1;
    } else if (e.key === 'ArrowRight') {
        newIndex = currentIndex < allTabs.length - 1 ? currentIndex + 1 : 0;
    } else {
        return;
    }

    e.preventDefault();
    allTabs[newIndex].click();
    allTabs[newIndex].focus();
});
