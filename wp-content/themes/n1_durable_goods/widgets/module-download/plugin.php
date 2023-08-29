<?php namespace N1_Durable_Goods;
/*
Plugin Name: Offline Module
Description: Displays link and messages to download epub versions of the latest issue.

Version: 1.0
Author: Durable Goods Design
Text Domain: module-offline
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

// TODO: change 'Module_Offline' to the name of your plugin
class Module_Offline extends \WP_Widget {

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
        // TODO:	replace 'Module_Offline' to be named more plugin specific. Other instances exist throughout the code, too.
        parent::__construct(
            'Module_Offline',
            __('Offline Module', 'Module_Offline'),
            array(
                'classname' => 'module-offline',
                'description' => __('Displays link and messages to download epub versions of the latest issue.',
                    'Module_Offline')
            ));

        // Register custom post types
        //add_action( 'init', array( $this, 'register_cpt' ) );

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

    static function get_download_dir() {
        return 'dl/issues/';
    }

    static function get_current_filename() {
        return get_field('issue_pdf', N1_Magazine::get_current_issue()->ID);
    }

    static function get_current_filepath() {
        return ABSPATH . self::get_download_dir() . self::get_current_filename();
    }

    static function get_filesize() {
        return round(filesize(self::get_current_filepath()) / 1024 / 1000, 1);
    }

    static function get_download_link() {
        $current_filename = self::get_current_filename();
        $download_dir = self::get_download_dir();
        return home_url('/') . $download_dir . $current_filename;
    }

    static function has_download() {
        global $userdata;
        //$member = new MM_User($userdata->ID);
        echo Metered_Paywall::is_paywalled() ? "<!-- Yes -->" : " <!-- No -->";
        return is_user_logged_in()
            && N1_Magazine::is_current_issue()
            && file_exists(self::get_current_filepath())
            && !Metered_Paywall::is_paywalled();
    }

    /**
     * Print Download button
     *
     */
    static function print_download_button() {

        $current_filename = self::get_current_filename();
        $download_dir = self::get_download_dir();
        $current_filepath = self::get_current_filepath();
        $current_filesize = self::get_filesize();
        ?>
      <div class="jump"><a href="<?php echo self::get_download_link() ?>"
                           class="jump dl"><?php _e('Download Digital Editions') ?> <span
            class="dl-size">(<?php echo $current_filesize ?>MB)</span></a></div>
        <?php

    }

    /**
     * Outputs the content of the widget.
     *
     * @param array  args    The array of form elements
     * @param array  instance  The current instance of the widget
     */
    public function widget($args, $instance) {
        extract($args, EXTR_SKIP);

        echo $before_widget;

        // TODO:	Here is where you manipulate your widget's values based on their input fields

        include(plugin_dir_path(__FILE__) . '/views/widget.php');

        echo $after_widget;

    } // end widget

    /**
     * Processes the widget's options to be saved.
     *
     * @param array  new_instance  The new instance of values to be generated via the update.
     * @param array  old_instance  The previous instance of values before the update.
     */
    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        // TODO:	Here is where you update your widget's old values with the new, incoming values

        return $instance;

    } // end widget

    /**
     * Generates the administration form for the widget.
     *
     * @param array  instance  The array of keys and values for the widget.
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
        load_plugin_textdomain('Module_Offline', false, plugin_dir_path(__FILE__) . '/lang/');

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
        wp_enqueue_style('module-offline-admin-styles', get_stylesheet_directory_uri() . '/widgets/module-offline/css/admin.css');

    } // end register_admin_styles

    /**
     * Registers and enqueues admin-specific JavaScript.
     */
    public function register_admin_scripts() {

        // TODO:	Change 'widget-name' to the name of your plugin
        wp_enqueue_script('module-offline-admin-script', get_stylesheet_directory_uri() . '/widgets/module-offline/js/admin.js', array('jquery'));

    } // end register_admin_scripts

    /**
     * Registers and enqueues widget-specific styles.
     */
    public function register_widget_styles() {

        // TODO:	Change 'widget-name' to the name of your plugin
        wp_enqueue_style('module-offline-widget-styles', get_stylesheet_directory_uri() . '/widgets/module-offline/css/widget.css');

    } // end register_widget_styles

    /**
     * Registers and enqueues widget-specific scripts.
     */
    public function register_widget_scripts() {

        // TODO:	Change 'widget-name' to the name of your plugin
        wp_enqueue_script('module-offline-script', get_stylesheet_directory_uri() . '/widgets/module-offline/js/widget.js', array('jquery'));

    } // end register_widget_scripts

    /**
     * Register Custom Post type.
     *
     */
    public function register_cpt() {
        register_post_type('Module_Offline', array(
            'label' => __('Offline Modules'),
            'description' => 'Manage Offline Modules.',
            'public' => 'false',
            'show_ui' => 'true',
            'show_in_menu' => 'true',
            'capability_type' => 'post',
            'hierarchical' => 'false',
            'rewrite' => array('slug' => 'Module_Offline', 'with_front' => '1'),
            'query_var' => 'true',
            'exclude_from_search' => 'true',
            'menu_position' => '5',
            'supports' => array('title'),
            'labels' => array(
                'name' => 'Offline Modules',
                'singular_name' => 'Offline Module',
                'menu_name' => 'Offline Modules',
                'add_new' => 'Add Offline Module',
                'add_new_item' => 'Add New Offline Module',
                'edit' => 'Edit',
                'edit_item' => 'Edit Offline Module',
                'new_item' => 'New Offline Module',
                'view' => 'View Offline Module',
                'view_item' => 'View Offline Module',
                'search_items' => 'Search Offline Modules',
                'not_found' => 'No Offline Modules Found',
                'not_found_in_trash' => 'No Offline Modules Found in Trash',
                'parent' => 'Parent Offline Module',
            )));
    }

} // end class

add_action('widgets_init', function () {
    register_widget("\N1_Durable_Goods\Module_Offline");
});
