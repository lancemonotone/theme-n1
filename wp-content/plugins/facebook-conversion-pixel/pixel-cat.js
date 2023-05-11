/* jshint asi: true */

jQuery( document ).ready(function($) {

	(function prepare_events() {

		for( var i = 0; i < fcaPcEvents.length; i++ ) {

			var eventName = fcaPcEvents[i].event
			var parameters = fcaPcEvents[i].parameters
			var triggerType = fcaPcEvents[i].triggerType
			var trigger = fcaPcEvents[i].trigger
			var apiAction = fcaPcEvents[i].apiAction

			switch ( triggerType ) {
				case 'css':
					$( trigger ).on( 'click', { name: eventName, params: parameters, apiAction: apiAction }, function( e ){
						fca_pc_fb_api_call( e.data.apiAction, e.data.name, e.data.params )
					})
					break

				case 'hover':

					$( trigger ).on( 'mouseenter', { name: eventName, params: parameters, apiAction: apiAction, trigger: trigger }, function( e ){
						fca_pc_fb_api_call( e.data.apiAction, e.data.name, e.data.params )
						$( e.data.trigger ).off( 'mouseenter' )
					})

					break

				case 'post':
					if ( fcaPcEvents[i].hasOwnProperty( 'delay' ) && fcaPcEvents[i].hasOwnProperty( 'scroll' ) ) {
						setTimeout( function( scrollTarget, apiAction, eventName, parameters ){
							$( window ).scroll( {
								'scrollTarget': scrollTarget,
								'apiAction': apiAction,
								'eventName': eventName,
								'parameters': parameters
								}, function( e ) {
									if ( e.data.scrollTarget <= scrolled_percent() ) {
										$( window ).off( e )
										fca_pc_fb_api_call( apiAction, eventName, parameters )
									}
							}).scroll()
						}, fcaPcEvents[i].delay * 1000, fcaPcEvents[i].scroll, apiAction, eventName, parameters  )


					} else if ( fcaPcEvents[i].hasOwnProperty( 'delay' ) ) {
						setTimeout( fca_pc_fb_api_call, fcaPcEvents[i].delay * 1000, apiAction, eventName, parameters  )
					} else {
						fca_pc_fb_api_call( apiAction, eventName, parameters )
					}
					break

				case 'url':
					$( 'a' ).each(function(){
						if ( $(this).attr( 'href' ) === trigger ) {
							$(this).on( 'click', { name: eventName, params: parameters, apiAction: apiAction }, function( e ){
								fca_pc_fb_api_call( e.data.apiAction, e.data.name, e.data.params )
							})
						}
					})
					break
			}

		}
	})()

	// fb initial pageview
	fca_pc_fb_api_call( 'track', 'PageView' )

	//SEARCH INTEGRATION
	if ( typeof fcaPcSearchQuery !== 'undefined' ) {
		fca_pc_fb_api_call( 'track', 'Search', fcaPcSearchQuery )
	}
	//LANDING PAGE CAT INTEGRATION
	if ( typeof fcaPcLandingPageCatEnabled !== 'undefined' ) {

		$( '#fca-lpc-optin-button' ).click( function( e ){

			var is_consent_checked

			var has_consent_checkbox = $( '#fca-lpc-gdpr-consent' ).length === 0 ? false : true

			if ( !has_consent_checkbox ) {
				is_consent_checked = true
			} else {
				is_consent_checked = $( '#fca-lpc-gdpr-consent' ).attr( 'checked' ) === 'checked' ? true : false	
			}

			if ( $( '#fca-lpc-email-input' ).val() ) {
				var data = {
					em: $( '#fca-lpc-email-input' ).val(),
					fn: $( '#fca-lpc-name-input' ).val()
				}

				if ( is_consent_checked ) {
					fca_pc_fb_api_call( 'track', 'Lead', { 'content_name': fcaPcPost.title } )	
					return true
				}
				
			}
		})
	}

	//OPTIN CAT INTEGRATION
	if ( typeof fcaPcOptinCatEnabled !== 'undefined' ) {

		$( '.fca_eoi_form' ).submit( function( e ){
			var op_data;
			var send_api_request = true
			var first_name = $( this ).find( 'input[name="name"]' ).val()
			var email = $( this ).find( 'input[name="email"]' ).val()
			
			if ( $(this).find( 'input[name="i_agree"]' ).length ) {
				if( $(this).find( '.fca_eoi_gdpr_consent' ).attr( 'checked' ) !== 'checked' ) {
					send_api_request = false
				}
			}

			if ( first_name !== '' ) {
				op_data = {
					em: email,
					fn: first_name
				}
			} else {
				op_data = {
					em: email
				}
			}
			
			if ( email && send_api_request ) {
				fca_pc_fb_api_call( 'track', 'Lead', { 'content_name': fcaPcPost.title, 'form_id': $(this).find( '#fca_eoi_form_id' ).val() } )
			}
		})
	}

	//EPT INTEGRATION
	if ( typeof fcaPcEptEnabled !== 'undefined' ) {
		$( '.ptp-checkout-button, .ptp-button, .fca-ept-button' ).click( function( e ){

			//THIS IS HANDLED BY THE 1-CLICK INTEGRATION, DONT FIRE THIS EVENT
			if (  $(this).attr( 'href' ).indexOf( '?edd_action=add_to_cart&download_id' ) !== -1 && fcaPcPost.edd_enabled ) {
				return
			}
			if (  $(this).attr( 'href' ).indexOf( 'add-to-cart=' ) !== -1 && fcaPcPost.woo_enabled ) {
				return
			}

			fca_pc_fb_api_call( 'track', 'InitiateCheckout', {
				'content_name': fcaPcPost.title,
				'pricing_table_id': get_ept_table_id( $(this) ),
				'plan_name' : get_ept_plan_name( $(this) ),
				'price' : get_ept_price( $(this) )
			})
		})
	}

	//QUIZ CAT INTEGRATION
	if ( typeof fcaPcQuizCatEnabled !== 'undefined' ) {

		$( '.fca_qc_start_button' ).click( function( e ){
			var id = parseInt ( $(this).closest( '.fca_qc_quiz' ).prop( 'id' ).replace( 'fca_qc_quiz_', '' ) )
			var name = $(this).closest( '.fca_qc_quiz' ).find( '.fca_qc_quiz_title' ).text()
			fca_pc_fb_api_call( 'trackCustom', 'QuizStart', { 'quiz_id': id, 'quiz_name': name } )
			return true
		})

		$( '.fca_qc_share_link' ).click( function( e ){
			var id = parseInt ( $(this).closest( '.fca_qc_quiz' ).prop( 'id' ).replace( 'fca_qc_quiz_', '' ) )
			var name = $(this).closest( '.fca_qc_quiz' ).find( '.fca_qc_quiz_title' ).text()
			fca_pc_fb_api_call( 'trackCustom', 'QuizShare', { 'quiz_id': id, 'quiz_name': name } )
			return true
		})

		$( '.fca_qc_submit_email_button' ).click( function( e ){

			var is_consent_checked

			var has_consent_checkbox = $( '.fca-qc-gdpr-consent' ).length === 0 ? false : true

			if ( !has_consent_checkbox ) {
				is_consent_checked = true
			} else {
				is_consent_checked = $( '.fca-qc-gdpr-consent' ).attr( 'checked' ) === 'checked' ? true : false	
			}

			if ( $(this).siblings( '#fca_qc_email_input' ).val() ) {
				var id = parseInt ( $(this).closest( '.fca_qc_quiz' ).prop( 'id' ).replace( 'fca_qc_quiz_', '' ) )
				var name = $(this).closest( '.fca_qc_quiz' ).find( '.fca_qc_quiz_title' ).text()

				var quiz_data = {
					em: $(this).siblings( '#fca_qc_email_input' ).val(),
					fn: $(this).siblings( '#fca_qc_name_input' ).val()
				}

				if ( is_consent_checked ) {
					fca_pc_fb_api_call( 'track', 'Lead', { 'quiz_id': id, 'quiz_name': name } )
					return true
				}

			}
		})

		$( '.fca_qc_score_title' ).on( 'DOMSubtreeModified', function( e ){
			if( !$(this).data( 'pixelcat' ) ) {
				$(this).data( 'pixelcat', true)
				var id = parseInt ( $(this).closest( '.fca_qc_quiz' ).prop( 'id' ).replace( 'fca_qc_quiz_', '' ) )
				var name = $(this).closest( '.fca_qc_quiz' ).find( '.fca_qc_quiz_title' ).text()
				fca_pc_fb_api_call( 'trackCustom', 'QuizCompletion', { 'quiz_id': id, 'quiz_name': name, 'quiz_result': $(this).text() } )
			}
			return true
		})
	}

	//EDD INTEGRATION
	if ( typeof fcaPcEddCheckoutCart !== 'undefined' ) {
		fca_pc_fb_api_call( 'track', 'InitiateCheckout', fcaPcEddCheckoutCart)

		//ADDPAYMENTINFO
		$( '#edd_purchase_form' ).on( 'submit', function( e ){
			fca_pc_fb_api_call( 'track', 'AddPaymentInfo', fcaPcEddCheckoutCart )
			return true
		})
	}

	if ( typeof fcaPcEddProduct !== 'undefined' ) {
		//VIEWCONTENT
		if( fcaPcPost.edd_delay ) {
			setTimeout( fca_pc_fb_api_call, fcaPcPost.edd_delay * 1000, 'track', 'ViewContent', fcaPcEddProduct  )
		} else {
			fca_pc_fb_api_call( 'track', 'ViewContent', fcaPcEddProduct )
		}

		//ADD TO CART
		$( '.edd-add-to-cart' ).click( function( e ){
			fca_pc_fb_api_call( 'track', 'AddToCart', fcaPcEddProduct )
		})
		//WISHLIST ( TODO )
		$( '.wl-add-to, .add_to_wishlist' ).click( function( e ){
			fca_pc_fb_api_call( 'track', 'AddToWishlist', fcaPcEddProduct )
		})
	}

	//PURCHASE
	if ( get_cookie( 'fca_pc_edd_purchase' ) ) {
		fca_pc_fb_api_call( 'track', 'Purchase', JSON.parse( decodeURIComponent ( get_cookie( 'fca_pc_edd_purchase' ).replace(/\+/g, '%20' ) ) ) )
		set_cookie( 'fca_pc_edd_purchase', '' )
	}

	//REMOVE ADVANCED MATCHING COOKIE IF APPLICABLE
	if ( get_cookie( 'fca_pc_advanced_matching' ) ) {
		set_cookie( 'fca_pc_advanced_matching', '' )
	}

	// in case ajax add to cart is active
	$( 'body' ).on( 'added_to_cart', function(){

		var ajax_data = {
			'action': 'fca_pc_woo_ajax_add_to_cart',
			'product_id': fcaPcPost.id
		}

		$.ajax({
			url: fcaPcCAPI.ajax_url,
			type: "POST",
			data: ajax_data,
			success: ( function( data ){
				fca_pc_fb_api_call( 'track', 'AddToCart', data )
			})
		})

	})


	//WOO INTEGRATION
	if ( get_cookie( 'fca_pc_woo_add_to_cart' ) ) {
		fca_pc_fb_api_call( 'track', 'AddToCart', JSON.parse( decodeURIComponent ( get_cookie( 'fca_pc_woo_add_to_cart' ).replace(/\+/g, '%20' ) ) ) )
		set_cookie( 'fca_pc_woo_add_to_cart', '' )
	}

	if ( typeof fcaPcWooCheckoutCart !== 'undefined' ) {
		fca_pc_fb_api_call( 'track', 'InitiateCheckout', fcaPcWooCheckoutCart)

		$( 'form.checkout' ).on( 'checkout_place_order', function( e ){
			fca_pc_fb_api_call( 'track', 'AddPaymentInfo', fcaPcWooCheckoutCart )
			return true
		})
	}

	if ( typeof fcaPcWooPurchase !== 'undefined' ) {
		fca_pc_fb_api_call( 'track', 'Purchase', fcaPcWooPurchase)
	}

	if ( typeof fcaPcWooProduct !== 'undefined' ) {
		if( fcaPcPost.woo_delay ) {
			setTimeout( fca_pc_fb_api_call, fcaPcPost.woo_delay * 1000, 'track', 'ViewContent', fcaPcWooProduct  )
		} else {
			fca_pc_fb_api_call( 'track', 'ViewContent', fcaPcWooProduct )
		}

		//WISHLIST
		$( '.wl-add-to, .add_to_wishlist' ).click( function( e ){
			fca_pc_fb_api_call( 'track', 'AddToWishlist', fcaPcWooProduct )
		})
	}

	//VIDEO EVENTS INTEGRATION

	if ( typeof fcaPcVideos !== 'undefined' ) {

		fcaPcVideos.forEach(function (video) {
			video.on( 'pixel_event', function( name, action, params ) {
				fca_pc_fb_api_call( name, action, params )
			} )
		})
	}

	if ( fcaPcCAPI.debug ) {
		console.log ( 'pixel cat events:' )
		console.log ( fcaPcEvents )
		console.log ( 'pixel cat post:' )
		console.log ( fcaPcPost )
	}

	function fca_pc_generate_id() {
		return 'xxxxxxxxxx'.replace(/[x]/g, function( c ) {
			return Math.floor(Math.random() * 16).toString( 16 )
		})
	}
	
	function fca_pc_set_cookie(cname, cvalue, exdays) {

		var d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		var expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";

	}

	function fca_pc_get_cookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split( ';' );
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ' ) {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

	function fca_pc_check_cookie() {
		var user = fca_pc_get_cookie("pixelcat_id");
		if (user == "") {
			user = fca_pc_generate_id();
			if (user != "" && user != null) {
				fca_pc_set_cookie("pixelcat_id", user, 365);
			}
		}
		return user
	}

	function fca_pc_fb_conversions_api( action, params, eventID ){

		var currentTime = new Date($.now()).toUTCString()
		var GMT_time = new Date(currentTime).valueOf() / 1000
		var url = window.location.origin + window.location.pathname

		var ajax_data = {
			'action': 'fca_pc_capi_event',
			'event_name': action,
			'event_time': GMT_time,
			'event_id': eventID,
			'external_id': fca_pc_check_cookie(),
			'client_user_agent': navigator.userAgent,
			'event_source_url': url,
			'custom_data': params,
			'nonce': fcaPcCAPI.nonce
		}

		$.ajax({
			url: fcaPcCAPI.ajax_url,
			type: "POST",
			data: ajax_data
		})

	}

	function fca_pc_capi_enabled(){

		var active_pixels = fcaPcCAPI.pixels
		for( i=0; i<active_pixels.length; i++ ){
			var pixel = JSON.parse( active_pixels[i] )
			if( pixel.type === 'Conversions API' && !pixel.paused ){
				return true
			}
		}
		return false
	}

	function fca_pc_fb_api_call( name, action, params ) {

		if( $.isFunction( fbq ) ){

			var eventID = fca_pc_generate_id()
			var externalID = fca_pc_check_cookie()
			var event_params = params ? add_auto_event_params( params ) : null

			// always send the pixel event
			fbq( name, action, event_params, { event_id: eventID, external_id: externalID } )

			// Check if Conversions API is enabled in any of the active pixels
			if( fca_pc_capi_enabled() ){
				fca_pc_fb_conversions_api( action, event_params, eventID )
			}

		}

	}

	function set_cookie( name, value ) {
		document.cookie = name + "=" + value + ";path=/"
	}

	function get_cookie( name ) {
		var value = "; " + document.cookie
		var parts = value.split( "; " + name + "=" )

		if ( parts.length === 2 ) {
			return parts.pop().split(";").shift()
		} else {
			return false
		}
	}

	function get_url_param( parameterName ) {
		var	tmp = []
		var items = location.search.substr(1).split( '&' )

		for ( var k = 0; k<items.length; k++ ) {
			tmp = items[k].split( '=' )
			if ( tmp[0] === parameterName ){
				return decodeURIComponent( tmp[1] ).replace( /\+/g, ' ' )
			}
		}
		return null
	}

	function add_auto_event_params( parameters ) {

		for ( var prop in parameters ) {
			//IGNORE ARRAYS
			if ( typeof( parameters[prop] ) === 'string' ) {
				parameters[prop] = parameters[prop].replace( '{post_id}', fcaPcPost.id )
					 .replace( '{post_title}', fcaPcPost.title )
					 .replace( '{post_type}', fcaPcPost.type )
					 .replace( '{post_category}', fcaPcPost.categories.join( ', ' ) )
			}
		}

		if ( fcaPcPost.utm_support ) {
			parameters = add_utm_params( parameters )
		}

		if ( fcaPcPost.user_parameters ) {
			parameters = add_user_params( parameters )
		}

		return parameters

	}

	function add_user_params( parameters ) {
		var user_params = [
			'referrer',
			'language',
			'logged_in',
			'post_tag',
			'post_category',
		]

		for ( var k = 0; k<user_params.length; k++ ) {
			if ( fcaPcUserParams[user_params[k]] ) {
				parameters[user_params[k]] = fcaPcUserParams[user_params[k]]
			}
		}

		return parameters

	}

	function scrolled_percent() {
		var top = $( window ).scrollTop()
		var height = $( document ).height() - $( window ).height()
		if ( height == 0 ) {
			return 100
		}
		return 100 * ( top / height )
	}

	function add_utm_params( parameters ) {
		var utm_params = [
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'utm_term',
			'utm_content',
		]

		for ( var j = 0; j<utm_params.length; j++ ) {
			if ( get_url_param( utm_params[j] ) !== null ) {
				parameters[utm_params[j]] = get_url_param( utm_params[j] )
			}
		}

		return parameters
	}

	function get_ept_table_id ( $clicked ) {
		var $table = []

		// EPT Legacy
		if ( $clicked.closest( '.ptp-pricing-table' ).length ) {
			$table = $clicked.closest( '.ptp-pricing-table' )
		} else if ( $clicked.closest( '.pricing_container' ).length ) {
			$table = $clicked.closest( '.pricing_container' )
		} else if ( $clicked.closest( '.ptp-stylish-pricingtable' ).length ) {
			$table = $clicked.closest( '.ptp-stylish-pricingtable' )
		} else if ( $clicked.closest( '.ptp-design4-pricingtable' ).length ) {
			$table = $clicked.closest( '.ptp-design4-pricingtable' )
		} else if ( $clicked.closest( '.ptp-dg5-pricing-table' ).length ) {
			$table = $clicked.closest( '.ptp-dg5-pricing-table' )
		} else if ( $clicked.closest( '.cd-pricing-container' ).length ) {
			$table = $clicked.closest( '.cd-pricing-container' )
		} else if ( $clicked.closest( '.ptp-comparison1-pricingtable' ).length ) {
			$table = $clicked.closest( '.ptp-comparison1-pricingtable' )
		} else if ( $clicked.closest( '.ptp-comparison2-pricingtable' ).length ) {
			$table = $clicked.closest( '.ptp-comparison2-pricingtable' )
		} else if ( $clicked.closest( '.ptp-comparison3-pricingtable' ).length ) {
			$table = $clicked.closest( '.ptp-comparison3-pricingtable' )
		// EPT 3+
		} else if ( $clicked.closest( '.fca-ept-main' ).length ) {
			$table = $clicked.closest( '.fca-ept-main' )
			return $table.attr( 'id' ).replace( 'fca-ept-table-','' )
		}

		if ( $table.length ) {
			return $table.attr( 'id' ).replace( 'ptp-','' )
		}

		return ''

	}
	function get_ept_plan_name( $clicked ) {
		var $name = []

		if ( $clicked.closest( '.ptp-col' ).find( '.ptp-plan' ).length ) {
			$name = $clicked.closest( '.ptp-col' ).find( '.ptp-plan' )
		} else if ( $clicked.closest( '.pricing_item' ).find( '.name' ).length ) {
			$name = $clicked.closest( '.pricing_item' ).find( '.name' )
		} else if ( $clicked.closest( '.ptp-stylish-column' ).find( '.title' ).length ) {
			$name = $clicked.closest( '.ptp-stylish-column' ).find( '.title' )
		} else if ( $clicked.closest( '.ptp-design4-col' ).find( '.ptp-design4-title' ).length ) {
			$name = $clicked.closest( '.ptp-design4-col' ).find( '.ptp-design4-title' ).eq(0)
		} else if ( $clicked.closest( '.ptp-dg5-col' ).find( '.ptp-dg5-plan' ).length ) {
			$name = $clicked.closest( '.ptp-dg5-col' ).find( '.ptp-dg5-plan' )
		} else if ( $clicked.closest( '.ptp-dg6-col' ).find( '.ptp-dg6-pricing-header h2' ).length ) {
			$name = $clicked.closest( '.ptp-dg6-col' ).find( '.ptp-dg6-pricing-header h2' )
		} else if ( $clicked.closest( '.ptp-dg7-col' ).find( '.ptp-dg7-pricing-header h2' ).length ) {
			$name = $clicked.closest( '.ptp-dg7-col' ).find( '.ptp-dg7-pricing-header h2' )
		} else if ( $clicked.closest( '.ptp-price-table' ).find( '.ptp-plan-title h2' ).length ) {
			$name = $clicked.closest( '.ptp-price-table' ).find( '.ptp-plan-title h2' )
		} else if ( $clicked.closest( '.ptp-cp2-price-table' ).find( '.ptp-cp2-plan-title h2' ).length ) {
			$name = $clicked.closest( '.ptp-cp2-price-table' ).find( '.ptp-cp2-plan-title h2' )
		} else if ( $clicked.closest( '.ptp-cp3-price-table' ).find( '.ptp-cp3-plan-title h2' ).length ) {
			$name = $clicked.closest( '.ptp-cp3-price-table' ).find( '.ptp-cp3-plan-title h2' )
		// EPT 3+
		} else if( $clicked.closest( '.fca-ept-column' ).find( '.fca-ept-plan' ).length ) {
			$name = $clicked.closest( '.fca-ept-column' ).find( '.fca-ept-plan' )
		}

		if ( $name.length ) {
			return $name.text()
		}

		return ''
	}

	function get_ept_price( $clicked ) {
		var $price = []

		if ( $clicked.closest( '.ptp-col' ).find( '.ptp-price' ).length ) {
			$price = $clicked.closest( '.ptp-col' ).find( '.ptp-price' )
		} else if ( $clicked.closest( '.pricing_item' ).find( '.price' ).length ) {
			$price = $clicked.closest( '.pricing_item' ).find( '.price' )
		} else if ( $clicked.closest( '.ptp-stylish-column' ).find( '.price' ).length ) {
			$price = $clicked.closest( '.ptp-stylish-column' ).find( '.price' )
		} else if ( $clicked.closest( '.ptp-design4-col' ).find( '.ptp-design4-price' ).length ) {
			$price = $clicked.closest( '.ptp-design4-col' ).find( '.ptp-design4-price' )
		} else if ( $clicked.closest( '.ptp-dg5-col' ).find( '.ptp-dg5-price' ).length ) {
			$price = $clicked.closest( '.ptp-dg5-col' ).find( '.ptp-dg5-price' )
		} else if ( $clicked.closest( '.ptp-dg6-col' ).find( '.ptp-dg6-price' ).length ) {
			$price = $clicked.closest( '.ptp-dg6-col' ).find( '.ptp-dg6-price' )
		} else if ( $clicked.closest( '.ptp-dg7-col' ).find( '.ptp-dg7-price' ).length ) {
			$price = $clicked.closest( '.ptp-dg7-col' ).find( '.ptp-dg7-price' )
		} else if ( $clicked.closest( '.ptp-price-table' ).find( '.cp1-ptp-price' ).length ) {
			$price = $clicked.closest( '.ptp-price-table' ).find( '.cp1-ptp-price' )
		} else if ( $clicked.closest( '.ptp-cp2-price-table' ).find( '.ptp-cp2-row-id-0' ).length ) {
			$price = $clicked.closest( '.ptp-cp2-price-table' ).find( '.ptp-cp2-row-id-0' )
		} else if ( $clicked.closest( '.ptp-cp3-price-table' ).find( '.cp3-ptp-price' ).length ) {
			$price = $clicked.closest( '.ptp-cp3-price-table' ).find( '.cp3-ptp-price' )
		// EPT 3+
		} else if( $clicked.closest( '.fca-ept-column' ).find( '.fca-ept-price' ).length ) {
			$price = $clicked.closest( '.fca-ept-column' ).find( '.fca-ept-price' )
		}

		if ( $price.length ) {
			return Number( $price.text().replace(/[^0-9\.]+/g,"") )
		}

		return ''

	}
})
