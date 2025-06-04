jQuery(document).ready(function ($) {
    // Find the ORCID ID field - updated to field_8
    var $orcidField = $('input[name="field_8"]');

    if ($orcidField.length) {
        // Add verification message container
        $orcidField.after('<div class="orcid-verification-message"></div>');

        // Add verification button
        $orcidField.after('<button type="button" class="verify-orcid-button">Verify ORCID ID</button>');

        // Handle verification button click
        $('.verify-orcid-button').on('click', function () {
            var orcidId = $orcidField.val();
            var $message = $('.orcid-verification-message');

            if (!orcidId) {
                $message.html('<p class="error">Please enter an ORCID ID</p>');
                return;
            }

            // Show loading state
            $message.html('<p class="loading">Verifying ORCID ID...</p>');

            // Send verification request
            $.ajax({
                url: orcid_vars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'verify_orcid_id',
                    nonce: orcid_vars.nonce,
                    orcid_id: orcidId
                },
                success: function (response) {
                    if (response.success) {
                        $message.html('<p class="success">✓ ' + response.data + '</p>');
                        // Refresh the publications tab if it exists
                        if ($('.orcid-publications-list').length) {
                            $('.refresh-orcid-publications').trigger('click');
                        }
                    } else {
                        $message.html('<p class="error">✗ ' + response.data + '</p>');
                    }
                },
                error: function () {
                    $message.html('<p class="error">Error verifying ORCID ID. Please try again.</p>');
                }
            });
        });

        // Add input validation for ORCID format
        $orcidField.on('input', function() {
            var value = $(this).val();
            // Remove any non-alphanumeric characters except X
            value = value.replace(/[^0-9X]/g, '');
            // Format with dashes
            if (value.length >= 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            if (value.length >= 9) {
                value = value.substring(0, 9) + '-' + value.substring(9);
            }
            if (value.length >= 14) {
                value = value.substring(0, 14) + '-' + value.substring(14);
            }
            $(this).val(value);
        });
    }
}); 