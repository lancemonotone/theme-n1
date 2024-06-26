<?php namespace N1_Durable_Goods;

class Assets {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'template_admin_css' ], 10 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueLegacyScripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'setup_admin_style' ] );
        add_action( 'login_enqueue_scripts', [ $this, 'setup_admin_style' ] );

        // remove CSS variables --wp--preset--color/gradient/duotone
        remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
        remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

        // remove SVG definitions for gradient/duotone
        remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
    }

    /**
     * Enqueue scripts for pages using templates defined in
     * class ACF, which use new Vite build system.
     */
    public function enqueueScripts() {
        $css = '/css/index.css';
        $js  = '/js/index.js';

        $css_version = filemtime( THEME_BUILD_PATH . $css );
        $js_version  = filemtime( THEME_BUILD_PATH . $js );

        wp_enqueue_style( 'nplusone', THEME_BUILD_URI . $css, [], $css_version );
        wp_enqueue_script( 'nplusone', THEME_BUILD_URI . $js, [ 'jquery' ], $js_version, true );
    }


    /**
     * Enqueue admin styles for pages using templates defined in
     * class ACF, which use new Vite build system.
     */
    function template_admin_css() {
        $file     = '/assets/build-admin/css/admin.css';
        $filepath = get_template_directory() . $file;
        $version  = file_exists( $filepath ) ? filemtime( $filepath ) : false;
        wp_enqueue_style( 'template_admin_css', get_template_directory_uri() . $file, false, $version );
    }

    /**
     * OG scripts and styles from nplusone theme. Eventually, these should be
     * refactored into the new build system.
     */
    function enqueueLegacyScripts() {
        $main_version   = filemtime( get_stylesheet_directory() . '/js/main.js' ) ?? null;
        $styles_version = filemtime( get_stylesheet_directory() . '/style.css' ) ?? null;

        // scripts
        wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', [], $main_version, true );
        wp_enqueue_style( 'n1-styles', get_stylesheet_uri(), [], $styles_version );
    }

    function setup_admin_style() {
        $admin_style_version = filemtime( get_stylesheet_directory() . '/css/admin-style.css' ) ?? null;

        // scripts
        // AdRotate script load is buggy, don't know why.
        // We'll load our own versions here and hope they don't need to be updated.
        wp_enqueue_script( 'raphael-n1', get_stylesheet_directory_uri() . '/js/raphael.js', [ 'jquery' ] );
        wp_enqueue_script( 'elycharts-n1', get_stylesheet_directory_uri() . '/js/ely-charts.js', [ 'jquery', 'raphael-n1' ] );
        wp_enqueue_script( 'textatcursor-n1', get_stylesheet_directory_uri() . '/js/text-at-cursor.js' );
        wp_enqueue_script( 'clicktracker-n1', get_stylesheet_directory_uri() . '/js/jquery.clicktracker.js' );
        wp_enqueue_script( 'jshowoff-n1', get_stylesheet_directory_uri() . '/js/jquery.jshowoff.min.js' );
        wp_enqueue_script( 'uploader-hook-n1', get_stylesheet_directory_uri() . '/js/uploader-hook.js' );
        // styles
        wp_enqueue_style( 'n1-admin-style', get_stylesheet_directory_uri() . '/css/admin-style.css', [], $admin_style_version );
    }

}

new Assets();
