jQuery(document).ready(function($) {
    $('#publications-filter-form').on('submit', function(e) {
        e.preventDefault();
        filterPublications(1);
    });
    
    $(document).on('click', '.publications-pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').match(/page\/(\d+)/)[1];
        filterPublications(page);
    });
    
    function filterPublications(page) {
        var $form = $('#publications-filter-form');
        var $publicationsList = $('#publications-list');
        
        $.ajax({
            url: publications_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_publications',
                type: $('#publication-type').val(),
                year: $('#publication-year').val(),
                author: $('#publication-author').val(),
                search: $('#publication-search').val(),
                page: page,
                nonce: publications_vars.nonce
            },
            beforeSend: function() {
                $publicationsList.html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function(response) {
                $publicationsList.html(response);
            },
            error: function() {
                $publicationsList.html('<p>Error loading publications. Please try again.</p>');
            }
        });
    }
    
    // Trigger filter on select change
    $('#publication-type, #publication-year, #publication-author').on('change', function() {
        $('#publications-filter-form').trigger('submit');
    });
});