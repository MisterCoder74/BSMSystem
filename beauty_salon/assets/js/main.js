/**
 * Beauty Salon Management System
 * Main JavaScript file
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initModals();
    initDeleteConfirmations();
    initDatePickers();
    initFilterSorting();
    initAppointmentCalendar();
});

/**
 * Initialize modal functionality
 */
function initModals() {
    // Open modal buttons
    const modalTriggers = document.querySelectorAll('[data-modal-target]');

    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);

            if (modal) {
                openModal(modal);
            }
        });
    });

    // Close modal buttons
    const closeBtns = document.querySelectorAll('.modal-close, .modal-cancel');

    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal-backdrop');
            closeModal(modal);
        });
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            closeModal(e.target);
        }
    });
}

/**
 * Open a modal
 */
function openModal(modal) {
    modal.classList.add('show');
}

/**
 * Close a modal
 */
function closeModal(modal) {
    modal.classList.remove('show');
}

/**
 * Initialize delete confirmations
 */
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Initialize date pickers
 */
function initDatePickers() {
    // This is a simple implementation - you might want to use a library
    // like flatpickr for more advanced date picking
    const datePickers = document.querySelectorAll('input[type="date"]');

    datePickers.forEach(picker => {
        // Set min date to today for appointment dates
        if (picker.classList.contains('future-date-only')) {
            picker.min = new Date().toISOString().split('T')[0];
        }
    });
}

/**
 * Initialize filter and sorting functionality
 */
function initFilterSorting() {
    const filterForms = document.querySelectorAll('.filter-form');

    filterForms.forEach(form => {
        // Auto-submit form when select fields change
        const selects = form.querySelectorAll('select');

        selects.forEach(select => {
            select.addEventListener('change', function() {
                form.submit();
            });
        });
    });
}

/**
 * Initialize appointment calendar
 */
function initAppointmentCalendar() {
    const calendar = document.getElementById('appointment-calendar');

    if (!calendar) return;

    // Handle month navigation
    const prevMonth = document.getElementById('prev-month');
    const nextMonth = document.getElementById('next-month');

    if (prevMonth) {
        prevMonth.addEventListener('click', function(e) {
            e.preventDefault();
            navigateCalendar('prev');
        });
    }

    if (nextMonth) {
        nextMonth.addEventListener('click', function(e) {
            e.preventDefault();
            navigateCalendar('next');
        });
    }

    // Initialize appointment viewing
    const appointments = document.querySelectorAll('.appointment');

    appointments.forEach(appt => {
        appt.addEventListener('click', function() {
            const apptId = this.getAttribute('data-id');
            viewAppointment(apptId);
        });
    });
}

/**
 * Navigate calendar to previous or next month
 */
function navigateCalendar(direction) {
    const currentMonth = document.getElementById('current-month').getAttribute('data-month');
    const currentYear = document.getElementById('current-month').getAttribute('data-year');

    let month = parseInt(currentMonth);
    let year = parseInt(currentYear);

    if (direction === 'prev') {
        month--;
        if (month < 1) {
            month = 12;
            year--;
        }
    } else {
        month++;
        if (month > 12) {
            month = 1;
            year++;
        }
    }

    // Redirect to the new month/year
    window.location.href = `index.php?page=appointments&month=${month}&year=${year}`;
}

/**
 * View appointment details
 */
function viewAppointment(id) {
    // You can implement AJAX to fetch appointment details
    // For now, we'll just redirect to the appointment view page
    window.location.href = `index.php?page=appointments&action=view&id=${id}`;
}

/**
 * Format currency amount
 */
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

/**
 * Search functionality
 */
function performSearch(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.submit();
    }
}

/**
 * Validate form
 */
function validateForm(formId) {
    const form = document.getElementById(formId);

    if (!form) return true;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');

            // Add error message if not exists
            let errorMsg = field.nextElementSibling;
            if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                errorMsg = document.createElement('div');
                errorMsg.classList.add('error-message');
                errorMsg.textContent = 'This field is required';
                field.parentNode.insertBefore(errorMsg, field.nextSibling);
            }
        } else {
            field.classList.remove('error');

            // Remove error message if exists
            const errorMsg = field.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.remove();
            }
        }
    });

    return isValid;
}

/**
 * Toggle password visibility
 */
function togglePasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);

    if (passwordInput) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    }
}

/**
 * Print element
 */
function printElement(elementId) {
    const element = document.getElementById(elementId);

    if (element) {
        const originalContents = document.body.innerHTML;
        document.body.innerHTML = element.innerHTML;
        window.print();
        document.body.innerHTML = originalContents;
    }
}
