jQuery(document).ready(function ($) {
    var $form = $('#fluent_demo');

    $('#demo_func').on('change', function () {
        var val = $(this).val();
        if(val == 'none') {
            $('#doc_link').hide();
        } else {
            $('#doc_link').show().find('a').attr('href', 'https://developer.wordpress.org/reference/functions/'+val);
        }
    });

    $form.on('submit', function (event) {
        event.preventDefault();
        $('.data_responses').hide();
        var data = $(this).serialize();
        $.get(window.fluent_demo.ajax_url, data)
            .then(response => {
                $('.response_body').text(response);
                $('.response_html').html(response);
                $('.data_responses').show();
            });
    });
});
