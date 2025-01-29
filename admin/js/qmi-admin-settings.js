jQuery(document).ready(function($) {
    // Function to handle adding new conditions
    $('#qmi-add-condition').on('click', function(e) {
        e.preventDefault();

        var newIndex = $('.qmi-condition').length + 1;

        var newCondition = `
            <div class="qmi-condition">
                <div class="qmi-row-number">${newIndex}</div> <!-- New row number column -->
                <input type="number" name="qmi_settings[qmi_conditions][${newIndex}][membership_level]" placeholder="Membership Level ID">
                <input type="number" name="qmi_settings[qmi_conditions][${newIndex}][min_score]" placeholder="Min Quiz Score">
                <input type="number" name="qmi_settings[qmi_conditions][${newIndex}][max_score]" placeholder="Max Quiz Score">
                
                <!-- Toggle button -->
                <label class="qmi-toggle">
                    <input type="checkbox" name="qmi_settings[qmi_conditions][${newIndex}][active]" checked>
                    <span class="qmi-slider round"></span>
                    <input type="hidden" name="qmi_settings[qmi_conditions][${newIndex}][active]" value="1"> <!-- Hidden field for toggle state -->
                </label>
                
                <button class="qmi-remove-condition">Remove</button>
            </div>
        `;

        $('#qmi-conditions-container').append(newCondition);
        updateRowNumbers();
    });

    // Function to handle removing conditions
    $('#qmi-conditions-container').on('click', '.qmi-remove-condition', function(e) {
        e.preventDefault();
        $(this).closest('.qmi-condition').remove();
        updateRowNumbers();
        $('#qmi-settings-form').submit(); // Submit form to update backend
    });

    // Function to handle initial state and toggle button behavior
    $('#qmi-conditions-container').on('change', '.qmi-toggle input[type="checkbox"]', function() {
        var isChecked = $(this).prop('checked');
        $(this).closest('.qmi-condition').toggleClass('inactive', !isChecked);
        $(this).siblings('input[type="hidden"]').val(isChecked ? '1' : '0'); // Update hidden field

        $('#qmi-settings-form').submit(); // Submit form to update backend
    });

    // Trigger initial state check on page load
    $('.qmi-toggle input[type="checkbox"]').each(function() {
        var isChecked = $(this).prop('checked');
        $(this).closest('.qmi-condition').toggleClass('inactive', !isChecked);
    });

    // Function to update row numbers
    function updateRowNumbers() {
        $('.qmi-condition').each(function(index) {
            $(this).find('.qmi-row-number').text(index + 1);
        });
    }

    // Initial row number update
    updateRowNumbers();
});
