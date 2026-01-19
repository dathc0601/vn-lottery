/**
 * Number Search and Highlighting
 * Search for specific numbers in lottery results
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeNumberSearch();
});

function initializeNumberSearch() {
    // Create search box if on results page
    const resultsTable = document.querySelector('.result-table');
    if (!resultsTable) return;

    // Check if search box already exists
    if (document.getElementById('number-search-container')) return;

    // Create search container
    const searchContainer = document.createElement('div');
    searchContainer.id = 'number-search-container';
    searchContainer.className = 'bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-300 rounded-lg p-4 mb-4';

    // Create search input
    const searchWrapper = document.createElement('div');
    searchWrapper.className = 'flex items-center gap-3';

    const label = document.createElement('label');
    label.className = 'text-sm font-semibold text-gray-700';
    label.textContent = 'Tìm số:';

    const input = document.createElement('input');
    input.type = 'text';
    input.id = 'number-search-input';
    input.className = 'px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent';
    input.placeholder = 'Nhập số cần tìm (2-6 chữ số)';
    input.maxLength = 6;
    input.pattern = '[0-9]*';

    const clearButton = document.createElement('button');
    clearButton.type = 'button';
    clearButton.className = 'px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors';
    clearButton.textContent = 'Xóa';
    clearButton.style.display = 'none';

    // Assemble search box
    searchWrapper.appendChild(label);
    searchWrapper.appendChild(input);
    searchWrapper.appendChild(clearButton);
    searchContainer.appendChild(searchWrapper);

    // Insert search box before results table
    resultsTable.parentNode.insertBefore(searchContainer, resultsTable);

    // Event listeners
    input.addEventListener('input', handleNumberSearch);
    input.addEventListener('keypress', function(e) {
        // Only allow numbers
        if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
        }
    });

    clearButton.addEventListener('click', function() {
        input.value = '';
        clearHighlights();
        clearButton.style.display = 'none';
    });
}

function handleNumberSearch(e) {
    const searchValue = e.target.value.trim();
    const clearButton = document.querySelector('#number-search-container button');

    // Clear previous highlights
    clearHighlights();

    if (searchValue.length === 0) {
        clearButton.style.display = 'none';
        return;
    }

    clearButton.style.display = 'block';

    // Search in all prize cells
    const prizeCells = document.querySelectorAll('.result-table td:not(.prize-label)');
    let matchCount = 0;

    prizeCells.forEach(cell => {
        const cellText = cell.textContent.trim();

        // Check if cell contains the search number
        if (cellText.includes(searchValue)) {
            highlightCell(cell, searchValue);
            matchCount++;
        }
    });

    // Show match count
    updateMatchCount(matchCount, searchValue);
}

function highlightCell(cell, searchValue) {
    const originalText = cell.textContent;
    const parts = originalText.split(searchValue);

    // Clear cell content
    while (cell.firstChild) {
        cell.removeChild(cell.firstChild);
    }

    // Rebuild with highlighted parts
    parts.forEach((part, index) => {
        // Add text part
        if (part) {
            const textNode = document.createTextNode(part);
            cell.appendChild(textNode);
        }

        // Add highlighted search value (except after last part)
        if (index < parts.length - 1) {
            const highlight = document.createElement('mark');
            highlight.className = 'bg-yellow-300 font-bold px-1 rounded';
            highlight.textContent = searchValue;
            cell.appendChild(highlight);
        }
    });

    // Add a visual indicator to the cell
    cell.classList.add('bg-yellow-50', 'ring-2', 'ring-yellow-400');
}

function clearHighlights() {
    // Remove all highlights
    const highlights = document.querySelectorAll('.result-table mark');
    highlights.forEach(mark => {
        const parent = mark.parentNode;
        const textContent = mark.textContent;
        const textNode = document.createTextNode(textContent);
        parent.replaceChild(textNode, mark);
    });

    // Normalize text nodes
    const prizeCells = document.querySelectorAll('.result-table td:not(.prize-label)');
    prizeCells.forEach(cell => {
        cell.normalize();
        cell.classList.remove('bg-yellow-50', 'ring-2', 'ring-yellow-400');
    });

    // Remove match count
    const matchInfo = document.getElementById('search-match-count');
    if (matchInfo) {
        matchInfo.remove();
    }
}

function updateMatchCount(count, searchValue) {
    // Remove existing match count
    const existing = document.getElementById('search-match-count');
    if (existing) {
        existing.remove();
    }

    if (count === 0) return;

    // Create match count element using DOM methods
    const matchInfo = document.createElement('div');
    matchInfo.id = 'search-match-count';
    matchInfo.className = 'mt-2 text-sm text-green-700 font-medium';

    // Create SVG icon
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('class', 'w-4 h-4 inline mr-1');
    svg.setAttribute('fill', 'currentColor');
    svg.setAttribute('viewBox', '0 0 20 20');

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('fill-rule', 'evenodd');
    path.setAttribute('d', 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z');
    path.setAttribute('clip-rule', 'evenodd');

    svg.appendChild(path);
    matchInfo.appendChild(svg);

    const text = document.createTextNode(` Tìm thấy ${count} kết quả chứa số "${searchValue}"`);
    matchInfo.appendChild(text);

    const searchContainer = document.getElementById('number-search-container');
    searchContainer.appendChild(matchInfo);
}
