<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2023 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_generate_input
 Purpose:   Generate advert code based on user input
 Since:		4.5
-------------------------------------------------------------*/
function adrotate_generate_input() {
    global $wpdb, $adrotate_config;

    if ( wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_generate_ad' ) ) {
        // Mandatory
        $id = '';
        if ( isset( $_POST[ 'adrotate_id' ] ) ) {
            $id = sanitize_key( $_POST[ 'adrotate_id' ] );
        }

        // Folder
        $folder      = $adrotate_config[ 'banner_folder' ];
        $folder_path = WP_CONTENT_DIR . "/" . $folder;
        $folder_url  = WP_CONTENT_URL . "/" . $folder;

        // Fullsize Image
        $fullsize_image = '';
        if ( isset( $_POST[ 'adrotate_fullsize_dropdown' ] ) ) {
            $fullsize_image      = $folder_url . '/' . strip_tags( trim( $_POST[ 'adrotate_fullsize_dropdown' ] ) );
            $fullsize_image_path = $folder_path . '/' . strip_tags( trim( $_POST[ 'adrotate_fullsize_dropdown' ] ) );
        } else {
            adrotate_return( 'adrotate', 500 );
        }

        // Target URL
        $targeturl = '';
        if ( isset( $_POST[ 'adrotate_targeturl' ] ) ) {
            $targeturl = strip_tags( trim( $_POST[ 'adrotate_targeturl' ] ) );
        }

        // Alt Attribute
        $alt = '';
        if ( isset( $_POST[ 'adrotate_title_attr' ] ) ) {
            $alt = strip_tags( trim( $_POST[ 'adrotate_title_attr' ] ) );
        }

        // No Follow
        $nofollow = '';
        if ( isset( $_POST[ 'adrotate_nofollow' ] ) ) {
            $nofollow = strip_tags( trim( $_POST[ 'adrotate_nofollow' ] ) );
        }

        $new_window = '';
        if ( isset( $_POST[ 'adrotate_newwindow' ] ) ) {
            $new_window = strip_tags( trim( $_POST[ 'adrotate_newwindow' ] ) );
        }

        $portability = '';
        if ( isset( $_POST[ 'adrotate_portability' ] ) ) {
            $portability = strip_tags( trim( $_POST[ 'adrotate_portability' ] ) );
        }

        // Add the small, medium, and large image URLs from the $_POST data
        // Small Image
        if ( isset( $_POST[ 'adrotate_small_dropdown' ] ) ) {
            $small_image = $folder_url . '/' . strip_tags( trim( $_POST[ 'adrotate_small_dropdown' ] ) );
        }

        // Medium Image
        $medium_image = '';
        if ( isset( $_POST[ 'adrotate_medium_dropdown' ] ) ) {
            $medium_image = $folder_url . '/' . strip_tags( trim( $_POST[ 'adrotate_medium_dropdown' ] ) );
        }

        // Large Image
        $large_image = '';
        if ( isset( $_POST[ 'adrotate_large_dropdown' ] ) ) {
            $large_image = $folder_url . '/' . strip_tags( trim( $_POST[ 'adrotate_large_dropdown' ] ) );
        }


        if ( current_user_can( 'adrotate_ad_manage' ) ) {
            if ( strlen( $portability ) == 0 ) {
                // Fullsize Image & figure out adwidth and adheight
                $adwidth       = $adheight = '';
                $fullsize_size = @getimagesize( $fullsize_image_path );
                if ( $fullsize_size ) {
                    $adwidth  = ' width="' . $fullsize_size[ 0 ] . '"';
                    $adheight = ' height="' . $fullsize_size[ 1 ] . '"';
                }

                // No Follow?
                if ( isset( $nofollow ) and strlen( $nofollow ) != 0 ) {
                    $nofollow = ' rel="nofollow"';
                } else {
                    $nofollow = '';
                }

                // Open in a new window?
                if ( isset( $new_window ) and strlen( $new_window ) != 0 ) {
                    $new_window = ' target="_blank"';
                } else {
                    $new_window = '';
                }

                // Determine image settings
                $asset = "<picture>
                             <source media=\"(min-width:1024px)\" srcset=\"$fullsize_image\">                 
                             <source media=\"(min-width:768px)\" srcset=\"$large_image\">
                             <source media=\"(min-width:440px)\" srcset=\"$medium_image\">
                             <img src=\"$small_image\"" . $adwidth . $adheight . " alt=\"$alt\" style=\"width:auto;\">
                          </picture>";


                // Generate code
                $bannercode = "<a href=\"" . $targeturl . "\"" . $new_window . " title=\"$alt\"" . $nofollow . ">" . $asset . "</a>";

                // Save the advert to the DB
                $wpdb->update( $wpdb->prefix . 'adrotate', [ 'bannercode' => $bannercode, 'imagetype' => 'dropdown', 'image' => $fullsize_image ], [ 'id' => $id ] );
            } else {
                $portability = adrotate_portable_hash( 'import', $portability );

                // Save the advert to the DB
                $wpdb->update( $wpdb->prefix . 'adrotate', [ 'title' => $portability[ 'title' ], 'bannercode' => $portability[ 'bannercode' ], 'thetime' => $portability[ 'thetime' ], 'updated' => current_time( 'timestamp' ), 'author' => $portability[ 'author' ], 'imagetype' => $portability[ 'imagetype' ], 'image' => $portability[ 'image' ], 'tracker' => $portability[ 'tracker' ], 'show_everyone' => $portability[ 'show_everyone' ], 'desktop' => $portability[ 'desktop' ], 'mobile' => $portability[ 'mobile' ], 'tablet' => $portability[ 'tablet' ], 'os_ios' => $portability[ 'os_ios' ], 'os_android' => $portability[ 'os_android' ], 'os_other' => $portability[ 'os_other' ], 'weight' => $portability[ 'weight' ], 'autodelete' => $portability[ 'autodelete' ], 'budget' => $portability[ 'budget' ], 'crate' => $portability[ 'crate' ], 'irate' => $portability[ 'irate' ], 'state_req' => $portability[ 'state_req' ], 'cities' => $portability[ 'cities' ], 'states' => $portability[ 'states' ], 'countries' => $portability[ 'countries' ] ], [ 'id' => $id ] );
            }

            adrotate_return( 'adrotate', 226, [ 'view' => 'edit', 'ad' => $id ] );
            exit;
        } else {
            adrotate_return( 'adrotate', 500 );
        }
    } else {
        adrotate_nonce_error();
        exit;
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_input
 Purpose:   Prepare input form on saving new or updated banners
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_insert_input() {
    global $wpdb, $adrotate_config;

    if ( wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_save_ad' ) ) {
        // Mandatory
        $id = $schedule_id = $author = $title = $bannercode = $active = '';
        if ( isset( $_POST[ 'adrotate_id' ] ) ) {
            $id = sanitize_key( $_POST[ 'adrotate_id' ] );
        }
        if ( isset( $_POST[ 'adrotate_schedule' ] ) ) {
            $schedule_id = sanitize_key( $_POST[ 'adrotate_schedule' ] );
        }
        if ( isset( $_POST[ 'adrotate_username' ] ) ) {
            $author = sanitize_key( $_POST[ 'adrotate_username' ] );
        }
        if ( isset( $_POST[ 'adrotate_title' ] ) ) {
            $title = sanitize_text_field( $_POST[ 'adrotate_title' ] );
        }
        if ( isset( $_POST[ 'adrotate_bannercode' ] ) ) {
            $bannercode = htmlspecialchars( trim( $_POST[ 'adrotate_bannercode' ] ), ENT_QUOTES );
        }
        $thetime = current_time( 'timestamp' );
        if ( isset( $_POST[ 'adrotate_active' ] ) ) {
            $active = strip_tags( trim( $_POST[ 'adrotate_active' ] ) );
        }

        // Schedules
        $start_date = $start_hour = $start_minute = $end_date = $end_hour = $end_minute = '';
        if ( isset( $_POST[ 'adrotate_start_date' ] ) ) {
            $start_date = sanitize_key( trim( $_POST[ 'adrotate_start_date' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_start_hour' ] ) ) {
            $start_hour = sanitize_key( trim( $_POST[ 'adrotate_start_hour' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_start_minute' ] ) ) {
            $start_minute = sanitize_key( trim( $_POST[ 'adrotate_start_minute' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_end_date' ] ) ) {
            $end_date = sanitize_key( trim( $_POST[ 'adrotate_end_date' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_end_hour' ] ) ) {
            $end_hour = sanitize_key( trim( $_POST[ 'adrotate_end_hour' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_end_minute' ] ) ) {
            $end_minute = sanitize_key( trim( $_POST[ 'adrotate_end_minute' ] ) );
        }

        $maxclicks = $maxshown = '';
        if ( isset( $_POST[ 'adrotate_maxclicks' ] ) ) {
            $maxclicks = sanitize_key( trim( $_POST[ 'adrotate_maxclicks' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_maxshown' ] ) ) {
            $maxshown = sanitize_key( trim( $_POST[ 'adrotate_maxshown' ] ) );
        }

        // Advanced options
        $image_field = $image_dropdown = $tracker = $show_everyone = '';
        $advertiser  = 0;
        if ( isset( $_POST[ 'adrotate_image' ] ) ) {
            $image_field = strip_tags( trim( $_POST[ 'adrotate_image' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_image_dropdown' ] ) ) {
            $image_dropdown = strip_tags( trim( $_POST[ 'adrotate_image_dropdown' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_tracker' ] ) ) {
            $tracker = strip_tags( trim( $_POST[ 'adrotate_tracker' ] ) );
        }

        // Misc variables
        $type   = '';
        $groups = [];
        if ( isset( $_POST[ 'groupselect' ] ) ) {
            $groups = $_POST[ 'groupselect' ];
        }
        if ( isset( $_POST[ 'adrotate_type' ] ) ) {
            $type = sanitize_key( trim( $_POST[ 'adrotate_type' ] ) );
        }


        if ( current_user_can( 'adrotate_ad_manage' ) ) {
            if ( strlen( $title ) < 1 ) {
                $title = 'Advert ' . $id;
            }
            $title = str_replace( '"', "", $title );

            // Clean up bannercode
            if ( preg_match( "/%ID%/", $bannercode ) ) {
                $bannercode = str_replace( '%ID%', '%id%', $bannercode );
            }
            if ( preg_match( "/%IMAGE%/", $bannercode ) ) {
                $bannercode = str_replace( '%IMAGE%', '%image%', $bannercode );
            }
            if ( preg_match( "/%TITLE%/", $bannercode ) ) {
                $bannercode = str_replace( '%TITLE%', '%title%', $bannercode );
            }
            if ( preg_match( "/%RANDOM%/", $bannercode ) ) {
                $bannercode = str_replace( '%RANDOM%', '%random%', $bannercode );
            }

            // Sort out start dates
            if ( strlen( $start_date ) > 0 ) {
                [ $start_day, $start_month, $start_year ] = explode( '-', $start_date ); // dd/mm/yyyy
            } else {
                $start_year = $start_month = $start_day = 0;
            }

            if ( ( $start_year > 0 and $start_month > 0 and $start_day > 0 ) and strlen( $start_hour ) == 0 ) {
                $start_hour = '00';
            }
            if ( ( $start_year > 0 and $start_month > 0 and $start_day > 0 ) and strlen( $start_minute ) == 0 ) {
                $start_minute = '00';
            }

            if ( $start_month > 0 and $start_day > 0 and $start_year > 0 ) {
                $start_date = mktime( $start_hour, $start_minute, 0, $start_month, $start_day, $start_year );
            } else {
                $start_date = 0;
            }

            // Sort out end dates
            if ( strlen( $end_date ) > 0 ) {
                [ $end_day, $end_month, $end_year ] = explode( '-', $end_date ); // dd/mm/yyyy
            } else {
                $end_year = $end_month = $end_day = 0;
            }

            if ( ( $end_year > 0 and $end_month > 0 and $end_day > 0 ) and strlen( $end_hour ) == 0 ) {
                $end_hour = '00';
            }
            if ( ( $end_year > 0 and $end_month > 0 and $end_day > 0 ) and strlen( $end_minute ) == 0 ) {
                $end_minute = '00';
            }

            if ( $end_month > 0 and $end_day > 0 and $end_year > 0 ) {
                $end_date = mktime( $end_hour, $end_minute, 0, $end_month, $end_day, $end_year );
            } else {
                $end_date = 0;
            }

            // Enddate is too early, reset to default
            if ( $end_date <= $start_date ) {
                $end_date = $start_date + 7257600;
            } // 84 days (12 weeks)

            // Sort out click and impressions restrictions
            if ( strlen( $maxclicks ) < 1 or ! is_numeric( $maxclicks ) ) {
                $maxclicks = 0;
            }
            if ( strlen( $maxshown ) < 1 or ! is_numeric( $maxshown ) ) {
                $maxshown = 0;
            }

            if ( isset( $tracker ) and strlen( $tracker ) != 0 ) {
                $tracker = 'Y';
            } else {
                $tracker = 'N';
            }

            // Determine image settings ($image_field has priority!)
            if ( strlen( $image_field ) > 1 ) {
                $imagetype = "field";
                $image     = $image_field;
            } elseif ( strlen( $image_dropdown ) > 1 ) {
                $imagetype = "dropdown";
                $image     = WP_CONTENT_URL . "/banners/" . $image_dropdown;
            } else {
                $imagetype = "";
                $image     = "";
            }

            // Save schedule for new ads or update the existing one
            if ( $type != 'empty' ) {
                $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_schedule` WHERE `id` IN (SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` != %d AND `schedule` > 0 AND `ad` = %d AND `group` = 0 AND `user` = 0);", $schedule_id, $id ) );
            }
            $wpdb->update( $wpdb->prefix . 'adrotate_schedule', [ 'starttime' => $start_date, 'stoptime' => $end_date, 'maxclicks' => $maxclicks, 'maximpressions' => $maxshown ], [ 'id' => $schedule_id ] );

            // Save the ad to the DB
            $wpdb->update( $wpdb->prefix . 'adrotate', [ 'title' => $title, 'bannercode' => $bannercode, 'updated' => $thetime, 'author' => $author, 'imagetype' => $imagetype, 'image' => $image, 'tracker' => $tracker, 'type' => $active ], [ 'id' => $id ] );

            // Fetch group records for the ad
            $groupmeta   = $wpdb->get_results( $wpdb->prepare( "SELECT `group` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `user` = 0 AND `schedule` = 0;", $id ) );
            $group_array = [];
            foreach ( $groupmeta as $meta ) {
                $group_array[] = $meta->group;
            }

            // Add new groups to this ad
            $insert = array_diff( $groups, $group_array );
            foreach ( $insert as &$value ) {
                $wpdb->insert( $wpdb->prefix . 'adrotate_linkmeta', [ 'ad' => $id, 'group' => $value, 'user' => 0, 'schedule' => 0 ] );
            }
            unset( $value );

            // Remove groups from this ad
            $delete = array_diff( $group_array, $groups );
            foreach ( $delete as &$value ) {
                $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = %d AND `user` = 0 AND `schedule` = 0;", $id, $value ) );
            }
            unset( $value );

            // Verify ad
            if ( $type == "empty" ) {
                $action = 'new';
            } else {
                $action = 'update';
            }

            if ( $active == "active" ) {
                // Verify all ads
                adrotate_prepare_evaluate_ads( false );
            }

            adrotate_return( 'adrotate', 200 );
            exit;
        } else {
            adrotate_return( 'adrotate', 500 );
        }
    } else {
        adrotate_nonce_error();
        exit;
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_group
 Purpose:   Save provided data for groups, update linkmeta where required
 Since:		0.4
-------------------------------------------------------------*/
function adrotate_insert_group() {
    global $wpdb, $adrotate_config;

    if ( wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_save_group' ) ) {
        $action = $id = $name = $modus = '';
        if ( isset( $_POST[ 'adrotate_action' ] ) ) {
            $action = sanitize_key( $_POST[ 'adrotate_action' ] );
        }
        if ( isset( $_POST[ 'adrotate_id' ] ) ) {
            $id = sanitize_key( $_POST[ 'adrotate_id' ] );
        }
        if ( isset( $_POST[ 'adrotate_groupname' ] ) ) {
            $name = sanitize_text_field( $_POST[ 'adrotate_groupname' ] );
        }
        if ( isset( $_POST[ 'adrotate_modus' ] ) ) {
            $modus = sanitize_key( trim( $_POST[ 'adrotate_modus' ] ) );
        }

        $rows = $columns = $adwidth = $adheight = $adspeed = '';
        if ( isset( $_POST[ 'adrotate_gridrows' ] ) ) {
            $rows = sanitize_key( trim( $_POST[ 'adrotate_gridrows' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_gridcolumns' ] ) ) {
            $columns = sanitize_key( trim( $_POST[ 'adrotate_gridcolumns' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_adwidth' ] ) ) {
            $adwidth = sanitize_key( trim( $_POST[ 'adrotate_adwidth' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_adheight' ] ) ) {
            $adheight = sanitize_key( trim( $_POST[ 'adrotate_adheight' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_adspeed' ] ) ) {
            $adspeed = sanitize_key( trim( $_POST[ 'adrotate_adspeed' ] ) );
        }

        $ads = $admargin = $align = '';
        if ( isset( $_POST[ 'adselect' ] ) ) {
            $ads = $_POST[ 'adselect' ];
        }
        if ( isset( $_POST[ 'adrotate_admargin' ] ) ) {
            $admargin = sanitize_key( trim( $_POST[ 'adrotate_admargin' ] ) );
        }
        if ( isset( $_POST[ 'adrotate_align' ] ) ) {
            $align = sanitize_key( trim( $_POST[ 'adrotate_align' ] ) );
        }

        $categories = $category_loc = $category_par = $pages = $page_loc = $page_par = '';
        if ( isset( $_POST[ 'adrotate_categories' ] ) ) {
            $categories = $_POST[ 'adrotate_categories' ];
        }
        if ( isset( $_POST[ 'adrotate_cat_location' ] ) ) {
            $category_loc = sanitize_key( $_POST[ 'adrotate_cat_location' ] );
        }
        if ( isset( $_POST[ 'adrotate_cat_paragraph' ] ) ) {
            $category_par = sanitize_key( $_POST[ 'adrotate_cat_paragraph' ] );
        }
        if ( isset( $_POST[ 'adrotate_pages' ] ) ) {
            $pages = $_POST[ 'adrotate_pages' ];
        }
        if ( isset( $_POST[ 'adrotate_page_location' ] ) ) {
            $page_loc = sanitize_key( $_POST[ 'adrotate_page_location' ] );
        }
        if ( isset( $_POST[ 'adrotate_page_paragraph' ] ) ) {
            $page_par = sanitize_key( $_POST[ 'adrotate_page_paragraph' ] );
        }

        $wrapper_before = $wrapper_after = '';
        if ( isset( $_POST[ 'adrotate_wrapper_before' ] ) ) {
            $wrapper_before = htmlspecialchars( trim( $_POST[ 'adrotate_wrapper_before' ] ), ENT_QUOTES );
        }
        if ( isset( $_POST[ 'adrotate_wrapper_after' ] ) ) {
            $wrapper_after = htmlspecialchars( trim( $_POST[ 'adrotate_wrapper_after' ] ), ENT_QUOTES );
        }

        if ( current_user_can( 'adrotate_group_manage' ) ) {
            if ( strlen( $name ) < 1 ) {
                $name = 'Group ' . $id;
            }
            $name = str_replace( '"', "", $name );

            if ( $modus < 0 or $modus > 2 ) {
                $modus = 0;
            }
            if ( $adspeed < 0 or $adspeed > 99999 ) {
                $adspeed = 6000;
            }
            if ( $align < 0 or $align > 3 ) {
                $align = 0;
            }

            // Sort out block shape
            if ( $rows < 1 or $rows == '' or ! is_numeric( $rows ) ) {
                $rows = 2;
            }
            if ( $columns < 1 or $columns == '' or ! is_numeric( $columns ) ) {
                $columns = 2;
            }
            if ( ( is_numeric( $adwidth ) and ( $adwidth < 1 or $adwidth > 9999 ) ) or $adwidth == '' or ( ! is_numeric( $adwidth ) and $adwidth != 'auto' ) ) {
                $adwidth = '728';
            }
            if ( ( is_numeric( $adheight ) and ( $adheight < 1 or $adheight > 9999 ) ) or $adheight == '' or ( ! is_numeric( $adheight ) and $adheight != 'auto' ) ) {
                $adheight = '90';
            }
            if ( $admargin < 0 or $admargin > 99 or $admargin == '' or ! is_numeric( $admargin ) ) {
                $admargin = 0;
            }

            // Categories
            if ( ! is_array( $categories ) ) {
                $categories = [];
            }
            $category = implode( ',', $categories );
            if ( $category_par > 0 ) {
                $category_loc = 4;
            }
            if ( $category_loc != 4 ) {
                $category_par = 0;
            }

            // Pages
            if ( ! is_array( $pages ) ) {
                $pages = [];
            }
            $page = implode( ',', $pages );
            if ( $page_par > 0 ) {
                $page_loc = 4;
            }
            if ( $page_loc != 4 ) {
                $page_par = 0;
            }

            // Fetch records for the group
            $linkmeta = $wpdb->get_results( $wpdb->prepare( "SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = %d AND `user` = 0;", $id ) );
            foreach ( $linkmeta as $meta ) {
                $meta_array[] = $meta->ad;
            }

            if ( empty( $meta_array ) ) {
                $meta_array = [];
            }
            if ( empty( $ads ) ) {
                $ads = [];
            }

            // Add new ads to this group
            $insert = array_diff( $ads, $meta_array );
            foreach ( $insert as &$value ) {
                $wpdb->insert( $wpdb->prefix . 'adrotate_linkmeta', [ 'ad' => $value, 'group' => $id, 'user' => 0 ] );
            }
            unset( $value );

            // Remove ads from this group
            $delete = array_diff( $meta_array, $ads );
            foreach ( $delete as &$value ) {
                $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = %d AND `user` = 0;", $value, $id ) );
            }
            unset( $value );

            // Update the group itself
            $wpdb->update( $wpdb->prefix . 'adrotate_groups', [ 'name' => $name, 'modus' => $modus, 'fallback' => 0, 'cat' => $category, 'cat_loc' => $category_loc, 'cat_par' => $category_par, 'page' => $page, 'page_loc' => $page_loc, 'page_par' => $page_par, 'wrapper_before' => $wrapper_before, 'wrapper_after' => $wrapper_after, 'align' => $align, 'gridrows' => $rows, 'gridcolumns' => $columns, 'admargin' => $admargin, 'adwidth' => $adwidth, 'adheight' => $adheight, 'adspeed' => $adspeed ], [ 'id' => $id ] );

            // Determine Dynamic Library requirement
            $dynamic_count = $wpdb->get_var( "SELECT COUNT(*) as `total` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1;" );
            update_option( 'adrotate_dynamic_required', $dynamic_count );

            // Generate CSS for group
            if ( $align == 0 ) { // None
                $group_align = "";
            } elseif ( $align == 1 ) { // Left
                $group_align = " float:left; clear:left;";
            } elseif ( $align == 2 ) { // Right
                $group_align = " float:right; clear:right;";
            } elseif ( $align == 3 ) { // Center
                $group_align = " margin: 0 auto;";
            }

            $output_css = "";
            if ( $modus == 0 and ( $admargin > 0 or $align > 0 ) ) { // Single ad group
                if ( $align < 3 ) {
                    $output_css .= "\t.g" . $adrotate_config[ 'adblock_disguise' ] . "-" . $id . " { margin:" . $admargin . "px; " . $group_align . " }\n";
                } else {
                    $output_css .= "\t.g" . $adrotate_config[ 'adblock_disguise' ] . "-" . $id . " { " . $group_align . " }\n";
                }
            }

            if ( $modus == 1 ) { // Dynamic group
                if ( $adwidth != 'auto' ) {
                    $width = " width:100%; max-width:" . $adwidth . "px;";
                } else {
                    $width = " width:auto;";
                }

                if ( $adheight != 'auto' ) {
                    $height = " height:100%; max-height:" . $adheight . "px;";
                } else {
                    $height = " height:auto;";
                }

                if ( $align < 3 ) {
                    $output_css .= "\t.g" . $adrotate_config[ 'adblock_disguise' ] . "-" . $id . " { margin:" . $admargin . "px; " . $width . $height . $group_align . " }\n";
                } else {
                    $output_css .= "\t.g" . $adrotate_config[ 'adblock_disguise' ] . "-" . $id . " {" . $width . $height . $group_align . " }\n";
                }

                unset( $width_sum, $width, $height_sum, $height );
            }

            if ( $modus == 2 ) { // Block group
                if ( $adwidth != 'auto' ) {
                    $width_sum  = $columns * ( $adwidth + ( $admargin * 2 ) );
                    $grid_width = "min-width:" . $admargin . "px; max-width:" . $width_sum . "px;";
                } else {
                    $grid_width = "width:auto;";
                }

                $output_css .= "\t.g" . $adrotate_config[ 'adblock_disguise' ] . "-" . $id . " { " . $grid_width . $group_align . " }\n";
                $output_css .= "\t.b" . $adrotate_config[ 'adblock_disguise' ] . "-" . $id . " { margin:" . $admargin . "px; }\n";
                unset( $width_sum, $grid_width, $height_sum, $grid_height );
            }

            $group_css        = get_option( 'adrotate_group_css' );
            $group_css[ $id ] = $output_css;
            update_option( 'adrotate_group_css', $group_css );
            // end CSS

            adrotate_return( 'adrotate-groups', 201 );
            exit;
        } else {
            adrotate_return( 'adrotate-groups', 500 );
        }
    } else {
        adrotate_nonce_error();
        exit;
    }
}


/*-------------------------------------------------------------
 Name:      adrotate_insert_media
 Purpose:   Prepare input form on saving new or updated banners
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_insert_media() {
    global $wpdb, $adrotate_config;

    if ( wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_save_media' ) ) {
        if ( current_user_can( 'adrotate_ad_manage' ) ) {
            if ( $_FILES[ "adrotate_image" ][ "size" ] > 0 and $_FILES[ "adrotate_image" ][ "size" ] <= 512000 ) {
                $file_path      = WP_CONTENT_DIR . "/" . esc_attr( $_POST[ 'adrotate_image_location' ] ) . "/";
                $file           = explode( ".", adrotate_sanitize_file_name( $_FILES[ "adrotate_image" ][ "name" ] ) );
                $file_name      = implode( '.', $file );
                $file_extension = array_pop( $file );
                $file_mimetype  = mime_content_type( $_FILES[ "adrotate_image" ][ "tmp_name" ] );

                if (
                    in_array( $file_extension, [ "jpg", "jpeg", "gif", "png", "html", "htm", "js", "svg", "zip" ] )
                    and in_array( $file_mimetype, [ "image/jpg", "image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/svg", "text/html", "text/htm", "application/x-javascript", "application/javascript", "text/javascript", "application/zip" ] )
                ) {
                    if ( $_FILES[ "adrotate_image" ][ "error" ] > 0 ) {
                        if ( $_FILES[ "adrotate_image" ][ "error" ] == 1 or $_FILES[ "adrotate_image" ][ "error" ] == 2 ) {
                            $errorcode = 511;
                        } elseif ( $_FILES[ "adrotate_image" ][ "error" ] == 3 ) {
                            $errorcode = 506;
                        } elseif ( $_FILES[ "adrotate_image" ][ "error" ] == 4 ) {
                            $errorcode = 506;
                        } elseif ( $_FILES[ "adrotate_image" ][ "error" ] == 6 or $_FILES[ "adrotate_image" ][ "error" ] == 7 ) {
                            $errorcode = 506;
                        } else {
                            $errorcode = '';
                        }
                        adrotate_return( 'adrotate-media', $errorcode ); // Other error
                    } else {
                        if ( ! move_uploaded_file( $_FILES[ "adrotate_image" ][ "tmp_name" ], $file_path . $file_name ) ) {
                            adrotate_return( 'adrotate-media', 506 ); // Upload error
                        }

                        if ( $file_mimetype == "application/zip" and $file_extension == "zip" ) {
                            require_once( ABSPATH . '/wp-admin/includes/file.php' );

                            $creds = request_filesystem_credentials( wp_nonce_url( 'admin.php?page=adrotate-media' ), '', false, $file_path, null );
                            if ( ! WP_Filesystem( $creds ) ) {
                                request_filesystem_credentials( wp_nonce_url( 'admin.php?page=adrotate-media' ), '', true, $file_path, null );
                            }

                            $unzipfile = unzip_file( $file_path . $file_name . '.' . $file_extension, $file_path . $file_name );
                            if ( is_wp_error( $unzipfile ) ) {
                                adrotate_return( 'adrotate-media', 512 ); // Can not unzip file
                            }

                            // Delete unwanted files
                            adrotate_clean_folder_contents( $file_path . $file_name );

                            // Delete the uploaded zip
                            adrotate_unlink( $file_name . '.' . $file_extension );
                        }

                        adrotate_return( 'adrotate-media', 202 ); // Success
                    }
                } else {
                    adrotate_return( 'adrotate-media', 510 ); // Filetype
                }
            } else {
                adrotate_return( 'adrotate-media', 511 ); // Size
            }
        } else {
            adrotate_return( 'adrotate-media', 500 ); // No access/permission
        }
    } else {
        adrotate_nonce_error();
        exit;
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_folder
 Purpose:   Create a folder
 Since:		5.8.6
-------------------------------------------------------------*/
function adrotate_insert_folder() {
    global $adrotate_config;

    if ( wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_save_media' ) ) {
        if ( current_user_can( 'adrotate_ad_manage' ) ) {
            $folder = ( isset( $_POST[ 'adrotate_folder' ] ) ) ? esc_attr( strip_tags( trim( $_POST[ 'adrotate_folder' ] ) ) ) : '';

            if ( strlen( $folder ) > 0 and strlen( $folder ) <= 100 ) {
                $folder = adrotate_sanitize_file_name( $folder );

                if ( wp_mkdir_p( WP_CONTENT_DIR . "/" . $adrotate_config[ 'banner_folder' ] . "/" . $folder ) ) {
                    adrotate_return( 'adrotate-media', 223 ); // Success
                } else {
                    adrotate_return( 'adrotate-media', 516 ); // error
                }
            } else {
                adrotate_return( 'adrotate-media', 515 ); // name length
            }
        } else {
            adrotate_return( 'adrotate-media', 500 ); // No access/permission
        }
    } else {
        adrotate_nonce_error();
        exit;
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_request_action
 Purpose:   Prepare action for banner or group from database
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_request_action() {
    global $wpdb, $adrotate_config;

    $banner_ids = $group_ids = '';

    if ( wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_bulk_ads_active' ) or wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_bulk_ads_disable' )
         or wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_bulk_ads_error' ) or wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_bulk_ads_queue' )
         or wp_verify_nonce( $_POST[ 'adrotate_nonce' ], 'adrotate_bulk_groups' ) ) {
        if ( isset( $_POST[ 'bannercheck' ] ) ) {
            $banner_ids = $_POST[ 'bannercheck' ];
        }
        if ( isset( $_POST[ 'disabledbannercheck' ] ) ) {
            $banner_ids = $_POST[ 'disabledbannercheck' ];
        }
        if ( isset( $_POST[ 'errorbannercheck' ] ) ) {
            $banner_ids = $_POST[ 'errorbannercheck' ];
        }
        if ( isset( $_POST[ 'groupcheck' ] ) ) {
            $group_ids = $_POST[ 'groupcheck' ];
        }
        if ( isset( $_POST[ 'adrotate_id' ] ) ) {
            $banner_ids = [ $_POST[ 'adrotate_id' ] ];
        }

        // Determine which kind of action to use
        if ( isset( $_POST[ 'adrotate_action' ] ) ) {
            // Default action call
            $actions = strip_tags( trim( $_POST[ 'adrotate_action' ] ) );
        } elseif ( isset( $_POST[ 'adrotate_disabled_action' ] ) ) {
            // Disabled ads listing call
            $actions = strip_tags( trim( $_POST[ 'adrotate_disabled_action' ] ) );
        } elseif ( isset( $_POST[ 'adrotate_error_action' ] ) ) {
            // Erroneous ads listing call
            $actions = strip_tags( trim( $_POST[ 'adrotate_error_action' ] ) );
        }
        if ( preg_match( "/-/", $actions ) ) {
            [ $action, $specific ] = explode( "-", $actions );
        } else {
            $action = $actions;
        }

        if ( $banner_ids != '' ) {
            $return = 'adrotate';
            if ( $action == 'export' ) {
                if ( current_user_can( 'adrotate_ad_manage' ) ) {
                    adrotate_export( $banner_ids );
                    $result_id = 215;
                } else {
                    adrotate_return( $return, 500 );
                }
            }
            foreach ( $banner_ids as $banner_id ) {
                if ( $action == 'deactivate' ) {
                    if ( current_user_can( 'adrotate_ad_manage' ) ) {
                        adrotate_active( $banner_id, 'deactivate' );
                        $result_id = 210;
                    } else {
                        adrotate_return( $return, 500 );
                    }
                }
                if ( $action == 'activate' ) {
                    if ( current_user_can( 'adrotate_ad_manage' ) ) {
                        adrotate_active( $banner_id, 'activate' );
                        $result_id = 211;
                    } else {
                        adrotate_return( $return, 500 );
                    }
                }
                if ( $action == 'delete' ) {
                    if ( current_user_can( 'adrotate_ad_delete' ) ) {
                        adrotate_delete( $banner_id, 'banner' );
                        $result_id = 203;
                    } else {
                        adrotate_return( $return, 500 );
                    }
                }
                if ( $action == 'reset' ) {
                    if ( current_user_can( 'adrotate_ad_delete' ) ) {
                        adrotate_reset( $banner_id );
                        $result_id = 208;
                    } else {
                        adrotate_return( $return, 500 );
                    }
                }
                if ( $action == 'renew' ) {
                    if ( current_user_can( 'adrotate_ad_manage' ) ) {
                        adrotate_renew( $banner_id, $specific );
                        $result_id = 209;
                    } else {
                        adrotate_return( $return, 500 );
                    }
                }
            }
            // Verify all ads
            adrotate_prepare_evaluate_ads( false );
        }

        if ( $group_ids != '' ) {
            $return = 'adrotate-groups';
            foreach ( $group_ids as $group_id ) {
                if ( $action == 'group_delete' ) {
                    if ( current_user_can( 'adrotate_group_delete' ) ) {
                        adrotate_delete( $group_id, 'group' );
                        $result_id = 204;
                    } else {
                        adrotate_return( $return, 500 );
                    }
                }
                if ( $action == 'group_delete_banners' ) {
                    if ( current_user_can( 'adrotate_group_delete' ) ) {
                        adrotate_delete( $group_id, 'bannergroup' );
                        $result_id = 213;
                    } else {
                        adrotate_return( $return, 500 );
                    }
                }
            }
        }

        adrotate_return( $return, $result_id );
    } else {
        adrotate_nonce_error();
        exit;
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_delete

 Purpose:   Remove banner or group from database
 Receive:   $id, $what
 Return:    -none-
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_delete( $id, $what ) {
    global $wpdb;

    if ( $id > 0 ) {
        if ( $what == 'banner' ) {
            $schedule_id = $wpdb->get_var( $wpdb->prepare( "SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0 AND `schedule` != 0;", $id ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_schedule` WHERE `id` = %d;", $schedule_id ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $id ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d;", $id ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `ad` = %d;", $id ) );
        } elseif ( $what == 'group' ) {
            $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_groups` WHERE `id` = %d;", $id ) );
            $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = %d;", $id ) );
            $wpdb->update( $wpdb->prefix . 'adrotate_groups', [ 'fallback' => 0 ], [ 'fallback' => $id ] );
        } elseif ( $what == 'bannergroup' ) {
            $linkmeta = $wpdb->get_results( $wpdb->prepare( "SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = %d AND `user` = '0' AND `schedule` = '0';", $id ) );
            foreach ( $linkmeta as $meta ) {
                adrotate_delete( $meta->ad, 'banner' );
            }
            unset( $linkmeta );
            adrotate_delete( $id, 'group' );
        }
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_active
 Purpose:   Activate or Deactivate a banner
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_active( $id, $what ) {
    global $wpdb;

    if ( $id > 0 ) {
        if ( $what == 'deactivate' ) {
            $wpdb->update( $wpdb->prefix . 'adrotate', [ 'type' => 'disabled' ], [ 'id' => $id ] );
        }
        if ( $what == 'activate' ) {
            // Determine status of ad
            $adstate = adrotate_evaluate_ad( $id );
            $adtype  = ( $adstate == 'error' or $adstate == 'expired' ) ? 'error' : 'active';

            $wpdb->update( $wpdb->prefix . 'adrotate', [ 'type' => $adtype ], [ 'id' => $id ] );
        }
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_reset
 Purpose:   Reset statistics for a banner
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_reset( $id ) {
    global $wpdb;

    if ( $id > 0 ) {
        $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `ad` = %d", $id ) );
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_renew
 Purpose:   Renew the end date of a banner with a new schedule starting where the last ended
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_renew( $id, $howlong = 2592000 ) {
    global $wpdb;

    if ( $id > 0 ) {
        $schedule_id = $wpdb->get_var( $wpdb->prepare( "SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0 ORDER BY `id` DESC LIMIT 1;", $id ) );
        if ( $schedule_id > 0 ) {
            $wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->prefix}adrotate_schedule` SET `stoptime` = `stoptime` + %d WHERE `id` = %d;", $howlong, $schedule_id ) );
        } else {
            $now      = current_time( 'timestamp' );
            $stoptime = $now + $howlong;
            $wpdb->insert( $wpdb->prefix . 'adrotate_schedule', [ 'name' => 'Schedule for ad ' . $id, 'starttime' => $now, 'stoptime' => $stoptime, 'maxclicks' => 0, 'maximpressions' => 0 ] );
            $wpdb->insert( $wpdb->prefix . 'adrotate_linkmeta', [ 'ad' => $id, 'group' => 0, 'user' => 0, 'schedule' => $wpdb->insert_id ] );
        }
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_export
 Purpose:   Export selected banners
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_export( $ids ) {
    if ( is_array( $ids ) ) {
        adrotate_export_ads( $ids );
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_options_submit
 Purpose:   Save options from dashboard
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_options_submit() {
    if ( wp_verify_nonce( $_POST[ 'adrotate_nonce_settings' ], 'adrotate_settings' ) ) {
        $settings_tab = sanitize_key( $_POST[ 'adrotate_settings_tab' ] );

        if ( $settings_tab == 'general' ) {
            $config = get_option( 'adrotate_config' );

            $config[ 'mobile_dynamic_mode' ] = ( isset( $_POST[ 'adrotate_mobile_dynamic_mode' ] ) ) ? 'Y' : 'N';
            $config[ 'jquery' ]              = ( isset( $_POST[ 'adrotate_jquery' ] ) ) ? 'Y' : 'N';
            $config[ 'jsfooter' ]            = ( isset( $_POST[ 'adrotate_jsfooter' ] ) ) ? 'Y' : 'N';

            // Handling for new fields
            $config[ 'duplicate_adverts_filter' ] = ( isset( $_POST[ 'adrotate_duplicate_adverts_filter' ] ) ) ? $_POST[ 'adrotate_duplicate_adverts_filter' ] : 'N';
            $config[ 'textwidget_shortcodes' ]    = ( isset( $_POST[ 'adrotate_textwidget_shortcodes' ] ) ) ? $_POST[ 'adrotate_textwidget_shortcodes' ] : 'N';
            $config[ 'live_preview' ]             = ( isset( $_POST[ 'adrotate_live_preview' ] ) ) ? $_POST[ 'adrotate_live_preview' ] : 'N';
            $config[ 'adblock_disguise' ]         = ( isset( $_POST[ 'adrotate_adblock_disguise' ] ) ) ? $_POST[ 'adrotate_adblock_disguise' ] : '';
            $config[ 'banner_folder' ]            = ( isset( $_POST[ 'adrotate_banner_folder' ] ) ) ? $_POST[ 'adrotate_banner_folder' ] : 'banners';

            // Rest of the settings
            $config[ 'notification_email' ]     = [];
            $config[ 'advertiser_email' ]       = [];
            $config[ 'enable_geo' ]             = 0;
            $config[ 'geo_cookie_life' ]        = 86400;
            $config[ 'geo_email' ]              = '';
            $config[ 'geo_pass' ]               = '';
            $config[ 'enable_advertisers' ]     = 'N';
            $config[ 'enable_editing' ]         = 'N';
            $config[ 'enable_geo_advertisers' ] = 0;

            update_option( 'adrotate_config', $config );

            // Sort out crawlers
            $crawlers     = explode( ',', trim( $_POST[ 'adrotate_crawlers' ] ) );
            $new_crawlers = [];
            foreach ( $crawlers as $crawler ) {
                $crawler = preg_replace( '/[^a-zA-Z0-9\[\]\-_:; ]/i', '', trim( $crawler ) );
                if ( strlen( $crawler ) > 0 ) {
                    $new_crawlers[] = $crawler;
                }
            }
            update_option( 'adrotate_crawlers', $new_crawlers );
        }


        if ( $settings_tab == 'notifications' ) {
            $notifications = get_option( 'adrotate_notifications' );

            $notifications[ 'notification_dash' ] = ( isset( $_POST[ 'adrotate_notification_dash' ] ) ) ? 'Y' : 'N';

            // Dashboard Notifications
            $notifications[ 'notification_dash_expired' ] = ( isset( $_POST[ 'adrotate_notification_dash_expired' ] ) ) ? 'Y' : 'N';
            $notifications[ 'notification_dash_soon' ]    = ( isset( $_POST[ 'adrotate_notification_dash_soon' ] ) ) ? 'Y' : 'N';

            // Turn options off. Available in AdRotate Pro only
            $notifications[ 'notification_email' ]           = 'N';
            $notifications[ 'notification_email_publisher' ] = [ get_option( 'admin_email' ) ];
            $notifications[ 'notification_mail_geo' ]        = 'N';
            $notifications[ 'notification_mail_status' ]     = 'N';
            $notifications[ 'notification_mail_queue' ]      = 'N';
            $notifications[ 'notification_mail_approved' ]   = 'N';
            $notifications[ 'notification_mail_rejected' ]   = 'N';

            update_option( 'adrotate_notifications', $notifications );
        }

        if ( $settings_tab == 'stats' ) {
            $config = get_option( 'adrotate_config' );

            $stats                                   = trim( $_POST[ 'adrotate_stats' ] );
            $config[ 'stats' ]                       = ( is_numeric( $stats ) and $stats >= 0 and $stats <= 3 ) ? $stats : 1;
            $config[ 'enable_loggedin_impressions' ] = 'Y';
            $config[ 'enable_loggedin_clicks' ]      = 'Y';
            $config[ 'enable_clean_trackerdata' ]    = ( isset( $_POST[ 'adrotate_enable_clean_trackerdata' ] ) ) ? 'Y' : 'N';

            if ( $config[ 'enable_clean_trackerdata' ] == "Y" and ! wp_next_scheduled( 'adrotate_delete_transients' ) ) {
                wp_schedule_event( current_time( 'timestamp' ), 'twicedaily', 'adrotate_delete_transients' );
            }
            if ( $config[ 'enable_clean_trackerdata' ] == "N" and wp_next_scheduled( 'adrotate_delete_transients' ) ) {
                wp_clear_scheduled_hook( 'adrotate_delete_transients' );
            }

            $impression_timer             = trim( $_POST[ 'adrotate_impression_timer' ] );
            $config[ 'impression_timer' ] = ( is_numeric( $impression_timer ) and $impression_timer >= 10 and $impression_timer <= 3600 ) ? $impression_timer : 60;
            $click_timer                  = trim( $_POST[ 'adrotate_click_timer' ] );
            $config[ 'click_timer' ]      = ( is_numeric( $click_timer ) and $click_timer >= 60 and $click_timer <= 86400 ) ? $click_timer : 86400;

            update_option( 'adrotate_config', $config );
        }

        if ( $settings_tab == 'roles' ) {
            $config = get_option( 'adrotate_config' );

            adrotate_set_capability( $_POST[ 'adrotate_ad_manage' ], "adrotate_ad_manage" );
            adrotate_set_capability( $_POST[ 'adrotate_ad_delete' ], "adrotate_ad_delete" );
            adrotate_set_capability( $_POST[ 'adrotate_group_manage' ], "adrotate_group_manage" );
            adrotate_set_capability( $_POST[ 'adrotate_group_delete' ], "adrotate_group_delete" );
            $config[ 'ad_manage' ]    = $_POST[ 'adrotate_ad_manage' ];
            $config[ 'ad_delete' ]    = $_POST[ 'adrotate_ad_delete' ];
            $config[ 'group_manage' ] = $_POST[ 'adrotate_group_manage' ];
            $config[ 'group_delete' ] = $_POST[ 'adrotate_group_delete' ];

            update_option( 'adrotate_config', $config );
        }

        if ( $settings_tab == 'misc' ) {
            $config = get_option( 'adrotate_config' );

            $config[ 'widgetalign' ]    = ( isset( $_POST[ 'adrotate_widgetalign' ] ) ) ? 'Y' : 'N';
            $config[ 'widgetpadding' ]  = ( isset( $_POST[ 'adrotate_widgetpadding' ] ) ) ? 'Y' : 'N';
            $config[ 'hide_schedules' ] = ( isset( $_POST[ 'adrotate_hide_schedules' ] ) ) ? 'Y' : 'N';
            $config[ 'w3caching' ]      = ( isset( $_POST[ 'adrotate_w3caching' ] ) ) ? 'Y' : 'N';
            $config[ 'borlabscache' ]   = ( isset( $_POST[ 'adrotate_borlabscache' ] ) ) ? 'Y' : 'N';

            update_option( 'adrotate_config', $config );
        }

        // Return to dashboard
        adrotate_return( 'adrotate-settings', 400, [ 'tab' => $settings_tab ] );
    } else {
        adrotate_nonce_error();
        exit;
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_roles
 Purpose:   Prepare user roles for WordPress
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_prepare_roles( $action ) {
    if ( $action == 'add' ) {
        add_role( 'adrotate_advertiser', __( 'AdRotate Advertiser', 'adrotate' ), [ 'read' => 1 ] );
    }
    if ( $action == 'remove' ) {
        remove_role( 'adrotate_advertiser' );
    }
}

?>
