/**
 * Utility Functions
 * Smooth scrolling, form validation, and other helpers
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeSmoothScroll();
    initializeFormValidation();
    initializeBackToTop();
});

/**
 * Smooth scroll to anchors
 */
function initializeSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            // Skip if href is just "#"
            if (href === '#') return;

            const target = document.querySelector(href);
            if (!target) return;

            e.preventDefault();

            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Update URL without page jump
            if (history.pushState) {
                history.pushState(null, null, href);
            }
        });
    });
}

/**
 * Enhanced form validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                return false;
            }
        });

        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });

    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let errorMessage = '';

    // Remove existing error
    removeFieldError(field);

    // Required check
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Trường này là bắt buộc';
    }

    // Date validation
    else if (type === 'date' && value) {
        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate > today) {
            isValid = false;
            errorMessage = 'Không thể chọn ngày trong tương lai';
        }
    }

    // Number validation
    else if (type === 'number' && value) {
        const min = field.getAttribute('min');
        const max = field.getAttribute('max');

        if (min && parseFloat(value) < parseFloat(min)) {
            isValid = false;
            errorMessage = `Giá trị tối thiểu là ${min}`;
        }

        if (max && parseFloat(value) > parseFloat(max)) {
            isValid = false;
            errorMessage = `Giá trị tối đa là ${max}`;
        }
    }

    // Pattern validation
    else if (field.hasAttribute('pattern') && value) {
        const pattern = new RegExp(field.getAttribute('pattern'));
        if (!pattern.test(value)) {
            isValid = false;
            errorMessage = field.getAttribute('title') || 'Định dạng không hợp lệ';
        }
    }

    if (!isValid) {
        showFieldError(field, errorMessage);
    }

    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-red-600 text-sm mt-1';
    errorDiv.textContent = message;

    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

function removeFieldError(field) {
    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

/**
 * Back to top button
 */
function initializeBackToTop() {
    // Create back to top button
    const button = document.createElement('button');
    button.id = 'back-to-top';
    button.className = 'fixed bottom-8 right-8 bg-[#4a7c2c] text-white p-3 rounded-full shadow-lg hover:bg-[#5a8c3c] transition-all duration-300 opacity-0 pointer-events-none z-50';
    button.setAttribute('aria-label', 'Quay lên đầu trang');

    // Create SVG icon
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('class', 'w-6 h-6');
    svg.setAttribute('fill', 'currentColor');
    svg.setAttribute('viewBox', '0 0 20 20');

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('fill-rule', 'evenodd');
    path.setAttribute('d', 'M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z');
    path.setAttribute('clip-rule', 'evenodd');

    svg.appendChild(path);
    button.appendChild(svg);

    document.body.appendChild(button);

    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            button.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            button.classList.add('opacity-0', 'pointer-events-none');
        }
    });

    // Scroll to top on click
    button.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Copy to clipboard helper
 */
window.copyToClipboard = function(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        return navigator.clipboard.writeText(text);
    } else {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        return Promise.resolve();
    }
};

/**
 * Format number with Vietnamese locale
 */
window.formatNumber = function(num) {
    return new Intl.NumberFormat('vi-VN').format(num);
};

/**
 * Format currency (VND)
 */
window.formatCurrency = function(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
};
