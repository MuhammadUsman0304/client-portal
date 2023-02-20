jQuery(document).ready(function ($) {
    $('.insert-shortcode').click(function () {
        var pageId = $(this).data('page-id');
        $.ajax({
            url: myAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'treatmentf_insert_shortcode',
                page_id: pageId,
                nonce: myAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    // Reload the page to see the updated content
                    window.location.reload();
                    console.log(response);
                } else {
                    alert('Error inserting shortcode ');
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });

    // removing shortcode

    $('.remove-shortcode').click(function () {
        var page_id = $(this).data('page-id');
        var data = {
            action: 'treatmentf_remove_shortcode',
            page_id: page_id,
            nonce: myAjax.nonce
        };
        $.post(myAjax.ajaxurl, data, function (response) {
            alert(response.data);
        });
    });
});
