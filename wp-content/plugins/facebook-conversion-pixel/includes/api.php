<?php

function fca_pc_capi_event( ) {
	
	$nonce = sanitize_text_field( $_POST['nonce'] );
	
	if( wp_verify_nonce( $nonce, 'fca_pc_capi_nonce' ) === false ){
		wp_send_json_error( 'Unauthorized, please log in and try again.' );
	}
	
	fca_pc_multi_capi_event();
	
	
	wp_send_json_success();

}
add_action( 'wp_ajax_fca_pc_capi_event', 'fca_pc_capi_event' );
add_action( 'wp_ajax_nopriv_fca_pc_capi_event', 'fca_pc_capi_event' );

function fca_pc_multi_capi_event(){
	
	$options = get_option( 'fca_pc', array() );
	$pixels = empty( $options['pixels'] ) ? array() : $options['pixels'];

	forEach( $pixels as $pixel ){

		$pixel = json_decode( stripslashes_deep( $pixel ), TRUE );
		$pixel_id = $pixel['pixel'];
		$capi_token = empty( $pixel['capi'] ) ? '' : $pixel['capi'];
		$test_code = empty( $pixel['test'] ) ? '' : $pixel['test'];
		$paused = $pixel['paused'];
			
		if( $pixel_id && !$paused && $capi_token ){
			fca_pc_fb_api_call( $pixel_id, $capi_token, $test_code );
		} 

	}
}

function fca_pc_fb_api_call( $pixel, $capi_token, $test_code ){

	$url = "https://graph.facebook.com/v11.0/$pixel/events?access_token=$capi_token";
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