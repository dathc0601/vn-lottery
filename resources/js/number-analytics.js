/**
 * Number Analytics Dashboard - Custom JavaScript
 * Interactive features and micro-interactions
 */

(function() {
    'use strict';

    /**
     * Initialize analytics dashboard features
     */
    function initAnalyticsDashboard() {
        console.log('Number Analytics Dashboard initialized');

        // Add staggered animation classes to cards
        animateCards();

        // Initialize tooltips
        initTooltips();

        // Smooth scroll behavior
        initSmoothScroll();

        // Number counter animations
        initCounterAnimations();

        // Listen for Livewire updates
        setupLivewireListeners();
    }

    /**
     * Animate cards with staggered entrance
     */
    function animateCards() {
        const cards = document.querySelectorAll('.performer-card, .comparison-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    /**
     * Initialize custom tooltips
     */
    function initTooltips() {
        const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
        tooltipTriggers.forEach(trigger => {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = trigger.getAttribute('data-tooltip');
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(0, 0, 0, 0.9);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-size: 0.875rem;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.3s;
                z-index: 1000;
            `;

            trigger.addEventListener('mouseenter', (e) => {
                document.body.appendChild(tooltip);
                const rect = e.target.getBoundingClientRect();
                tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
                tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
                setTimeout(() => tooltip.style.opacity = '1', 10);
            });

            trigger.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
                setTimeout(() => {
                    if (tooltip.parentNode) {
                        document.body.removeChild(tooltip);
                    }
                }, 300);
            });
        });
    }

    /**
     * Initialize smooth scroll behavior
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Animate number counters
     */
    function initCounterAnimations() {
        const counters = document.querySelectorAll('.counter-animate');
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.textContent);
                    animateCounter(counter, 0, target, 1000);
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);

        counters.forEach(counter => observer.observe(counter));
    }

    /**
     * Animate a counter from start to end value
     */
    function animateCounter(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= end) {
                element.textContent = end;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    /**
     * Setup Livewire event listeners
     */
    function setupLivewireListeners() {
        if (typeof Livewire !== 'undefined') {
            // Re-initialize animations after Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                setTimeout(() => {
                    animateCards();
                    initCounterAnimations();
                }, 100);
            });

            // Listen for custom events
            window.addEventListener('number-selected', (event) => {
                console.log('Number selected:', event.detail.number);
                // Could trigger modal, highlight, etc.
            });
        }
    }

    /**
     * Utility: Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Utility: Throttle function
     */
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    /**
     * Heat Map Interactions
     */
    function initHeatMapInteractions() {
        const heatCells = document.querySelectorAll('.heat-cell');
        heatCells.forEach(cell => {
            cell.addEventListener('click', function() {
                // Add pulse effect on click
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = '';
                }, 10);

                // Could trigger detail modal here
                const number = this.querySelector('.number-text')?.textContent;
                if (number) {
                    console.log('Heat cell clicked:', number);
                    // Dispatch custom event
                    window.dispatchEvent(new CustomEvent('heat-cell-clicked', {
                        detail: { number }
                    }));
                }
            });
        });
    }

    /**
     * Add keyboard navigation for heat map
     */
    function initKeyboardNavigation() {
        let focusedCell = null;
        const heatCells = Array.from(document.querySelectorAll('.heat-cell'));

        document.addEventListener('keydown', (e) => {
            if (!focusedCell && heatCells.length > 0) {
                focusedCell = heatCells[0];
            }

            if (!focusedCell) return;

            const currentIndex = heatCells.indexOf(focusedCell);
            let newIndex = currentIndex;

            switch(e.key) {
                case 'ArrowRight':
                    newIndex = Math.min(currentIndex + 1, heatCells.length - 1);
                    break;
                case 'ArrowLeft':
                    newIndex = Math.max(currentIndex - 1, 0);
                    break;
                case 'ArrowDown':
                    newIndex = Math.min(currentIndex + 10, heatCells.length - 1);
                    break;
                case 'ArrowUp':
                    newIndex = Math.max(currentIndex - 10, 0);
                    break;
                case 'Enter':
                    focusedCell.click();
                    return;
                default:
                    return;
            }

            e.preventDefault();
            focusedCell.style.outline = 'none';
            focusedCell = heatCells[newIndex];
            focusedCell.style.outline = '3px solid #fbbf24';
            focusedCell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    }

    /**
     * Export utility functions to window for use in Alpine/Livewire
     */
    window.NumberAnalytics = {
        debounce,
        throttle,
        animateCounter,
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAnalyticsDashboard);
    } else {
        initAnalyticsDashboard();
    }

    // Re-initialize after dynamic content loads
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:load', () => {
            setTimeout(() => {
                initHeatMapInteractions();
                initKeyboardNavigation();
            }, 500);
        });
    }

})();
