<?php
/**
 * JSON REST API N1 Issue Gallery
 *
 * @package JSON_Rest_API_N1_Issue_Gallery
 *
 * @author Durable Goods Design
 * @version 1.0.0
 */
/*
Plugin Name: JSON REST API N1 Issue Gallery
Plugin URI: http://durablegoodsdesign.com
Description: JSON REST API N1 Issue Gallery - Requires JSON REST API plugin
Version: 1.0.0
Author: Durable Goods Design
Author URI: http://durablegoodsdesign.com
License: GPL3

Copyright Durable Goods Design

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if(!defined('JSON_API_VERSION')) return new WP_Error('json_rest_api_n1_issue_gallery', 'JSON REST API plugin not active');

// Include constants file
require_once( dirname( __FILE__ ) . '/lib/constants.php' );
// Uncomment if this plugin includes a widget.
require_once dirname( __FILE__ ) . '/widget.php';

class JSON_Rest_API_N1_Issue_Gallery {
	var $debug = true;
	var $namespace = "json-rest-api-n1-issue-gallery";
	var $id = "json_rest_api_n1_issue_gallery";
	var $friendly_name = "JSON REST API N1 Issue Gallery";
	var $version = "1.0.0";

	// Default plugin options
	/*var $defaults = array(
		'option_1' => "foobar"
	);*/

	/**
	 * Instantiation construction
	 *
	 * @uses add_action()
	 * @uses JSON_Rest_API_N1_Issue_Gallery::wp_register_scripts()
	 * @uses JSON_Rest_API_N1_Issue_Gallery::wp_register_styles()
	 */
	function __construct() {
		// Name of the option_value to store plugin options in
		$this->option_name = '_' . $this->namespace . '--options';

		// Load all library files used by this plugin
		$libs = glob( JSON_REST_API_N1_ISSUE_GALLERY_DIRNAME . '/lib/*.php' );
		foreach ( $libs as $lib ) {
			require_once( $lib );
		}

		/**
		 * Make this plugin available for translation.
		 * Translations can be added to the /languages/ directory.
		 */
		load_theme_textdomain( $this->namespace, JSON_REST_API_N1_ISSUE_GALLERY_DIRNAME . '/languages' );

		// Add all action, filter and shortcode hooks
		$this->_add_hooks();
	}

	/**
	 * Add in various hooks
	 *
	 * Place all add_action, add_filter, add_shortcode hook-ins here
	 */
	private function _add_hooks() {
		// Options page for configuration
		//add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		// Route requests for form processing
		add_action( 'init', array( &$this, 'route' ) );

		// Add a settings link next to the "Deactivate" link on the plugin listing page
		// add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );

		// Register all JavaScripts for this plugin
		add_action( 'init', array( &$this, 'wp_register_scripts' ), 1 );
		// Register all Stylesheets for this plugin
		add_action( 'init', array( &$this, 'wp_register_styles' ), 1 );
	}

	/**
	 * Process update page form submissions
	 *
	 * @uses JSON_Rest_API_N1_Issue_Gallery::sanitize()
	 * @uses wp_redirect()
	 * @uses wp_verify_nonce()
	 */
	private function _admin_options_update() {
		// Verify submission for processing using wp_nonce
		if ( wp_verify_nonce( $_REQUEST['_wpnonce'], "{$this->namespace}-update-options" ) ) {
			$data = array();
			/**
			 * Loop through each POSTed value and sanitize it to protect against malicious code. Please
			 * note that rich text (or full HTML fields) should not be processed by this function and
			 * dealt with directly.
			 */
			foreach ( $_POST['data'] as $key => $val ) {
				$data[ $key ] = $this->_sanitize( $val );
			}

			/**
			 * Place your options processing and storage code here
			 */

			// Update the options value with the data submitted
			update_option( $this->option_name, $data );

			// Redirect back to the options page with the message flag to show the saved message
			wp_safe_redirect( $_REQUEST['_wp_http_referer'] . '&message=1' );
			exit;
		}
	}

	/**
	 * Sanitize data
	 *
	 * @param mixed $str The data to be sanitized
	 *
	 * @uses wp_kses()
	 *
	 * @return mixed The sanitized version of the data
	 */
	public function _sanitize( $str ) {
		if ( ! function_exists( 'wp_kses' ) ) {
			require_once( ABSPATH . 'wp-includes/kses.php' );
		}
		global $allowedposttags;
		global $allowedprotocols;

		if ( is_string( $str ) ) {
			$str = wp_kses( $str, $allowedposttags, $allowedprotocols );
		} elseif ( is_array( $str ) ) {
			$arr = array();
			foreach ( (array) $str as $key => $val ) {
				$arr[ $key ] = $this->_sanitize( $val );
			}
			$str = $arr;
		}

		return $str;
	}

	/**
	 * Hook into register_activation_hook action
	 *
	 * Put code here that needs to happen when your plugin is first activated (database
	 * creation, permalink additions, etc.)
	 */
	static function activate() {
		// Do activation actions
	}

	/**
	 * Define the admin menu options for this plugin
	 *
	 * @uses add_action()
	 * @uses add_options_page()
	 */
	function admin_menu() {
		$page_hook = add_options_page( $this->friendly_name, $this->friendly_name, 'administrator', $this->namespace, array(
			&$this,
			'admin_options_page'
		) );

		// Add print scripts and styles action based off the option page hook
		add_action( 'admin_print_scripts-' . $page_hook, array( &$this, 'admin_print_scripts' ) );
		add_action( 'admin_print_styles-' . $page_hook, array( &$this, 'admin_print_styles' ) );
	}


	/**
	 * The admin section options page rendering method
	 *
	 * @uses current_user_can()
	 * @uses wp_die()
	 */
	function admin_options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page' );
		}

		$page_title = $this->friendly_name . ' Options';
		$namespace  = $this->namespace;

		include( JSON_REST_API_N1_ISSUE_GALLERY_DIRNAME . "/views/view-options.php" );
	}

	/**
	 * Load JavaScript for the admin options page
	 *
	 * @uses wp_enqueue_script()
	 */
	function admin_print_scripts() {
		wp_enqueue_script( "{$this->namespace}-admin" );
	}

	/**
	 * Load Stylesheet for the admin options page
	 *
	 * @uses wp_enqueue_style()
	 */
	function admin_print_styles() {
		wp_enqueue_style( "{$this->namespace}-admin" );
	}

	/**
	 * Hook into register_deactivation_hook action
	 *
	 * Put code here that needs to happen when your plugin is deactivated
	 */
	static function deactivate() {
		// Do deactivation actions
	}

	/**
	 * Retrieve the stored plugin option or the default if no user specified value is defined
	 *
	 * @param string $option_name The name of the TrialAccount option you wish to retrieve
	 *
	 * @uses get_option()
	 *
	 * @return mixed Returns the option value or false(boolean) if the option is not found
	 */
	function get_option( $option_name ) {
		// Load option values if they haven't been loaded already
		if ( ! isset( $this->options ) || empty( $this->options ) ) {
			$this->options = get_option( $this->option_name, $this->defaults );
		}

		if ( isset( $this->options[ $option_name ] ) ) {
			return $this->options[ $option_name ];    // Return user's specified option value
		} elseif ( isset( $this->defaults[ $option_name ] ) ) {
			return $this->defaults[ $option_name ];   // Return default option value
		}

		return false;
	}

	/**
	 * Initialization function to hook into the WordPress init action
	 *
	 * Instantiates the class on a global variable and sets the class, actions
	 * etc. up for use.
	 */
	static function instance() {
		global $JSON_Rest_API_N1_Issue_Gallery;

		// Only instantiate the Class if it hasn't been already
		if ( ! isset( $JSON_Rest_API_N1_Issue_Gallery ) ) {
			$JSON_Rest_API_N1_Issue_Gallery = new JSON_Rest_API_N1_Issue_Gallery();
		}
	}

	/**
	 * Hook into plugin_action_links filter
	 *
	 * Adds a "Settings" link next to the "Deactivate" link in the plugin listing page
	 * when the plugin is active.
	 *
	 * @param object $links An array of the links to show, this will be the modified variable
	 * @param string $file The name of the file being processed in the filter
	 */
	function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( JSON_REST_API_N1_ISSUE_GALLERY_DIRNAME . '/' . basename( __FILE__ ) ) ) {
			$old_links = $links;
			$new_links = array(
				"settings" => '<a href="options-general.php?page=' . $this->namespace . '">' . __( 'Settings' ) . '</a>'
			);
			$links     = array_merge( $new_links, $old_links );
		}

		return $links;
	}

	/**
	 * Route the user based off of environment conditions
	 *
	 * This function will handling routing of form submissions to the appropriate
	 * form processor.
	 *
	 * @uses JSON_Rest_API_N1_Issue_Gallery::_admin_options_update()
	 */
	function route() {
		$uri = $_SERVER['REQUEST_URI'];
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https' : 'http';
		$hostname = $_SERVER['HTTP_HOST'];
		$url = "{$protocol}://{$hostname}{$uri}";
		$is_post = (bool) ( strtoupper( $_SERVER['REQUEST_METHOD'] ) == "POST" );

		// Check if a nonce was passed in the request
		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];

			// Handle POST requests
			if ( $is_post ) {
				if ( wp_verify_nonce( $nonce, "{$this->namespace}-update-options" ) ) {
					$this->_admin_options_update();
				}
			} // Handle GET requests
			else {

			}
		}
	}

	/**
	 * Register scripts used by this plugin for enqueuing elsewhere
	 *
	 * @uses wp_register_script()
	 */
	function wp_register_scripts() {
		// Admin JavaScript
		wp_register_script( "{$this->namespace}-admin", JSON_REST_API_N1_ISSUE_GALLERY_URLPATH . "/js/admin.js", array( 'jquery' ), $this->version, true );
	}

	/**
	 * Register styles used by this plugin for enqueuing elsewhere
	 *
	 * @uses wp_register_style()
	 */
	function wp_register_styles() {
		// Admin Stylesheet
		wp_register_style( "{$this->namespace}-admin", JSON_REST_API_N1_ISSUE_GALLERY_URLPATH . "/css/admin.css", array(), $this->version, 'screen' );
	}
}

if ( ! isset( $JSON_Rest_API_N1_Issue_Gallery ) ) {
	JSON_Rest_API_N1_Issue_Gallery::instance();
}

register_activation_hook( __FILE__, array( 'JSON_Rest_API_N1_Issue_Gallery', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'JSON_Rest_API_N1_Issue_Gallery', 'deactivate' ) );
