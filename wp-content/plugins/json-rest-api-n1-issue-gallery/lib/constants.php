<?php
/**
 * Constants used by this plugin
 * 
 * @package JSON_Rest_API_N1_Issue_Gallery
 * 
 * @author Durable Goods Design
 * @version 1.0.0
 * @since 1.0.0
 */

// The current version of this plugin
if( !defined( 'JSON_REST_API_N1_ISSUE_GALLERY_VERSION' ) ) define( 'JSON_REST_API_N1_ISSUE_GALLERY_VERSION', '1.0.0' );

// The directory the plugin resides in
if( !defined( 'JSON_REST_API_N1_ISSUE_GALLERY_DIRNAME' ) ) define( 'JSON_REST_API_N1_ISSUE_GALLERY_DIRNAME', dirname( dirname( __FILE__ ) ) );

// The URL path of this plugin
if( !defined( 'JSON_REST_API_N1_ISSUE_GALLERY_URLPATH' ) ) define( 'JSON_REST_API_N1_ISSUE_GALLERY_URLPATH', WP_PLUGIN_URL . "/" . plugin_basename( JSON_REST_API_N1_ISSUE_GALLERY_DIRNAME ) );

if( !defined( 'IS_AJAX_REQUEST' ) ) define( 'IS_AJAX_REQUEST', ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) );