document.addEventListener('DOMContentLoaded', function() {
    // Confirm before deleting
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this case?')) {
                e.preventDefault();
            }
        });
    });

    // Filter cases
    const filterForm = document.getElementById('filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            window.location.href = `index.php?${params}`;
        });
    }

    // Reset filter
    const resetFilter = document.getElementById('reset-filter');
    if (resetFilter) {
        resetFilter.addEventListener('click', function() {
            window.location.href = 'index.php';
        });
    }

    // Date picker initialization (if you're using a datepicker library)
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.datepicker', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    // Time picker initialization
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.timepicker', {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    }
});