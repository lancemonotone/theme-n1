<?php
/*
Plugin Name: Add Meta Tags
Plugin URI: http://www.g-loaded.eu/2006/01/05/add-meta-tags-wordpress-plugin/
Description: Add basic meta tags and also Opengraph, Schema.org Microdata, Twitter Cards and Dublin Core metadata to optimize your web site for better SEO.
Version: 2.9.9
Author: George Notaras
Author URI: http://www.g-loaded.eu/
License: Apache License v2
Text Domain: add-meta-tags
Domain Path: /languages/
*/

/**
 *  This file is part of the Add-Meta-Tags distribution package.
 *
 *  Add-Meta-Tags is an extension for the WordPress publishing platform.
 *
 *  Homepage:
 *  - http://wordpress.org/plugins/add-meta-tags/
 *  Documentation:
 *  - http://www.codetrax.org/projects/wp-add-meta-tags/wiki
 *  Development Web Site and Bug Tracker:
 *  - http://www.codetrax.org/projects/wp-add-meta-tags
 *  Main Source Code Repository (Mercurial):
 *  - https://bitbucket.org/gnotaras/wordpress-add-meta-tags
 *  Mirror repository (Git):
 *  - https://github.com/gnotaras/wordpress-add-meta-tags
 *  Historical plugin home:
 *  - http://www.g-loaded.eu/2006/01/05/add-meta-tags-wordpress-plugin/
 *
 *  Licensing Information
 *
 *  Copyright 2006-2013 George Notaras <gnot@g-loaded.eu>, CodeTRAX.org
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *  The NOTICE file contains additional licensing and copyright information.
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
    header( 'HTTP/1.0 403 Forbidden' );
    echo 'This file should not be accessed directly!';
    exit; // Exit if accessed directly
}


// Store plugin main file path
define( 'AMT_PLUGIN_FILE', __FILE__ );
// Store plugin directory
// NOTE: TODO: Consider using __DIR__ (requires PHP >=5.3) instead of dirname.
// See: http://stackoverflow.com/questions/2220443/whats-better-of-requiredirname-file-myparent-php-than-just-require#comment18170996_12129877
//define( 'AMT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AMT_PLUGIN_DIR', dirname(AMT_PLUGIN_FILE) . '/' );

// Import modules
require( AMT_PLUGIN_DIR . 'amt-settings.php' );
require( AMT_PLUGIN_DIR . 'amt-admin-panel.php' );
require( AMT_PLUGIN_DIR . 'amt-utils.php' );
require( AMT_PLUGIN_DIR . 'amt-template-tags.php' );
require( AMT_PLUGIN_DIR . 'amt-embed.php' );
require( AMT_PLUGIN_DIR . 'metadata/amt_basic.php' );
require( AMT_PLUGIN_DIR . 'metadata/amt_twitter_cards.php' );
require( AMT_PLUGIN_DIR . 'metadata/amt_opengraph.php' );
require( AMT_PLUGIN_DIR . 'metadata/amt_dublin_core.php' );
require( AMT_PLUGIN_DIR . 'metadata/amt_schemaorg.php' );
require( AMT_PLUGIN_DIR . 'metadata/amt_extended.php' );
// Command Line Interface via WPCLI
require( AMT_PLUGIN_DIR . 'amt-cli.php' );


/**
 * Translation Domain
 *
 * Translation files are searched in: wp-content/plugins
 */
//load_plugin_textdomain('add-meta-tags', false, dirname( plugin_basename( AMT_PLUGIN_FILE ) ) . '/languages/');
load_plugin_textdomain('add-meta-tags', false, AMT_PLUGIN_DIR . 'languages/');


/**
 * Settings Link in the ``Installed Plugins`` page
 */
function amt_plugin_actions( $links, $file ) {
    if( $file == plugin_basename( AMT_PLUGIN_FILE ) && function_exists( "admin_url" ) ) {
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=add-meta-tags-options' ) . '">' . __('Settings') . '</a>';
        // Add the settings link before other links
        array_unshift( $links, $settings_link );
    }
    return $links;
}
add_filter( 'plugin_action_links', 'amt_plugin_actions', 10, 2 );



/**
 * Replaces the text to be used in the title element, if a replacement text has been set.
 */
// function amt_custom_title_tag($title, $separator) {
function amt_custom_title_tag($title) {

    if ( is_feed() || is_search() || is_404() ) {
        return $title;
    }

    // Get the options
    $options = get_option('add_meta_tags_opts');
    // Get current post object
    $post = get_queried_object();

    $processed_title = amt_get_title_for_title_element($options, $post);
    if ( ! empty($processed_title) ) {
        $processed_title = str_replace('%title%', $title, $processed_title);
        return esc_attr($processed_title);
    }

    // WordPress adds multipage information if a custom title is not set.
    return $title;
}
// add_filter('wp_title', 'amt_custom_title_tag', 1000, 2);
// Reverting back to the one argument version of the fitlering function.
add_filter('wp_title', 'amt_custom_title_tag', 1000, 1);


/**
 * Sets the correct lang attribute of the html element of the page,
 * according to the content's locale.
 */
function amt_set_html_lang_attribute( $lang ) {
    //var_dump($lang);
    $options = get_option('add_meta_tags_opts');
    if ( ! is_array($options) ) {
        return $lang;
    } elseif ( ! array_key_exists( 'manage_html_lang_attribute', $options) ) {
        return $lang;
    } elseif ( $options['manage_html_lang_attribute'] == '0' ) {
        return $lang;
    }
    // Set the html lang attribute according to the locale
    $locale = '';
    if ( is_singular() ) {
        $post = get_queried_object();
        $locale = str_replace( '_', '-', amt_get_language_content($options, $post) );
    } else {
        $locale = str_replace( '_', '-', amt_get_language_site($options) );
    }
    // Allow filtering
    $locale = apply_filters( 'amt_wordpress_lang', $locale );
    if ( ! empty($locale) ) {
        // Replace WordPress locale with ours. (even if it's the same)
        $lang = str_replace( get_bloginfo('language'), $locale, $lang );
    }
    return $lang;
}
add_filter( 'language_attributes', 'amt_set_html_lang_attribute' );


/**
 * Returns an array of all the generated metadata for the head area.
 */
function amt_get_metadata_head() {

    // For AMT timings
    $t = microtime(true);

    // Get the options the DB
    $options = get_option("add_meta_tags_opts");
    $do_add_metadata = true;

    $metadata_arr = array();

    // No metadata for password protected posts.
    if ( post_password_required() ) {
        return $metadata_arr;
    }

    // Robots Meta Tag content.
    $robots_content = '';

    // Check for NOINDEX,FOLLOW on archives.
    // There is no need to further process metadata as we explicitly ask search
    // engines not to index the content.
    if ( is_archive() || is_search() ) {
        if (
            ( is_search() && ($options["noindex_search_results"] == "1") )  ||          // Search results
            ( is_date() && ($options["noindex_date_archives"] == "1") )  ||             // Date and time archives
            ( is_category() && is_paged() && ($options["noindex_category_archives"] == "1") )  ||     // Category archives (except 1st page)
            ( is_tag() && is_paged() && ($options["noindex_tag_archives"] == "1") )  ||               // Tag archives (except 1st page)
            ( is_tax() && is_paged() && ($options["noindex_taxonomy_archives"] == "1") )  ||          // Custom taxonomy archives (except 1st page)
            ( is_author() && is_paged() && ($options["noindex_author_archives"] == "1") )             // Author archives (except 1st page)
        ) {
            $do_add_metadata = false;   // No need to process metadata
            $robots_content = 'NOINDEX,FOLLOW';
            // Allow filtering of the robots meta tag content.
            // Dev Note: Filtering of the robots meta tag takes place here, so as to avoid double filtering in case $do_add_metadata is true.
            $robots_content = apply_filters( 'amt_robots_data', $robots_content );
        }
    }
    // Add a robots meta tag if its content is not empty.
    if ( ! empty( $robots_content ) ) {
        $metadata_arr[] = '<meta name="robots" content="' . $robots_content . '" />';
    }


    // Get current post object
    $post = get_queried_object();
    if ( is_null( $post ) ) {
        // Allow metadata on the default front page (latest posts).
        // A post object is not available on that page, but we still need to
        // generate metadata for it. A $post object exists for the "front page"
        // and the "posts page" when static pages are used. No allow rule needed.
        if ( ! amt_is_default_front_page() ) {
            $do_add_metadata = false;
        }
    } elseif ( is_singular() ) {
        // The post type check should only take place on content pages.
        // Check if metadata should be added to this content type.
        $post_type = get_post_type( $post );
        if ( ! in_array( $post_type, amt_get_supported_post_types() ) ) {
            $do_add_metadata = false;
        }
    }

    // Add Metadata
    if ($do_add_metadata) {

        // Attachments and embedded media are collected only on content pages.
        if ( is_singular() ) {
            // Get an array containing the attachments
            $attachments = amt_get_ordered_attachments( $post );
            //var_dump($attachments);

            // Get an array containing the URLs of the embedded media
            $embedded_media = amt_get_embedded_media( $post );
            //var_dump($embedded_media);
        } else {
            $attachments = array();
            $embedded_media = array();
        }

        // Basic Meta tags
        $metadata_arr = array_merge( $metadata_arr, amt_add_basic_metadata_head( $post, $attachments, $embedded_media, $options ) );
        //var_dump(amt_add_basic_metadata());
        // Add Opengraph
        $metadata_arr = array_merge( $metadata_arr, amt_add_opengraph_metadata_head( $post, $attachments, $embedded_media, $options ) );
        // Add Twitter Cards
        $metadata_arr = array_merge( $metadata_arr, amt_add_twitter_cards_metadata_head( $post, $attachments, $embedded_media, $options ) );
        // Add Dublin Core
        $metadata_arr = array_merge( $metadata_arr, amt_add_dublin_core_metadata_head( $post, $attachments, $embedded_media, $options ) );
        // Add Google+ Author/Publisher links
        $metadata_arr = array_merge( $metadata_arr, amt_add_schemaorg_metadata_head( $post, $attachments, $embedded_media, $options ) );
        // Add JSON+LD Schema.org
        $metadata_arr = array_merge( $metadata_arr, amt_add_jsonld_schemaorg_metadata_head( $post, $attachments, $embedded_media, $options ) );
    }

    // Allow filtering of the all the generated metatags
    $metadata_arr = apply_filters( 'amt_metadata_head', $metadata_arr );

    // For AMT timings
    if ( apply_filters('amt_enable_timing', false) ) {
        $metadata_arr[] = sprintf( '<!-- Add-Meta-Tags Timings - Creation %.3f sec -->', (microtime(true) - $t) );
    }

    // Add our comment
    if ( count( $metadata_arr ) > 0 ) {
        array_unshift( $metadata_arr, "<!-- BEGIN Metadata added by Add-Meta-Tags WordPress plugin -->" );
        array_push( $metadata_arr, "<!-- END Metadata added by Add-Meta-Tags WordPress plugin -->" );
    }

    return $metadata_arr;
}


/**
 * Prints the generated metadata for the head area.
 */
function amt_add_metadata_head() {
    echo PHP_EOL . implode(PHP_EOL, amt_get_metadata_head()) . PHP_EOL . PHP_EOL;
}
add_action('wp_head', 'amt_add_metadata_head', 0);


/**
 * Returns an array of all the generated metadata for the footer area.
 */
function amt_get_metadata_footer() {

    // For AMT timings
    $t = microtime(true);

    // Get the options the DB
    $options = get_option("add_meta_tags_opts");
    $do_add_metadata = true;

    $metadata_arr = array();

    // Get current post object
    $post = get_queried_object();
    if ( is_null( $post ) ) {
        // Allow metadata on the default front page (latest posts).
        // A post object is not available on that page, but we still need to
        // generate metadata for it. A $post object exists for the "front page"
        // and the "posts page" when static pages are used. No allow rule needed.
        if ( ! amt_is_default_front_page() ) {
            $do_add_metadata = false;
        }
    } elseif ( is_singular() ) {
        // The post type check should only take place on content pages.
        // Check if metadata should be added to this content type.
        $post_type = get_post_type( $post );
        if ( ! in_array( $post_type, amt_get_supported_post_types() ) ) {
            $do_add_metadata = false;
        }
    }

    // Add Metadata
    if ($do_add_metadata) {

        // Attachments and embedded media are collected only on content pages.
        if ( is_singular() ) {
            // Get an array containing the attachments
            $attachments = amt_get_ordered_attachments( $post );
            //var_dump($attachments);

            // Get an array containing the URLs of the embedded media
            $embedded_media = amt_get_embedded_media( $post );
            //var_dump($embedded_media);
        } else {
            $attachments = array();
            $embedded_media = array();
        }

        // Add Schema.org Microdata
        $metadata_arr = array_merge( $metadata_arr, amt_add_schemaorg_metadata_footer( $post, $attachments, $embedded_media, $options ) );
    }

    // Allow filtering of all the generated metatags
    $metadata_arr = apply_filters( 'amt_metadata_footer', $metadata_arr );

    // For AMT timings
    if ( apply_filters('amt_enable_timing', false) ) {
        $metadata_arr[] = sprintf( '<!-- Add-Meta-Tags Timings - Creation %.3f sec -->', (microtime(true) - $t) );
    }

    // Add our comment
    if ( count( $metadata_arr ) > 0 ) {
        array_unshift( $metadata_arr, "<!-- BEGIN Metadata added by Add-Meta-Tags WordPress plugin -->" );
        array_push( $metadata_arr, "<!-- END Metadata added by Add-Meta-Tags WordPress plugin -->" );
    }

    return $metadata_arr;
}


/**
 * Prints the generated metadata for the footer area.
 */
function amt_add_metadata_footer() {
    echo PHP_EOL . implode(PHP_EOL, amt_get_metadata_footer()) . PHP_EOL . PHP_EOL;
}
add_action('wp_footer', 'amt_add_metadata_footer', 0);


/**
 * Review mode
 */

function amt_get_metadata_review($options) {
    //
    //  TODO: FIX THIS MESS
    //
    //return '<pre>' . amt_metatag_highlighter( htmlspecialchars( amt_add_schemaorg_metadata_content_filter('dzfgdzfdzfdszfzf'), ENT_NOQUOTES) ) . '</pre>';
    // Returns metadata review code
    //return '<pre>' . htmlentities( implode(PHP_EOL, amt_get_metadata_head()) ) . '</pre>';
    $msg = '<span style="text-decoration: underline; color: black;"><span style="font-weight: bold;">NOTE</span>: This box is displayed because <span style="font-weight: bold;">Review Mode</span> has been enabled in' . PHP_EOL . 'the Add-Meta-Tags settings. Only logged in administrators can see this box.</span>' . PHP_EOL . PHP_EOL;
    $msg_body = '<span style="text-decoration: underline; color: black;">The following metadata has been embedded in the body.</span>';
    $metadata = '<pre>';
    $metadata .= $msg . amt_metatag_highlighter( implode(PHP_EOL, amt_get_metadata_head()) ) . PHP_EOL;
    if ( $options["schemaorg_force_jsonld"] == "0" ) {
        $metadata .= PHP_EOL . $msg_body . PHP_EOL . PHP_EOL . amt_metatag_highlighter( amt_add_schemaorg_metadata_content_filter('') ) . PHP_EOL;
    }
    $metadata .= PHP_EOL . amt_metatag_highlighter( implode(PHP_EOL, amt_get_metadata_footer()) ) . PHP_EOL;
    $metadata .= '</pre>';
    return $metadata;
    //return '<pre lang="XML" line="1">' . implode(PHP_EOL, amt_get_metadata_head()) . '</pre>';
}

function amt_add_metadata_review($post_body) {

    if ( is_singular() ) {

        // Get current post object
        $post = get_queried_object();
        if ( is_null( $post ) ) {
            return $post_body;
        }

        // Check if metadata is supported on this content type.
        $post_type = get_post_type( $post );
        if ( ! in_array( $post_type, amt_get_supported_post_types() ) ) {
            return $post_body;
        }

        // Check if Review Mode is enabled
        $options = get_option("add_meta_tags_opts");
        if ( $options["review_mode"] == "0" ) {
            return $post_body;
        }

        // Only administrators can see the review box.
        if ( current_user_can( 'create_users' ) ) {
            $post_body = amt_get_metadata_review($options) . '<br /><br />' . $post_body;
        }

    }

    return $post_body;
}

add_filter('the_content', 'amt_add_metadata_review', 9999);

