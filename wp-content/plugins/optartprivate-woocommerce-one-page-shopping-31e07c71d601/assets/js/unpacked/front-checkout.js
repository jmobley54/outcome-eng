/**
 * This file contains the scripts which are copied from original WooCommerce checkout.js
 * file. We change them a bit to fix into our solution.
 *
 *
 * ATTENTION!
 * Please remember about packing this file after you'll finish working on it.
 * Otherwise plugin won't work on production servers. Packed version of this
 * file should be placed in /assets/js/front-checkout-min.js
 *
 *
 * @packer http://dean.edwards.name/packer/
 */

( function( $ ){
    $( document ).ready( function(){

        var xhr,
            checkoutContent = $( '#one-page-shopping-checkout-content' );

        /**
         * Function is different than original by possibility to work under and over WC version 2.1
         */
        function update_checkout() {

            if ( xhr ) {
                xhr.abort();
            }

            var method = [],
                shipping_methods = [];

            if ( ops_checkout_data.wc_old_version ) {
                if ( $( 'select#shipping_method' ).size() > 0 || $( 'input#shipping_method' ).size() > 0 ) {
                    method = $( '#shipping_method' ).val();
                }
                else {
                    method = $( 'input[name=shipping_method]:checked' ).val();
                }
            }
            else {
                $('select#shipping_method, input[name^=shipping_method][type=radio]:checked, input[name^=shipping_method][type=hidden]').each( function( index, input ) {
                    shipping_methods[ $(this).data( 'index' ) ] = $(this).val();
                } );
            }

            var payment_method 	= $('#order_review input[name=payment_method]:checked').val();
            var country 		= $('#billing_country').val();
            var state 			= $('#billing_state').val();
            var postcode 		= $('input#billing_postcode').val();
            var city	 		= $('input#billing_city').val();
            var address	 		= $('input#billing_address_1').val();
            var address_2	 	= $('input#billing_address_2').val();
            var optart_security,
                optart_methods,
                optart_url;

            if ( ops_checkout_data.wc_old_version ) {
                if ( $('#shiptobilling input').is(':checked') || $('#shiptobilling input').size() == 0 ) {
                    var s_country 	= country;
                    var s_state 	= state;
                    var s_postcode 	= postcode;
                    var s_city 		= city;
                    var s_address 	= address;
                    var s_address_2	= address_2;
                } else {
                    var s_country 	= $('#shipping_country').val();
                    var s_state 	= $('#shipping_state').val();
                    var s_postcode 	= $('input#shipping_postcode').val();
                    var s_city 		= $('input#shipping_city').val();
                    var s_address 	= $('input#shipping_address_1').val();
                    var s_address_2	= $('input#shipping_address_2').val();
                }
                optart_security = woocommerce_params.update_order_review_nonce;
                optart_methods = method;
                optart_url = woocommerce_params.ajax_url;
            }
            else {
                if ( $('#ship-to-different-address input').is(':checked') || $('#ship-to-different-address input').size() == 0 ) {
                    var s_country 	= $('#shipping_country').val();
                    var s_state 	= $('#shipping_state').val();
                    var s_postcode 	= $('input#shipping_postcode').val();
                    var s_city 		= $('input#shipping_city').val();
                    var s_address 	= $('input#shipping_address_1').val();
                    var s_address_2	= $('input#shipping_address_2').val();
                } else {
                    var s_country 	= country;
                    var s_state 	= state;
                    var s_postcode 	= postcode;
                    var s_city 		= city;
                    var s_address 	= address;
                    var s_address_2	= address_2;
                }
                optart_security = ops_checkout_data.update_order_review_nonce;
                optart_methods = shipping_methods;
                optart_url = ops_checkout_data.ajax_url;
            }

            //$('#order_methods, #order_review').block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});

            var data = {
                action: 			'woocommerce_update_order_review',
                security: 			optart_security,
                shipping_method: 	optart_methods,
                payment_method:		payment_method,
                country: 			country,
                state: 				state,
                postcode: 			postcode,
                city:				city,
                address:			address,
                address_2:			address_2,
                s_country: 			s_country,
                s_state: 			s_state,
                s_postcode: 		s_postcode,
                s_city:				s_city,
                s_address:			s_address,
                s_address_2:		s_address_2,
                post_data:			$('form.checkout').serialize()
            };

            xhr = $.ajax({
                type: 'POST',
                url: optart_url,
                data: data,
                success: function( response ) {

                    // Always update the fragments
                    if ( response && response.fragments ) {
                        $.each( response.fragments, function ( key, value ) {
                            $( key ).replaceWith( value );
                            $( key ).unblock();
                        } );
                    }
                    else {
                        var markup = response.html === undefined ? response : response.html;
                        $('#order_review').html(markup);
                    }
                }
            });
        }

        // Event for updating the checkout
        $( 'body' ).bind( 'update_checkout', function() {
            update_checkout();
        });

        if ( ops_checkout_data.option_guest_checkout === 'yes' ) {

            var createAccount = $( 'div.create-account' );
            createAccount.hide();

            $( 'input#createaccount' ).change( function() {
                createAccount.hide();

                if ( $( this ).is( ':checked' ) ) {
                    createAccount.slideDown();
                }
            }).change();
        }

        // Inputs/selects which update totals instantly
        checkoutContent
        .on( 'input change', 'select#shipping_method, input[name^=shipping_method], input[name=shipping_method], #shiptobilling input, .update_totals_on_change select, #ship-to-different-address input', function(){
            $( 'body' ).trigger( 'update_checkout' );
        });

        checkoutContent.on( 'click', 'a.showlogin', function() {
            $( 'form.login' ).slideToggle();

            return false;
        });

        // click on link "Click here to enter your code"
        checkoutContent.on( 'click', 'a.showcoupon', function(){
            $('.checkout_coupon').slideToggle();
            $('#coupon_code').focus();
            return false;
        });

        /* AJAX Form Submission */

        checkoutContent.on( 'submit', 'form.checkout', function() {

            var $form = $( this );

            if ( $form.is( '.processing' ) ) {
                return false;
            }

            // Trigger a handler to let gateways manipulate the checkout if needed
            if ( $form.triggerHandler( 'checkout_place_order' ) !== false && $form.triggerHandler( 'checkout_place_order_' + $( '#order_review input[name=payment_method]:checked' ).val() ) !== false ) {

                $form.addClass( 'processing' );

                var form_data = $form.data();

/*                if ( form_data["blockUI.isBlocked"] != 1 ) {
                    $form.block({ message: null, overlayCSS: { background: '#fff url(' + ops_checkout_data.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });
                }*/

                $.ajax({
                    type:		'POST',
                    url:		ops_checkout_data.ajax_url + '?action=woocommerce_checkout',
                    data:		$form.serialize(),
                    success:	function( code ) {
                        var result = '';

                        try {
                            // Get the valid JSON only from the returned string
                            if ( code.indexOf( '<!--WC_START-->' ) >= 0 )
                                code = code.split( '<!--WC_START-->' )[1]; // Strip off before after WC_START

                            if ( code.indexOf( '<!--WC_END-->' ) >= 0 )
                                code = code.split( '<!--WC_END-->' )[0]; // Strip off anything after WC_END

                            // Parse
                            result = $.parseJSON( code );

                            if ( result.result === 'success' ) {
                                window.location = decodeURI( result.redirect );
                            } else if ( result.result === 'failure' ) {
                                throw 'Result failure';
                            } else {
                                throw 'Invalid response';
                            }
                        }

                        catch( err ) {

                            if ( result.reload === 'true' ) {
                                window.location.reload();
                                return;
                            }

                            // Remove old errors
                            $( '.woocommerce-error, .woocommerce-message' ).remove();

                            // Add new errors
                            if ( result.messages ) {
                                $form.prepend( result.messages );
                            } else {
                                $form.prepend( code );
                            }

                            // Cancel processing
                            $form.removeClass( 'processing' ).unblock();

                            // Lose focus for all fields
                            $form.find( '.input-text, select' ).blur();

                            // Scroll to top
                            $( 'html, body' ).animate({
                                scrollTop: ( $( 'form.checkout' ).offset().top - 100 )
                            }, 1000 );

                            // Trigger update in case we need a fresh nonce
                            if ( result.refresh === 'true' )
                                $( 'body' ).trigger( 'update_checkout' );

                            $( 'body' ).trigger( 'checkout_error' );
                        }
                    },
                    dataType: 'html'
                });

            }

            return false;
        });

        /* AJAX Coupon Form Submission */
        checkoutContent.on( 'submit', 'form.checkout_coupon', function() {
            var $form = $(this);

            if ( $form.is('.processing') ) return false;

            var optart_ajax_loader,
                optart_nonce,
                optart_url;

            if ( ops_checkout_data.wc_old_version ) {
                optart_ajax_loader = woocommerce_params.ajax_loader_url;
                optart_nonce = woocommerce_params.apply_coupon_nonce;
                optart_url = woocommerce_params.ajax_url;
            }
            else {
                optart_ajax_loader = ops_checkout_data.ajax_loader_url;
                optart_nonce = ops_checkout_data.apply_coupon_nonce;
                optart_url = ops_checkout_data.ajax_url;
            }

            //$form.addClass('processing').block({message: null, overlayCSS: {background: '#fff url(' + optart_ajax_loader + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});

            var data = {
                action: 			'woocommerce_apply_coupon',
                security: 			optart_nonce,
                coupon_code:		$form.find('input[name=coupon_code]').val()
            };

            $.ajax({
                type: 		'POST',
                url: 		optart_url,
                data: 		data,
                success: 	function( code ) {
                    $('.woocommerce-error, .woocommerce-message').remove();
                    $form.removeClass('processing').unblock();

                    if ( code ) {
                        $form.before( code );
                        $form.slideUp();

                        $('body').trigger('update_checkout');
                    }
                },
                dataType: 	"html"
            });
            return false;
        });

        $( '#one-page-shopping-checkout' )

            /* Payment option selection */

            .on( 'click', '.payment_methods input.input-radio', function() {
                if ( $( '.payment_methods input.input-radio' ).length > 1 ) {
                    var target_payment_box = $( 'div.payment_box.' + $( this ).attr( 'ID' ) );

                    if ( $( this ).is( ':checked' ) && ! target_payment_box.is( ':visible' ) ) {
                        $( 'div.payment_box' ).filter( ':visible' ).slideUp( 250 );

                        if ( $( this ).is( ':checked' ) ) {
                            $( 'div.payment_box.' + $( this ).attr( 'ID' ) ).slideDown( 250 );
                        }
                    }
                } else {
                    $( 'div.payment_box' ).show();
                }

                if ( $( this ).data( 'order_button_text' ) ) {
                    $( '#place_order' ).val( $( this ).data( 'order_button_text' ) );
                } else {
                    $( '#place_order' ).val( $( '#place_order' ).data( 'value' ) );
                }
            })

            // Trigger initial click
            .find( 'input[name=payment_method]:checked' ).click();

    });
})( jQuery );