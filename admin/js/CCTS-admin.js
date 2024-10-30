(function ( $ ) {
	'use strict';

	var ajax_url   = ced_tiktok_obj.ajax_url;
	var ajax_nonce = ced_tiktok_obj.ajax_nonce;

	$( document ).on(
		'click' ,
		'.ced_connector_tiktok_manual_table' ,
		function () {
			if ($( '#connector_dev' ).is( ':checked' )) {
				$( '.pre-connectForm' ).toggle();
			} else {
				$( '.pre-connectForm' ).hide();
			}
		}
	);

	$( document ).on(
		'click' ,
		'.ced_tiktok_connect_button' ,
		function () {
			$( '.ced_connector_loader' ).show();
			$.ajax(
				{
					url : ajax_url,
					data : {
						ajax_nonce : ajax_nonce,
						action : 'ced_tiktok_connect_account',
					},
					type:'POST',
					success : function ( response ) {
						$( '.ced_connector_loader' ).hide();
						response             = jQuery.parseJSON( response );
						window.location.href = response.auth_url;
					}
				}
			);
		}
	);

	$( document ).on(
		'click' ,
		'.ced_tiktok_manual_connect_button' ,
		function () {
			$( '.ced_connector_loader' ).show();
			var consumer_key    = $( document ).find( '.ced_connector_consumer_key' ).val();
			var consumer_secret = $( document ).find( '.ced_connector_consumer_secret' ).val();
			$.ajax(
				{
					url : ajax_url,
					data : {
						ajax_nonce : ajax_nonce,
						consumer_key : consumer_key,
						consumer_secret : consumer_secret,
						action : 'ced_tiktok_manual_connect_account',
					},
					type:'POST',
					success : function ( response ) {
						$( '.ced_connector_loader' ).hide();
						response = jQuery.parseJSON( response );
						if (response.status == 200) {
							window.location.href = response.auth_url;
						} else {
							jQuery( '.error-banner' ).find( '.error-text' ).text( response.message );
							jQuery( '.error-banner' ).css( "display", "flex" );
							jQuery( '.error-banner' ).show();
						}

					}
				}
			);
		}
	);

	$( document ).ready(
		function () {
			$( 'select[name="order_status"]' ).on(
				'change',
				function (e) {
					let val                   = $( this ).val();
					let allowedStatusToRemove = ['wc-refunded','wc-failed','wc-cancelled'];
					let metaFieldToRemove     = document.querySelector( 'div[id="ced_tiktok_manage_orders_metabox"]' );
					let trackingCompany       = document.querySelector( 'select[name="trackingCompany"]' );
					let trackingNumber        = document.querySelector( 'input[id="trackingNumber"]' );
					if ( allowedStatusToRemove.includes( val )) {
						if (metaFieldToRemove) {
							if (trackingCompany) {
								trackingCompany.removeAttribute( 'required' )
							}
							if (trackingNumber) {
								trackingNumber.removeAttribute( 'required' )
							}
						}
					} else {
						if (metaFieldToRemove) {
							if (trackingCompany) {
								trackingCompany.setAttribute( 'required', '' )
							}
							if (trackingNumber) {
								trackingNumber.setAttribute( 'required', '' )
							}
						}
					}
				}
			);
		}
	)

})( jQuery );
