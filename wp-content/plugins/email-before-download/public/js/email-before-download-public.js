jQuery( document ).ready(function() {
    document.addEventListener('wpcf7mailsent', function (event) {
        var inputs = event.detail.inputs;
        var downloads = [];
        var settings = [];
        for ( var i = 0; i < inputs.length; i++ ) {
            if ( 'your-email' === inputs[i].name ) {
                var email = inputs[i].value ;
            }
            if("ebd_downloads[]" === inputs[i].name){
                downloads.push(inputs[i].value);
            }
            if('ebd_settings[]' === inputs[i].name){
                settings.push(inputs[i].value);
            }
        }
        var id = event.detail.id;
        var data = {
            action: 'ebd_inline_links',
            email: email,
            downloads: downloads,
            settings: settings,
            security: ebd_inline.ajax_nonce
        };
        jQuery.post(ebd_inline.ajaxurl, data, function (data) {
            data = JSON.parse(data);
                if(data.download){
                    window.location.href = data.url;
                }else{
                    jQuery('#' + id).append(data.html);
                }

            }
        );
    }, false);
});