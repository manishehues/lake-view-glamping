;( function ( $ ) {
    try {
        var stripe = Stripe( hotel_booking_stripe_params.publish_key );
    } catch( error ) {
        console.log( error );
        return;
    }

    var learnpress_addon_stripe = {

        init: function() {
            window.addEventListener( 'hashchange', learnpress_addon_stripe.onHashChange );
        },

        notice: function( $message ) {
            $( '#hotel-booking-payment' ).find( 'p.hotel-booking-message.stripe' ).remove();
            $( '#hotel-booking-payment' ).prepend( '<p class="hotel-booking-message stripe message message-error" style="color:#a94442">' + $message + '</p>' );
        },

        onHashChange: function() {
            var partials = window.location.hash.match( /^#?confirm-(pi|si)-([^:]+):(.+)$/ );

            if ( ! partials || 4 > partials.length ) {
                return;
            }

            var type               = partials[1];
            var intentClientSecret = partials[2];
            var redirectURL        = decodeURIComponent(partials[3]);

            // Cleanup the URL
            window.location.hash = '';
            learnpress_addon_stripe.openIntentModal( intentClientSecret, redirectURL, false, 'si' === type );
        },
        
        openIntentModal: function( intentClientSecret, redirectURL, alwaysRedirect, isSetupIntent ) {
            var buttonCheckout = $( '#hb-payment-form button.hb_button' ),
                formCheckout = buttonCheckout.closest( 'form' );
                
            stripe[ isSetupIntent ? 'confirmCardSetup' : 'confirmCardPayment' ]( intentClientSecret )
                .then( function( response ) {
                    if ( response.error ) {
                        //learnpress_addon_stripe.notice( hotel_booking_stripe_params.error_verify );
                        throw response.error;
                    }

                    var intent = response[ isSetupIntent ? 'setupIntent' : 'paymentIntent' ];
                    if ( 'requires_capture' !== intent.status && 'succeeded' !== intent.status ) {
                        //learnpress_addon_stripe.notice( hotel_booking_stripe_params.error_verify );
                       // buttonCheckout.html( lpCheckoutSettings.i18n_place_order );
                        buttonCheckout.prop( 'disabled', false );
                        return;
                    }
                    
                    buttonCheckout.html( hotel_booking_stripe_params.button_verify );

                    $.get( redirectURL, function( data ) {
                        if ( data.result !== 'success' ) {
                            if ( data.message ) {
                                learnpress_addon_stripe.notice( data.message );
                            } else {
                                learnpress_addon_stripe.notice( 'Hotel Booking Stripe Js error.' );
                            }
                        }

                        if ( data.redirect ) {
                            window.location = data.redirect;
                        }

                        //buttonCheckout.html( lpCheckoutSettings.i18n_place_order );
                        buttonCheckout.prop( 'disabled', false );
                    } );
                } )
                .catch( function( error ) {
                    $( document.body ).trigger( 'stripeError', { error: error } );
                    
                    learnpress_addon_stripe.notice( hotel_booking_stripe_params.error_verify );

                    // Report back to the server.
                    buttonCheckout.html( hotel_booking_stripe_params.button_verify );

                    formCheckout.addClass( 'lp-stripe_loading' );

                    $.get( redirectURL, function( data ) {
                        if ( data.result !== 'success' ) {
                            if ( data.message ) {
                                learnpress_addon_stripe.notice( data.message );
                            } else {
                                learnpress_addon_stripe.notice( 'Hotel Booking Stripe Js error.' );
                            }
                        }

                        if ( data.redirect ) {
                            window.location = data.redirect;
                        }

                        //buttonCheckout.html( lpCheckoutSettings.i18n_place_order );
                        buttonCheckout.prop( 'disabled', false );
                    } );
                } );
        },

    };

    $( document ).ready( function () {
        learnpress_addon_stripe.init();
    } );
} )( jQuery );