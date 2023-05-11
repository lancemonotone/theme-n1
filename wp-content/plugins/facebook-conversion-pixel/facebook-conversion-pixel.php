<?php
/*
	Plugin Name: Pixel Cat Lite
	Plugin URI: https://fatcatapps.com/pixel-cat
	Description: Provides an easy way to embed Facebook pixels
	Text Domain: facebook-conversion-pixel
	Domain Path: /languages
	Author: Fatcat Apps
	Author URI: https://fatcatapps.com/
	License: GPLv2
	Version: 2.6.6
*/


// BASIC SECURITY
defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );



if ( !defined( 'FCA_PC_PLUGIN_DIR' ) ) {

	//DEFINE SOME USEFUL CONSTANTS
	define( 'FCA_PC_DEBUG', FALSE );
	define( 'FCA_PC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'FCA_PC_PLUGINS_URL', plugins_url( '', __FILE__ ) );
	define( 'FCA_PC_PLUGINS_BASENAME', plugin_basename(__FILE__) );
	define( 'FCA_PC_PLUGIN_FILE', __FILE__ );
	define( 'FCA_PC_PLUGIN_PACKAGE', 'Lite' ); //DONT CHANGE THIS - BREAKS AUTO UPDATER
	define( 'FCA_PC_PLUGIN_NAME', 'Pixel Cat Premium: ' . FCA_PC_PLUGIN_PACKAGE );

	if ( FCA_PC_DEBUG ) {
		define( 'FCA_PC_PLUGIN_VER', '2.6.' . time() );
	} else {
		define( 'FCA_PC_PLUGIN_VER', '2.6.6' );
	}

	//LOAD CORE
	include_once( FCA_PC_PLUGIN_DIR . '/includes/functions.php' );
	include_once( FCA_PC_PLUGIN_DIR . '/includes/api.php' );

	$options = get_option( 'fca_pc', array() );

	//LOAD OPTIONAL MODULES
	if ( !empty( $options['woo_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events.php' );
	}
	if ( !empty( $options['woo_feed'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-feed.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-feed.php' );
	}
	if ( !empty( $options['edd_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events.php' );
	}
	if ( !empty( $options['edd_feed'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-feed.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-feed.php' );
	}
	if ( !empty( $options['quizcat_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/quizcat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/quizcat.php' );
	}
	if ( !empty( $options['optincat_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/optincat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/optincat.php' );
	}
	if ( !empty( $options['landingpagecat_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/landingpagecat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/landingpagecat.php' );
	}
	if ( !empty( $options['ept_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/ept.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/ept.php' );
	}
	if ( !empty( $options['amp_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/amp.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/amp.php' );
	}

	//LOAD MODULES
	include_once( FCA_PC_PLUGIN_DIR . '/includes/editor/editor.php' );
	if ( file_exists ( FCA_PC_PLUGIN_DIR . '/includes/editor/editor-premium.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/editor/editor-premium.php' );
	}

	if ( file_exists ( FCA_PC_PLUGIN_DIR . '/includes/licensing/licensing.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/licensing/licensing.php' );
	}

	if ( file_exists ( FCA_PC_PLUGIN_DIR . '/includes/upgrade.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/upgrade.php' );
	}
	
	//INSERT PIXEL
	function fca_pc_maybe_add_pixel() {

		$options = get_option( 'fca_pc', array() );

		$pixels = empty ( $options['pixels'] ) ? array() : $options['pixels'];

		$pixel_id = empty( $pixels ) ? '' : json_decode( stripslashes_deep( $pixels[0] ), TRUE )['pixel'];
		
		$paused = empty( $pixels ) ? false : json_decode( stripslashes_deep( $pixels[0] ), TRUE )['paused'];

		$roles = wp_get_current_user()->roles;
		$exclude = empty ( $options['exclude'] ) ? array() : str_replace( ' ', '_', $options['exclude'] );
		$roles_check_passed = 0 === count( array_intersect( array_map( 'strtolower', $roles ), array_map( 'strtolower', $exclude ) ) );

		if ( $pixel_id && $roles_check_passed ) {

			//HOOK IN OTHER INTEGRATIONS/FEATURES
			do_action( 'fca_pc_start_pixel_output', $options );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'fca_pc_client_js' );

			wp_enqueue_script( 'fca_pc_video_js', FCA_PC_PLUGINS_URL . '/video.js', array(), false, true );

			wp_localize_script( 'fca_pc_client_js', 'fcaPcEvents', fca_pc_get_active_events() );
			wp_localize_script( 'fca_pc_client_js', 'fcaPcPost', fca_pc_post_parameters() );
			wp_localize_script( 'fca_pc_client_js', 'fcaPcCAPI', array( 'pixels' => stripslashes_deep( $pixels ), 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'fca_pc_capi_nonce' ), 'debug' => FCA_PC_DEBUG ) );

			//ONLY USE DEFAULT SEARCH IF WE DIDNT USE WOO OR EDD SPECIFIC
			if ( is_search() && $options['search_integration'] == 'on' ) {
				wp_localize_script( 'fca_pc_client_js', 'fcaPcSearchQuery', array( 'search_string' => get_search_query() ) );
			}
			if ( !empty( $options['user_parameters'] ) ) {
				wp_localize_script( 'fca_pc_client_js', 'fcaPcUserParams', fca_pc_user_parameters() );
			}
			
			ob_start(); ?>

			<!-- Facebook Pixel Code -->
			<script>
			!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
			n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
			document,'script','https://connect.facebook.net/en_US/fbevents.js' );
			<?php 
			if( FCA_PC_PLUGIN_PACKAGE === 'Lite' && !$paused ) {
				echo 'fbq( "init", ' . fca_pc_clean_pixel_id( $pixel_id ) . ' );';
			} else if ( function_exists( 'fca_pc_multi_pixel_init' ) ) {
				echo fca_pc_multi_pixel_init( $options );				
			}
			?>
			</script>
			<!-- DO NOT MODIFY -->
			<!-- End Facebook Pixel Code -->

			<?php
			echo ob_get_clean();

		}
	}
	add_action( 'wp_head', 'fca_pc_maybe_add_pixel', 1 );
	
	function fca_pc_register_scripts() {
		if ( FCA_PC_DEBUG ) {
			wp_register_script( 'fca_pc_client_js', FCA_PC_PLUGINS_URL . '/pixel-cat.js', array( 'jquery' ), FCA_PC_PLUGIN_VER, true );
		} else {
			wp_register_script( 'fca_pc_client_js', FCA_PC_PLUGINS_URL . '/pixel-cat.min.js', array( 'jquery' ), FCA_PC_PLUGIN_VER, true );
		}
	}
	add_action( 'init', 'fca_pc_register_scripts' );

	function fca_pc_add_plugin_action_links( $links ) {

		$configure_url = admin_url( 'admin.php?page=fca_pc_settings_page' );
		$support_url = FCA_PC_PLUGIN_PACKAGE === 'Lite' ? 'https://wordpress.org/support/plugin/facebook-conversion-pixel' : 'https://fatcatapps.com/support';

		$new_links = array(
			'configure' => "<a href='" . esc_url( $configure_url ) . "' >" . esc_attr__( 'Configure Pixel', 'facebook-conversion-pixel' ) . '</a>',
			'support' => "<a target='_blank' href='" . esc_url( $support_url ) . "' >" . esc_attr__( 'Support', 'facebook-conversion-pixel' ) . '</a>'
		);

		$links = array_merge( $new_links, $links );

		return $links;

	}
	add_filter( 'plugin_action_links_' . FCA_PC_PLUGINS_BASENAME, 'fca_pc_add_plugin_action_links' );

	// LOCALIZATION
	function fca_pc_load_localization() {
		load_plugin_textdomain( 'pixel-cat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'fca_pc_load_localization' );

	//ADD NAG IF NO PIXEL IS SET
	function fca_pc_admin_notice() {

		$show_upgrade_info = get_option( 'fca_pc_after_upgrade_info', false );

		if ( isSet( $_GET['fca_pc_dismiss_upgrade_info'] ) && current_user_can( 'manage_options' ) ) {
			$show_upgrade_info = false;
			update_option( 'fca_pc_after_upgrade_info', false );
		}

		if ( $show_upgrade_info ) {
			$settings_url = admin_url( 'admin.php?page=fca_pc_settings_page' );
			$read_more_url = 'https://fatcatapps.com/migrate-new-facebook-pixel/';
			$dismiss_url = add_query_arg( 'fca_pc_dismiss_upgrade_info', true );

			echo '<div id="fca-pc-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
				echo '<p style="margin-top: 0;"><strong>' .  esc_attr__( "Pixel Cat: ", 'facebook-conversion-pixel' ) . '</strong>' .  esc_attr__( "Thanks for upgrading to the new Facebook Pixel. We've prepared a handy guide that explains what you'll need to do to complete setup.", 'facebook-conversion-pixel' ) . '</p>';
				echo '<p>'.  esc_attr__( "Want to revert to the old Facebook Conversion Pixel? Go to your", 'facebook-conversion-pixel' ) . " <a href='" . esc_url( $settings_url ) . "'>" . esc_attr__( "Facebook Pixel settings</a> and click 'Click here to downgrade' at the very bottom of the screen.", 'facebook-conversion-pixel' ) . '</p>';
				echo "<a style='margin-right: 16px; margin-top: 32px;' href='$read_more_url' class='button button-primary' target='_blank' >" . esc_attr__( 'Read the Facebook Pixel migration guide', 'facebook-conversion-pixel' ) . "</a> ";
				echo "<a style='margin-right: 16px; position: relative;	top: 36px;' href='" . esc_url( $dismiss_url ) . "'>" . esc_attr__( 'Close', 'facebook-conversion-pixel' ) . "</a> ";
				echo '<br style="clear:both">';
			echo '</div>';

		}

		$dismissed = get_option( 'fca_pc_no_pixel_dismissed', false );
		$options = get_option( 'fca_pc', array() );
		$screen = get_current_screen();

		if ( isSet( $_GET['fca_pc_dismiss_no_pixel'] ) && current_user_can( 'manage_options' ) ) {
			$dismissed = true;
			update_option( 'fca_pc_no_pixel_dismissed', true );
		}

		if ( !$dismissed && empty( $options['pixels'] ) && $screen->id != 'toplevel_page_fca_pc_settings_page'  ) {
			$url = admin_url( 'admin.php?page=fca_pc_settings_page' );
			$dismiss_url = add_query_arg( 'fca_pc_dismiss_no_pixel', true );

			echo '<div id="fca-pc-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
				echo '<p><strong>' . esc_attr__( "Thank you for installing Pixel Cat.", 'facebook-conversion-pixel' ) . '</strong></p>';
				echo '<p>' . esc_attr__( "It looks like you haven't configured your Facebook Pixel yet. Ready to get started?", 'facebook-conversion-pixel' ) . '</p>';
				echo "<a href='" . esc_url( $url ) . "' class='button button-primary' style='margin-top: 25px;'>" . esc_attr__( 'Set up my Pixel', 'facebook-conversion-pixel' ) . "</a> ";
				echo "<a style='position: relative; top: 30px; left: 16px;' href='" . esc_url( $dismiss_url ) . "' >" . esc_attr__( 'Dismiss', 'facebook-conversion-pixel' ) . "</a> ";
				echo '<br style="clear:both">';
			echo '</div>';
		}

		if ( FCA_PC_PLUGIN_PACKAGE === 'Lite' ){

			if ( isSet( $_GET['fca_pc_leave_review'] ) ) {

				$review_url = 'https://wordpress.org/support/plugin/facebook-conversion-pixel/reviews/';
				update_option( 'fca_pc_show_review_notice', false );
				wp_redirect($review_url);
				exit;

			}

			$show_review_option = get_option( 'fca_pc_show_review_notice', 'not-set' );

			if ( $show_review_option === 'not-set' && !wp_next_scheduled( 'fca_pc_schedule_review_notice' )  ) {

				wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_pc_schedule_review_notice' );

			}

			if ( isSet( $_GET['fca_pc_postpone_review_notice'] ) ) {

				$show_review_option = false;
				update_option( 'fca_pc_show_review_notice', $show_review_option );
				wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_pc_schedule_review_notice' );

			}

			if ( isSet( $_GET['fca_pc_forever_dismiss_notice'] ) ) {

				$show_review_option = false;
				update_option( 'fca_pc_show_review_notice', $show_review_option );

			}

			$review_url = add_query_arg( 'fca_pc_leave_review', true );
			$postpone_url = add_query_arg( 'fca_pc_postpone_review_notice', true );
			$forever_dismiss_url = add_query_arg( 'fca_pc_forever_dismiss_notice', true );

			if ( $show_review_option && $show_review_option !== 'not-set' ){

				$plugin_name = 'facebook-conversion-pixel';

				echo '<div id="fca-pc-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
					echo '<p>' . esc_attr__( "Hi! You've been using Pixel Cat for a while now, so who better to ask for a review than you? Would you please mind leaving us one? It really helps us a lot!", $plugin_name ) . '</p>';
					echo "<a href='" . esc_url( $review_url ) . "' class='button button-primary' style='margin-top: 2px;'>" . esc_attr__( 'Leave review', 'facebook-conversion-pixel' ) . "</a> ";
					echo "<a style='position: relative; top: 10px; left: 7px;' href='" . esc_url( $postpone_url ) . "' >" . esc_attr__( 'Maybe later', 'facebook-conversion-pixel' ) . "</a> ";
					echo "<a style='position: relative; top: 10px; left: 16px;' href='" . esc_url( $forever_dismiss_url ) . "' >" . esc_attr__( 'No thank you', 'facebook-conversion-pixel' ) . "</a> ";
					echo '<br style="clear:both">';
				echo '</div>';

			}
		}
	}
	add_action( 'admin_notices', 'fca_pc_admin_notice' );


	function fca_pc_enable_review_notice(){
		update_option( 'fca_pc_show_review_notice', true );
		wp_clear_scheduled_hook( 'fca_pc_schedule_review_notice' );
	}

	add_action ( 'fca_pc_schedule_review_notice', 'fca_pc_enable_review_notice' );

	//TURN OFF EDD/WOOCOMMERCE INTEGRATIONS WHEN PLUGINS ARE DISABLED
	function fca_pc_plugin_check( $plugin ) {

		$options = get_option( 'fca_pc', array() );

		if ( $plugin == 'woocommerce/woocommerce.php' ) {
			$options['woo_integration'] = false;
			$options['woo_feed'] = false;
		}
		if ( $plugin == 'easy-digital-downloads/easy-digital-downloads.php' ) {
			$options['edd_integration'] = false;
			$options['edd_feed'] = false;
		}

		update_option( 'fca_pc', $options );

	}
	add_action( 'deactivated_plugin', 'fca_pc_plugin_check', 10, 1 );

	//DEACTIVATION SURVEY
	if ( FCA_PC_PLUGIN_PACKAGE === 'Lite' ) {
		function fca_pc_admin_deactivation_survey( $hook ) {
			if ( $hook === 'plugins.php' ) {

				ob_start(); ?>

				<div id="fca-deactivate" style="position: fixed; left: 232px; top: 191px; border: 1px solid #979797; background-color: white; z-index: 9999; padding: 12px; max-width: 669px;">
					<h3 style="font-size: 14px; border-bottom: 1px solid #979797; padding-bottom: 8px; margin-top: 0;"><?php esc_attr_e( 'Sorry to see you go', 'facebook-conversion-pixel' ) ?></h3>
					<p><?php esc_attr_e( 'Hi, this is David, the creator of Pixel Cat. Thanks so much for giving my plugin a try. I’m sorry that you didn’t love it.', 'facebook-conversion-pixel' ) ?>
					</p>
					<p><?php esc_attr_e( 'I have a quick question that I hope you’ll answer to help us make Pixel Cat better: what made you deactivate?', 'facebook-conversion-pixel' ) ?>
					</p>
					<p><?php esc_attr_e( 'You can leave me a message below. I’d really appreciate it.', 'facebook-conversion-pixel' ) ?>
					</p>
					<p><b><?php esc_attr_e( 'If you\'re upgrading to Pixel Cat Premium and have questions or need help, click <a href=' . 'https://fatcatapps.com/article-categories/gen-getting-started/' . ' target="_blank">here</a></b>', 'facebook-conversion-pixel' ) ?>
					</p>
					<p><textarea style='width: 100%;' id='fca-pc-deactivate-textarea' placeholder='<?php esc_attr_e( 'What made you deactivate?', 'facebook-conversion-pixel' ) ?>'></textarea></p>

					<div style='float: right;' id='fca-deactivate-nav'>
						<button style='margin-right: 5px;' type='button' class='button button-secondary' id='fca-pc-deactivate-skip'><?php esc_attr_e( 'Skip', 'facebook-conversion-pixel' ) ?></button>
						<button type='button' class='button button-primary' id='fca-pc-deactivate-send'><?php esc_attr_e( 'Send Feedback', 'facebook-conversion-pixel' ) ?></button>
					</div>

				</div>

				<?php

				$html = ob_get_clean();

				$data = array(
					'html' => $html,
					'nonce' => wp_create_nonce( 'fca_pc_uninstall_nonce' ),
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				);

				wp_enqueue_script( 'fca_pc_deactivation_js', FCA_PC_PLUGINS_URL . '/includes/deactivation.min.js', false, FCA_PC_PLUGIN_VER, true );
				wp_localize_script( 'fca_pc_deactivation_js', 'fca_pc', $data );
			}


		}
		add_action( 'admin_enqueue_scripts', 'fca_pc_admin_deactivation_survey' );
	}
		
	function fca_pc_backward_compatibility_260( ){
		
		$options = get_option( 'fca_pc', array() );
		$updated = get_option( 'fca_pc_version' ) ? version_compare( get_option( 'fca_pc_version' ), '2.6.0', '>=' ) : 0;
		$pixels = empty( $options['pixels'] ) ? false : true;

		//if fca_pc_version doesn't exist, take old ids and create new db structure
		if( !$updated && !$pixels ){

			$old_pixels = array();
			$pixel_count = 1;
			$pixel = empty( $options['id'] ) ? '' : $options['id'];
			$pixels = empty( $options['ids'] ) ? array() : $options['ids'];

			if( $pixel ){
				$old_pixel = array(
					'pixel' => $pixel,
					'capi' => '',
					'test' => '',
					'paused' => '',
					'type' => 'Facebook Pixel',
					'ID' => 'old_pixel_' . $pixel_count
				);
				array_push( $old_pixels, json_encode( $old_pixel ) );
				$pixel_count += 1;
			}

			if( $pixels ){
				forEach( $pixels as $pixel ){
					$old_pixel = array(
						'pixel' => $pixel,
						'capi' => '',
						'test' => '',
						'paused' => '',
						'type' => 'Facebook Pixel',
						'ID' => 'old_pixel_' . $pixel_count
					);
					array_push( $old_pixels, json_encode( $old_pixel ) );
					$pixel_count += 1;
				}
			}

			// add old pixels to 'pixels' array
			$options['pixels'] = $old_pixels;
			update_option( 'fca_pc', $options );
			update_option( 'fca_pc_version', '2.6.0' );

		}
	
	}
	add_action( 'admin_init', 'fca_pc_backward_compatibility_260' );
	
}
