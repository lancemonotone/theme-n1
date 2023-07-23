<?php namespace N1_Durable_Goods;
/*
Plugin Name: Bookstore Module
Description: A custom post type or widget-like module featuring promotions from the Bookstore, which is hosted offsite (Shopify). Admins will need to be able to edit the following fields:
Module title (e.g. "The Bookstore"; text input)
Module subtitle ("New and notable books from n+1"; text input)
Module dek ("Two Reasons to Get Excited About School"; text input)
Up to two slots for book promotion, including:
Book cover (image upload)
Book promotional text (WYSIWYG)
Link to Shopify bookstore

Version: 1.0
Author: Durable Goods Design
Text Domain: module-bookstore
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

// TODO: change 'Module_Bookstore' to the name of your plugin
class Module_Bookstore extends \WP_Widget {

    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct() {

        // load plugin text domain
        add_action('init', array($this, 'widget_textdomain'));

        // Hooks fired when the Widget is activated and deactivated
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // TODO:	update classname and description
        // TODO:	replace 'module_bookstore' to be named more plugin specific. Other instances exist throughout the code, too.
        parent::__construct(
            'module_bookstore',
            __('Bookstore Module', 'module_bookstore'),
            array(
                'classname' => 'module-bookstore',
                'description' => __('Features promotions from the Bookstore, which is hosted offsite. Be sure to enter the Shopify URL in Site Settings.',
                    'module_bookstore')
            ));

        // Register custom post types
        // add_action('init', array($this, 'register_cpt'));

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
     * @param array    args        The array of form elements
     * @param array    instance    The current instance of the widget
     */
    public function widget($args, $instance) {

        echo $args['before_widget'];

        // TODO:	Here is where you manipulate your widget's values based on their input fields

        include(plugin_dir_path(__FILE__) . '/views/widget.php');

        echo $args['after_widget'];

    } // end widget

    /**
     * Processes the widget's options to be saved.
     *
     * @param array    new_instance    The new instance of values to be generated via the update.
     * @param array    old_instance    The previous instance of values before the update.
     */
    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        // TODO:	Here is where you update your widget's old values with the new, incoming values
        $instance['always_latest'] = strip_tags($new_instance['always_latest']);
        $instance['bookstore_module'] = strip_tags($new_instance['bookstore_module']);

        return $instance;

    } // end widget

    /**
     * Generates the administration form for the widget.
     *
     * @param array    instance    The array of keys and values for the widget.
     */
    public function form($instance) {
        // TODO:	Store the values of the widget in their own variable
        // Display the admin form
        include(plugin_dir_path(__FILE__) . '/views/admin.php');

    } // end form

    /*--------------------------------------------------*/
    /* Public Functions
    /*--------------------------------------------------*/

    /**
     * Loads the Widget's text domain for localization and translation.
     */
    public function widget_textdomain() {

        // TODO be sure to change 'widget-name' to the name of *your* plugin
        load_plugin_textdomain('module_bookstore', false, plugin_dir_path(__FILE__) . '/lang/');

    } // end widget_textdomain

    /**
     * Fired when the plugin is activated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
     */
    public function activate($network_wide) {
        // TODO define activation functionality here
    } // end activate

    /**
     * Fired when the plugin is deactivated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
     */
    public function deactivate($network_wide) {
        // TODO define deactivation functionality here
    } // end deactivate

    /**
     * Registers and enqueues admin-specific styles.
     */
    public function register_admin_styles() {

        // TODO:	Change 'widget-name' to the name of your plugin
        wp_enqueue_style('module-bookstore-admin-styles', get_stylesheet_directory_uri() . '/widgets/module-bookstore/css/admin.css');

    } // end register_admin_styles

    /**
     * Registers and enqueues admin-specific JavaScript.
     */
    public function register_admin_scripts() {

        // TODO:	Change 'widget-name' to the name of your plugin
        wp_enqueue_script('module-bookstore-admin-script', get_stylesheet_directory_uri() . '/widgets/module-bookstore/js/admin.js', array('jquery'));

    } // end register_admin_scripts

    /**
     * Registers and enqueues widget-specific styles.
     */
    public function register_widget_styles() {

        // TODO:	Change 'widget-name' to the name of your plugin
        wp_enqueue_style('module-bookstore-widget-styles', get_stylesheet_directory_uri() . '/widgets/module-bookstore/css/widget.css');

    } // end register_widget_styles

    /**
     * Registers and enqueues widget-specific scripts.
     */
    public function register_widget_scripts() {

        // TODO:	Change 'widget-name' to the name of your plugin
        wp_enqueue_script('module-bookstore-script', get_stylesheet_directory_uri() . '/widgets/module-bookstore/js/widget.js', array('jquery'));

    } // end register_widget_scripts

    /**
     * Register Custom Post type.
     *
     */
    public function register_cpt() {
        register_post_type('module_bookstore', array(
            'label' => __('Bookstore Modules'),
            'description' => 'Manage Bookstore modules.',
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'module_bookstore', 'with_front' => '1'),
            'query_var' => true,
            'exclude_from_search' => true,
            'menu_position' => '5',
            'supports' => array('title'),
            'labels' => array(
                'name' => 'Bookstore Modules',
                'singular_name' => 'Bookstore Module',
                'menu_name' => 'Bookstore Modules',
                'add_new' => 'Add Bookstore Module',
                'add_new_item' => 'Add New Bookstore Module',
                'edit' => 'Edit',
                'edit_item' => 'Edit Bookstore Module',
                'new_item' => 'New Bookstore Module',
                'view' => 'View Bookstore Module',
                'view_item' => 'View Bookstore Module',
                'search_items' => 'Search Bookstore Modules',
                'not_found' => 'No Bookstore Modules Found',
                'not_found_in_trash' => 'No Bookstore Modules Found in Trash',
                'parent' => 'Parent Bookstore Module',
            )));
    }

} // end class

add_action('widgets_init', function () {
    register_widget("\N1_Durable_Goods\Module_Bookstore");
});
