/**
 * Provinces Management Page - Interactive Features
 * Handles drag-and-drop reordering with SortableJS
 */

import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', function () {
    initializeProvincesGrid();
});

// Re-initialize after Livewire updates
document.addEventListener('livewire:navigated', function () {
    initializeProvincesGrid();
});

function initializeProvincesGrid() {
    const grid = document.querySelector('.provinces-grid');

    if (!grid) {
        return;
    }

    // Initialize SortableJS
    Sortable.create(grid, {
        animation: 200,
        handle: '.btn-drag',
        ghostClass: 'province-card-ghost',
        dragClass: 'province-card-dragging',
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',

        onStart: function (evt) {
            // Add dragging state to body for global styles
            document.body.classList.add('provinces-dragging');
        },

        onEnd: function (evt) {
            // Remove dragging state
            document.body.classList.remove('provinces-dragging');

            // Get new order of province IDs
            const order = Array.from(grid.children).map(card =>
                parseInt(card.dataset.provinceId)
            );

            // Update via Livewire
            if (window.Livewire) {
                // Find the Livewire component
                const component = window.Livewire.find(
                    grid.closest('[wire\\:id]')?.getAttribute('wire:id')
                );

                if (component) {
                    component.call('updateProvinceOrder', order);
                }
            }
        },

        onMove: function (evt) {
            // Prevent moving if drag handle is disabled
            if (evt.related.classList.contains('disabled')) {
                return false;
            }
        }
    });
}

/**
 * Initialize when included via module bundler
 */
export function initProvinces() {
    initializeProvincesGrid();
}

/**
 * Export for global access
 */
window.initProvinces = initProvinces;
