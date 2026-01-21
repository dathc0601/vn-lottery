/**
 * Load More Results functionality for regional lottery pages
 */
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const resultsContainer = document.getElementById('results-container');
    const loadMoreContainer = document.getElementById('load-more-container');

    if (!loadMoreBtn || !resultsContainer) {
        return;
    }

    let isLoading = false;

    const regionLabels = {
        'xsmb': 'KQXS MB',
        'xsmt': 'KQXS MT',
        'xsmn': 'KQXS MN'
    };

    loadMoreBtn.addEventListener('click', async function() {
        if (isLoading) return;

        const region = this.dataset.region;
        const nextDate = this.dataset.nextDate;

        if (!region || !nextDate) {
            console.error('Missing region or date');
            return;
        }

        isLoading = true;
        setLoadingState(true, region);

        try {
            const response = await fetch(`/api/load-more/${region}/${nextDate}`);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.error) {
                showError(data.error);
                return;
            }

            if (data.html && data.resultsCount > 0) {
                // Add date separator before new results
                const separator = createDateSeparator(data.currentDate);
                resultsContainer.appendChild(separator);

                // Append new results using DOMParser for safer HTML insertion
                // The HTML is server-rendered from trusted Blade templates
                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');
                const newElements = doc.body.children;

                // Move all children from parsed document to results container
                while (newElements.length > 0) {
                    resultsContainer.appendChild(newElements[0]);
                }

                // Re-initialize digit display radios for new cards
                initializeDigitDisplayRadios();

                // Update button with next date
                if (data.hasMore && data.nextDate) {
                    loadMoreBtn.dataset.nextDate = data.nextDate;
                } else {
                    // No more results, show message and disable button
                    showNoMoreResults(region);
                }
            } else if (!data.hasMore) {
                showNoMoreResults(region);
            } else {
                // No results found for this date, but more dates available
                loadMoreBtn.dataset.nextDate = data.nextDate;
            }

        } catch (error) {
            console.error('Error loading more results:', error);
            showError('Có lỗi xảy ra. Vui lòng thử lại.');
        } finally {
            isLoading = false;
            setLoadingState(false, region);
        }
    });

    function setLoadingState(loading, region) {
        const loadIcon = loadMoreBtn.querySelector('.load-icon');
        const loadingSpinner = loadMoreBtn.querySelector('.loading-spinner');
        const loadMoreText = document.getElementById('load-more-text');

        if (loading) {
            loadMoreBtn.disabled = true;
            if (loadIcon) loadIcon.classList.add('hidden');
            if (loadingSpinner) loadingSpinner.classList.remove('hidden');
            if (loadMoreText) loadMoreText.textContent = 'Đang tải...';
        } else {
            loadMoreBtn.disabled = false;
            if (loadIcon) loadIcon.classList.remove('hidden');
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
            if (loadMoreText) loadMoreText.textContent = `Xem thêm ${regionLabels[region] || 'KQXS'}`;
        }
    }

    function createDateSeparator(date) {
        const separator = document.createElement('div');
        separator.className = 'date-separator bg-gray-100 border border-gray-300 px-4 py-3 mb-5 rounded text-center';

        // Create SVG icon
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'w-4 h-4 inline-block mr-2');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('viewBox', '0 0 24 24');

        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('stroke-linecap', 'round');
        path.setAttribute('stroke-linejoin', 'round');
        path.setAttribute('stroke-width', '2');
        path.setAttribute('d', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z');
        svg.appendChild(path);

        // Create text span
        const span = document.createElement('span');
        span.className = 'font-semibold text-gray-700';
        span.appendChild(svg);
        span.appendChild(document.createTextNode(`Kết quả ngày ${date}`));

        separator.appendChild(span);
        return separator;
    }

    function showNoMoreResults(region) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.classList.remove('bg-[#ff6600]', 'hover:bg-[#ff7700]');
        loadMoreBtn.classList.add('bg-gray-400', 'cursor-not-allowed');

        const loadIcon = loadMoreBtn.querySelector('.load-icon');
        const loadMoreText = document.getElementById('load-more-text');

        if (loadIcon) loadIcon.classList.add('hidden');
        if (loadMoreText) loadMoreText.textContent = 'Đã hiển thị tất cả kết quả trong 30 ngày qua';
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-center';

        const messagePara = document.createElement('p');
        messagePara.textContent = message;
        errorDiv.appendChild(messagePara);

        const retryBtn = document.createElement('button');
        retryBtn.className = 'mt-2 text-sm underline hover:no-underline retry-btn';
        retryBtn.textContent = 'Thử lại';
        retryBtn.addEventListener('click', function() {
            errorDiv.remove();
            loadMoreBtn.click();
        });
        errorDiv.appendChild(retryBtn);

        loadMoreContainer.insertBefore(errorDiv, loadMoreBtn);
    }

    function initializeDigitDisplayRadios() {
        // Find all result cards and re-initialize their digit display radios
        const resultCards = resultsContainer.querySelectorAll('.result-card');

        resultCards.forEach(card => {
            const cardId = card.id.replace('result-', '');
            const radios = card.querySelectorAll(`input[name="digit-display-${cardId}"]`);

            radios.forEach(radio => {
                // Remove existing listeners by cloning
                const newRadio = radio.cloneNode(true);
                radio.parentNode.replaceChild(newRadio, radio);

                newRadio.addEventListener('change', function() {
                    const displayType = this.value;
                    const numbers = card.querySelectorAll('.result-table-xskt .number');

                    numbers.forEach(numberSpan => {
                        const originalNumber = numberSpan.getAttribute('data-original') || numberSpan.textContent.trim();

                        // Store original if not stored yet
                        if (!numberSpan.getAttribute('data-original')) {
                            numberSpan.setAttribute('data-original', originalNumber);
                        }

                        if (displayType === 'all') {
                            numberSpan.textContent = originalNumber;
                        } else if (displayType === '2') {
                            // Show last 2 digits
                            if (originalNumber.length >= 2) {
                                numberSpan.textContent = originalNumber.slice(-2);
                            }
                        } else if (displayType === '3') {
                            // Show last 3 digits
                            if (originalNumber.length >= 3) {
                                numberSpan.textContent = originalNumber.slice(-3);
                            }
                        }
                    });
                });
            });
        });
    }
});
