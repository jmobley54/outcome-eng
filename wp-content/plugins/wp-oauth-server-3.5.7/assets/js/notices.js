jQuery(function ($) {
    $(document).on('click', '.wo_30day_notice .notice-dismiss', function () {
        var type = $(this).closest('.wo_30day_notice').data('notice');
        $.ajax(ajaxurl,
            {
                type: 'POST',
                data: {
                    action: 'wo_wo30notice_dismiss',
                    type: type,
                }
            });
    });

});