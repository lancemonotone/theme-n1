<?php

////////////////////////////
// API ENDPOINTS
////////////////////////////

//UNINSTALL ENDPOINT
function fca_pc_uninstall_ajax() {
	
	$msg = sanitize_text_field( esc_textarea( $_REQUEST['msg'] ) );
	$nonce = sanitize_text_field( $_REQUEST['nonce'] );
	$nonceVerified = wp_verify_nonce( $nonce, 'fca_pc_uninstall_nonce') == 1;

	if ( $nonceVerified && !empty( $msg ) ) {
		
		$url =  "https://api.fatcatapps.com/api/feedback.php";
				
		$body = array(
			'product' => 'pixelcat',
			'msg' => $msg,
		);
		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
			'body' => json_encode( $body ),	
			'blocking'    => true,
			'sslverify'   => false
		);	
		
		$return = wp_remote_post( $url, $args );
		
		wp_send_json_success( $msg );

	}
	wp_send_json_error( $msg );

}
add_action( 'wp_ajax_fca_pc_uninstall', 'fca_pc_uninstall_ajax' );

function fca_pc_capi_event( ) {
	$nonce = sanitize_text_field( $_POST['nonce'] );
	if( wp_verify_nonce( $nonce, 'fca_pc_capi_nonce' ) === false ){
		wp_send_json_error( 'Unauthorized, please log in and try again.' );
	}
	$options = get_option( 'fca_pc', array() );

	if ( FCA_PC_PLUGIN_PACKAGE === 'Lite' ) {

		$pixel = json_decode( stripslashes_deep( $options['pixels'][0] ), TRUE );
		$pixel_id = $pixel['pixel'];
		$capi_token = empty( $pixel['capi'] ) ? '' : $pixel['capi'];
		$test_code = empty( $pixel['test'] ) ? '' : $pixel['test'];
		$paused = $pixel['paused'];
		
		if( $pixel_id && !$paused && $capi_token ){
			fca_pc_fb_api_call( $pixel_id, $capi_token, $test_code );
		}

	} else {
		fca_pc_multi_capi_event( $options );
	}
	
	wp_send_json_success();

}
add_action( 'wp_ajax_fca_pc_capi_event', 'fca_pc_capi_event' );
add_action( 'wp_ajax_nopriv_fca_pc_capi_event', 'fca_pc_capi_event' );


function fca_pc_fb_api_call( $pixel, $capi_token, $test_code ){

	$url = 'https://graph.facebook.com/v11.0/' . $pixel . '/events?access_token=' . $capi_token;
	$test_code = empty( $test_code ) ? ']}' : '], "test_event_code": "' . $test_code . '"}';
	$event_name = sanitize_text_field( $_POST['event_name'] );
	$event_time = sanitize_text_field( $_POST['event_time'] );
	$external_id = sanitize_text_field( $_POST['external_id'] );
	$event_id = sanitize_text_field( $_POST['event_id'] );
	$ip_addr = fca_pc_get_client_ip();
	$client_user_agent = sanitize_text_field( $_POST['client_user_agent'] );
	$event_source_url = sanitize_text_field( $_POST['event_source_url'] );
	$custom_data = sanitize_text_field( json_encode( $_POST['custom_data'] ) );

	$array_with_parameters = '{ "data": [
		{
			"event_name": "' . $event_name . '",
			"event_time": ' . $event_time . ',
			"event_id": "' .  $event_id . '",
			"event_source_url": "' . $event_source_url .'",
			"action_source": "website",
			"user_data": {
				"external_id": "' . $external_id . '",
				"client_ip_address": "' . $ip_addr . '",
				"client_user_agent": "' . $client_user_agent . '"
			},
			"custom_data": ' . $custom_data . '
		}' . $test_code;

	$data = wp_remote_request($url, array(
		'headers'   => array( 'Content-Type' => 'application/json' ),
		'body'      => $array_with_parameters,
		'method'    => 'POST',
		'data_format' => 'body'
	));

	$response = wp_remote_retrieve_body( $data );

}