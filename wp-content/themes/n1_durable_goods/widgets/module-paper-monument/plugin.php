<?php namespace N1_Durable_Goods;
/*
Plugin Name: Paper Monument Module
Description: This area of the homepage shows the latest four posts
from the Paper Monument category in Online Only.
Display post title (with click-through link) and author only.
In addition, admins will need the ability to edit:
Module title (e.g. "Paper Monument"; text input)
Module subtitle (�Contemporary writing on art�; text input)

Version: 1.0
Author: Durable Goods Design
Text Domain: module-paper-monument
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2013 Durable Goods Design (info@durablegoodsdesign.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// TODO: change 'Module_Paper_Monument' to the name of your plugin
class Module_Paper_Monument extends \WP_Widget {

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// TODO:	update classname and description
		// TODO:	replace 'Module_Paper_Monument' to be named more plugin specific. Other instances exist throughout the code, too.
		parent::__construct(
			'Module_Paper_Monument',
			__( 'Paper Monument Module', 'Module_Paper_Monument' ),
			array(
				'classname'		=>	'module-paper-monument',
				'description'	=>	__( 'This area of the homepage shows the latest four posts from the Paper Monument category in Online Only.',
				'Module_Paper_Monument' )
			)
		);

		// Register admin styles and scripts
		//add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		//add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		// TODO:	Here is where you manipulate your widget's values based on their input fields

		include( plugin_dir_path( __FILE__ ) . '/views/widget.php' );

		echo $after_widget;

	} // end widget

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// TODO:	Here is where you update your widget's old values with the new, incoming values
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['subtitle'] = strip_tags($new_instance['subtitle']);
		$instance['img_src'] = strip_tags($new_instance['img_src']);


		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {
		// TODO:	Store the values of the widget in their own variable
		// Display the admin form
		include( plugin_dir_path(__FILE__) . '/views/admin.php' );

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		// TODO be sure to change 'widget-name' to the name of *your* plugin
		load_plugin_textdomain( 'Module_Paper_Monument', false, plugin_dir_path( __FILE__ ) . '/lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param		boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		// TODO:	Change 'widget-name' to the name of your plugin
		wp_enqueue_style( 'module-paper-monument-admin-styles', get_stylesheet_directory_uri().'/widgets/module-paper-monument/css/admin.css');

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		// TODO:	Change 'widget-name' to the name of your plugin
		// enable Media uploads
		wp_enqueue_media();
		wp_enqueue_script( 'module-paper-monument-admin-script', get_stylesheet_directory_uri().'/widgets/module-paper-monument/js/admin.js', array('jquery') );

	} // end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		// TODO:	Change 'widget-name' to the name of your plugin
		wp_enqueue_style( 'module-paper-monument-widget-styles', get_stylesheet_directory_uri().'/widgets/module-paper-monument/css/widget.css');

	} // end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

		// TODO:	Change 'widget-name' to the name of your plugin
		wp_enqueue_script( 'module-paper-monument-script', get_stylesheet_directory_uri().'/widgets/module-paper-monument/js/widget.js', array('jquery') );

	} // end register_widget_scripts

} // end class

add_action( 'widgets_init', function(){
    register_widget("\N1_Durable_Goods\Module_Paper_Monument");
});
