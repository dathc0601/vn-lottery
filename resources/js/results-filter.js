/**
 * AJAX Results Filtering
 * Handles filtering lottery results without page reload
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get filter elements
    const dateInput = document.querySelector('input[name="date"]');
    const provinceSelect = document.querySelector('select[name="province_id"]');
    const regionSelect = document.querySelector('select[name="region"]');

    if (!dateInput && !provinceSelect && !regionSelect) {
        return; // No filters on this page
    }

    // Handle date change
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            updateResults();
        });
    }

    // Handle province change
    if (provinceSelect) {
        provinceSelect.addEventListener('change', function() {
            updateResults();
        });
    }

    // Handle region change
    if (regionSelect) {
        regionSelect.addEventListener('change', function() {
            updateResults();
        });
    }

    /**
     * Update results via AJAX
     */
    function updateResults() {
        const currentUrl = new URL(window.location.href);

        // Update URL parameters
        if (dateInput && dateInput.value) {
            currentUrl.searchParams.set('date', dateInput.value);
        }

        if (provinceSelect && provinceSelect.value) {
            currentUrl.searchParams.set('province_id', provinceSelect.value);
        } else if (provinceSelect) {
            currentUrl.searchParams.delete('province_id');
        }

        if (regionSelect && regionSelect.value) {
            currentUrl.searchParams.set('region', regionSelect.value);
        } else if (regionSelect) {
            currentUrl.searchParams.delete('region');
        }

        // Update browser URL without reload
        window.history.pushState({}, '', currentUrl);

        // Show loading indicator
        showLoading();

        // Fetch new results
        fetch(currentUrl.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update results container by replacing the element
            const newContent = doc.querySelector('#results-container');
            const currentContent = document.querySelector('#results-container');

            if (newContent && currentContent) {
                currentContent.replaceWith(newContent);
            }

            hideLoading();
        })
        .catch(error => {
            console.error('Error fetching results:', error);
            hideLoading();
        });
    }

    /**
     * Show loading indicator
     */
    function showLoading() {
        const resultsContainer = document.querySelector('#results-container');
        if (resultsContainer) {
            resultsContainer.style.opacity = '0.5';
            resultsContainer.style.pointerEvents = 'none';
        }
    }

    /**
     * Hide loading indicator
     */
    function hideLoading() {
        const resultsContainer = document.querySelector('#results-container');
        if (resultsContainer) {
            resultsContainer.style.opacity = '1';
            resultsContainer.style.pointerEvents = 'auto';
        }
    }

    /**
     * Handle browser back/forward buttons
     */
    window.addEventListener('popstate', function() {
        location.reload();
    });
});
