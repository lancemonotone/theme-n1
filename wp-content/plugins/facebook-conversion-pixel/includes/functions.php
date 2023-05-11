<?php

////////////////////////////
// FUNCTIONS
////////////////////////////

function fca_pc_post_parameters() {
	global $post;

	$post_id = empty ( $post->ID ) ? 0 : $post->ID;
	$options = get_option( 'fca_pc', array() );

	return array(
		'title' => empty ( $post->post_title ) ? '' : $post->post_title,
		'type' => empty ( $post->post_type ) ? '' : $post->post_type,
		'id' => $post_id,
		'categories' => fca_pc_get_category_names( $post_id ),
		'utm_support' => empty( $options['utm_support'] ) ? false : true,
		'user_parameters' => empty( $options['user_parameters'] ) ? false : true,
		'edd_delay' => empty( $options['edd_delay'] ) ? 0 : intVal($options['edd_delay']),
		'woo_delay' => empty( $options['woo_delay'] ) ? 0 : intVal($options['woo_delay']),
		'edd_enabled' => empty( $options['edd_integration'] ) ? false : true,
		'woo_enabled' => empty( $options['woo_integration'] ) ? false : true,
		'video_enabled' => empty( $options['video_events'] ) ? false : true,
	);
}

function fca_pc_get_active_events( $id = '' ) {

	if ( !$id ) {
		$id = get_the_id();
	}

	$options = get_option( 'fca_pc', array() );
	$events = empty( $options['events'] ) ? array() : stripslashes_deep( $options['events'] );

	$categories = wp_get_post_categories( $id );
	$tags = wp_get_post_tags( $id );

	$active_events = array();
	if ( !empty ( $events ) ) {
		forEach ( $events as $event ) {
			$event = json_decode( $event );

			if ( !empty( $event->paused ) ) {
				//skip this one
				continue;
			}

			if ( is_array( $event->trigger ) ) {
				$post_id_match = in_array( $id, $event->trigger );
				//CHECK CATEGORIES & TAGS
				$category_match = count( array_intersect( array_map( 'fca_pc_cat_id_fiter', $categories ), $event->trigger ) ) > 0;
				$tag_match = count( array_intersect( array_map( 'fca_pc_tag_id_fiter', $tags ), $event->trigger ) ) > 0;
				$front_page_match = is_front_page() && in_array( 'front', $event->trigger );
				$blog_page_match = is_home() && in_array( 'blog', $event->trigger );
				if ( in_array( 'all', $event->trigger ) OR $post_id_match OR $category_match OR $front_page_match OR $blog_page_match OR $tag_match ) {
					$active_events[] = $event;
				}
			} else {
				//CSS TRIGGERS
				$active_events[] = $event;
			}

		}
	}

	return $active_events;

}

function fca_pc_advanced_matching() {

	if ( !empty( $_COOKIE['fca_pc_advanced_matching'] ) ) {
		return stripslashes_deep( $_COOKIE['fca_pc_advanced_matching'] );
	} else if ( is_user_logged_in() ) {

		$user = wp_get_current_user();

		$fn = empty( $user->first_name ) ? $user->billing_first_name : $user->first_name;
		$ln = empty( $user->last_name ) ? $user->billing_last_name : $user->last_name;
		$user_data = array (
			'em' => esc_attr( $user->user_email ),
			'fn' 	=> esc_attr( $fn ),
			'ln' 	=> esc_attr( $ln ),
			'ph' 	=> esc_attr( $user->billing_phone ),
			'ct' 	=> esc_attr( $user->billing_city ),
			'st' 	=> esc_attr( $user->billing_state ),
			'zp' 	=> esc_attr( $user->billing_postcode )
		);

		return json_encode( array_map( 'strtolower', array_filter( $user_data ) ) );

	} else if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {

		global $wp;
		$order_id = isset( $wp->query_vars['order-received'] ) ? intval( $wp->query_vars['order-received'] ) : 0;
		$order = new WC_Order( $order_id );

		$user_data = array (
			'em' => $order->get_billing_email(),
			'fn' 	=> esc_attr( $order->get_billing_first_name() ),
			'ln' 	=> esc_attr( $order->get_billing_last_name() ),
			'ct' 	=> esc_attr( $order->get_billing_city() ),
			'st' 	=> esc_attr( $order->get_billing_state() ),
			'zp' 	=> esc_attr( $order->get_billing_postcode() ),
		);

		return json_encode( array_map( 'strtolower', array_filter( $user_data ) ) );

	}

	return false;
}

function fca_pc_encode_xml( $string ) {
	return htmlspecialchars( strip_tags ( $string ) );
}

function fca_pc_user_parameters( $id = '' ) {

	if ( !$id ) {
		$id = get_the_id();
	}

	$lang = empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? 'en-US' : $_SERVER['HTTP_ACCEPT_LANGUAGE'] ;

	return array(
		'referrer' => wp_get_raw_referer(),
		'language' => sanitize_text_field( $lang ),
		'logged_in' => is_user_logged_in() ? 'true' : 'false',
		'post_tag' => implode( ', ', array_map( 'fca_pc_tag_name_fiter', wp_get_post_tags( $id ) ) ),
		'post_category' => implode( ', ', fca_pc_get_category_names( $id ) ),
	);
}

function fca_pc_get_category_names( $id = '' ){

	if ( !$id ) {
		$id = get_the_id();
	}

	$category_names = array();
	$categories = wp_get_post_categories( $id );
	if ( is_array( $categories ) ) {
		forEach ( $categories as $cat_id ) {
			$category_names[] = get_cat_name( $cat_id );
		}
	}
	return $category_names;
}

function fca_pc_woo_product_cat_and_tags() {

	$return = array();

	$tags = get_terms( 'product_tag' );
	if ( !is_array( $tags ) ) {
		$tags = array();
	}
	$cats = get_terms( 'product_cat' );
	if ( !is_array( $cats ) ) {
		$cats = array();
	}
	forEach ( array_merge( $cats, $tags ) as $obj ) {
		$return[$obj->term_id] = $obj->name;
	}
	return $return;
}

function fca_pc_edd_product_cat_and_tags() {

	$return = array();

	$tags = get_terms( 'download_tag' );
	if ( !is_array( $tags ) ) {
		$tags = array();
	}
	$cats = get_terms( 'download_category' );
	if ( !is_array( $cats ) ) {
		$cats = array();
	}
	forEach ( array_merge( $cats, $tags ) as $obj ) {
		$return[$obj->term_id] = $obj->name;
	}
	return $return;
}

//SINGLE-SELECT
function fca_pc_select( $name, $selected = '', $options = array(), $atts = '' ) {

	$name = esc_attr( $name );
	$html = "<select name='fca_pc[$name]' $atts style='width:100%' >";

		if ( empty( $options ) && !empty( $selected ) ) {
			$selected = esc_attr( $selected );
			$html .= "<option selected='selected' value='$selected'>" . esc_attr__('Loading...', 'facebook-conversion-pixel') . "</option>";
		} else {
			forEach ( $options as $key => $text ) {
				$sel = $key === $selected ? 'selected="selected"' : '';
				$key = esc_attr( $key );
				$text = esc_attr( $text );
				$html .= "<option $sel value='$key'>$text</option>";
			}
		}

	$html .= '</select>';
	
	return $html;

}

//MULTI-SELECT
function fca_pc_select_multiple( $name, $selected = array(), $options = array(), $atts = '' ) {
	
	$name = esc_attr( $name );
	
	$html = "<select name='fca_pc[$name][]' $atts class='fca_pc_multiselect' multiple style='width:100%'  >";
	
		forEach ( $options as $key => $text ) {

			$sel = '';
			if ( in_array( $key, $selected ) ) {

				$sel = 'selected="selected"';

			}

			$key = esc_attr( $key );
			$text = esc_attr( $text );
			$html .= "<option $sel value='$key'>$text</option>";

		}
		
	$html .= '</select>';
	
	return $html;

}

//RETURN GENERIC INPUT HTML
function fca_pc_input ( $name, $placeholder = '', $value = '', $type = 'text', $atts = '' ) {

	$name = esc_attr( $name );
	$placeholder = esc_attr( $placeholder );

	$html = "<div class='fca-pc-field fca-pc-field-$type'>";

		switch ( $type ) {

			case 'checkbox':
				$checked = !empty( $value ) ? "checked='checked'" : '';

				$html .= "<div class='onoffswitch'>";
					$html .= "<input $atts style='display:none;' type='checkbox' id='fca_pc[$name]' class='onoffswitch-checkbox fca-pc-input-$type fca-pc-$name' name='fca_pc[$name]' $checked>";
					$html .= "<label class='onoffswitch-label' for='fca_pc[$name]'><span class='onoffswitch-inner' data-content-on='ON' data-content-off='OFF'><span class='onoffswitch-switch'></span></span></label>";
				$html .= "</div>";
				break;
			case 'textarea':
				$value = esc_textarea( $value );
				$html .= "<textarea $atts placeholder='$placeholder' class='fca-pc-input-$type fca-pc-$name' name='fca_pc[$name]'>$value</textarea>";
				break;
			default:
				$value = esc_attr( $value );
				$html .= "<input $atts type='$type' placeholder='$placeholder' class='fca-pc-input-$type fca-pc-$name' name='fca_pc[$name]' value='$value'>";
		}

	$html .= '</div>';

	return $html;
}

function fca_pc_sanitize_text_array( $array ) {
	if ( !is_array( $array ) ) {
		return sanitize_text_field ( $array );
	}
	foreach ( $array as $key => &$value ) {
		if ( is_array( $value ) ) {
			$value = fca_sp_sanitize_text_array( $value );
		} else {
			$value = sanitize_text_field( $value );
		}
	}

	return $array;
}


function fca_pc_get_client_ip(){

	$ip_addr = null;
	if( $_SERVER['REMOTE_ADDR'] ){
		$ip_addr = $_SERVER['REMOTE_ADDR'];
	} else if ( $_SERVER['HTTP_X_FORWARDED_FOR'] ){
		$ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if ( $_SERVER['HTTP_CLIENT_IP'] ){
		$ip_addr = $_SERVER['HTTP_CLIENT_IP'];
	}

	if( $ip_addr ){
		// check in case multiple addresses were returned
		return explode( ',', $ip_addr )[0];
	} else {
		return null;
	}

}

function fca_pc_delete_icons() {
	ob_start(); ?>
		<span class='dashicons dashicons-trash fca_delete_icon fca_delete_button' title='<?php esc_attr_e( 'Delete', 'facebook-conversion-pixel' ) ?>'></span>
		<span class='dashicons dashicons-yes fca_delete_icon fca_delete_icon_confirm' title='<?php esc_attr_e( 'Confirm Delete', 'facebook-conversion-pixel' ) ?>' style='display:none;'></span>
		<span class='dashicons dashicons-no fca_delete_icon fca_delete_icon_cancel' title='<?php esc_attr_e( 'Cancel', 'facebook-conversion-pixel' ) ?>' style='display:none;'></span>
	<?php
	return ob_get_clean();
}

function fca_pc_tooltip( $text = 'Tooltip', $icon = 'dashicons dashicons-editor-help' ) {
	return "<span class='$icon fca_pc_tooltip' title='" . htmlentities( $text ) . "'></span>";
}

function fca_pc_clean_pixel_id( $value ) {
	
	return preg_replace("/[^0-9]/", '', $value);
}

//HELPER FILTERS
function fca_pc_cat_id_fiter ( $cat_id ) {
	return 'cat' . $cat_id;
}
function fca_pc_tag_id_fiter ( $tag_id ) {
	return 'tag' . $tag_id->term_id;
}
function fca_pc_tag_name_fiter ( $tag ) {
	return $tag->name;
}
function fca_pc_term_id_fiter ( $obj ) {
	return $obj->term_id;
}
