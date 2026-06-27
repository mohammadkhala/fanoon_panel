document.querySelectorAll('.filter-form').forEach(form => {
    form.addEventListener('submit', function (e) {

        const startInput = form.querySelector('[name="start_date"]');
        const endInput = form.querySelector('[name="end_date"]');

        // Only run if both fields exist
        if (startInput && endInput) {
            const startDate = startInput.value ? new Date(startInput.value) : null;
            const endDate = endInput.value ? new Date(endInput.value) : null;

            // Rule 1: start_date filled but end_date empty
            if (startDate && !endDate) {
                e.preventDefault();
                toastr.error('يرجى اختيار تاريخ النهاية عند تحديد تاريخ البداية.');
                return false;
            }

            if (!startDate && endDate) {
                e.preventDefault();
                toastr.error('يرجى اختيار تاريخ البداية عند تحديد تاريخ النهاية.');
                return false;
            }


            // Rule 2: both filled, start must be <= end
            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                toastr.error('يجب أن يكون تاريخ البداية أصغر من أو يساوي تاريخ النهاية.')
                return false;
            }

            // Clean empty params before submit (without removing inputs)
            ['start_date', 'end_date'].forEach(name => {
                const input = form.querySelector(`[name="${name}"]`);
                if (input && !input.value) {
                    input.setAttribute('name', ''); // clear name so it won't be submitted
                }
            });
        }
    });
});
