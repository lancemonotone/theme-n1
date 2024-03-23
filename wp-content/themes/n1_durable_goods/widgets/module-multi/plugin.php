<?php namespace N1_Durable_Goods;

/*
Plugin Name: Multi Module
Description: Show n latest articles in reverse chronological order, newest first.
Can be displayed with image, without image, or as pullquote. More By this Author,
More from Issue [N], Related Content, Featured Article, and Online Only widgets
all use the same or very similar data output (i.e., title, image (if any), author,
issue, category, pullquote) and front-end visual display styles.


Version: 1.0
Author: Durable Goods Design
Text Domain: module-multi
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

class Module_Multi extends \WP_Widget {
    var int $version;

    var array $default_args = [];
    /*--------------------------------------------------*/
    /* Constructor
      /*--------------------------------------------------*/

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct() {
        $this->version = time();

        // load plugin text domain
        add_action( 'init', [ $this, 'widget_textdomain' ] );

        // Hooks fired when the Widget is activated and deactivated
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

        // TODO:	update classname and description
        // TODO:	replace 'Module_Multi' to be named more plugin specific. Other instances exist throughout the code, too.
        parent::__construct(
            'Module_Multi',
            __( 'Multi Module', 'Module_Multi' ),
            [
                'classname'   => 'module-multi',
                'description' => __( 'More By this Author, More from Issue [N], Related Content, Featured Article, and Online Only widgets.',
                    'Module_Multi' )
            ]
        );

        // Register admin styles and scripts
        //add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
        //add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

        // Register site styles and scripts
        //add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
        add_action( 'wp_enqueue_scripts', [ $this, 'register_widget_scripts' ] );

        // Register AJAX
        add_action( "wp_ajax_get_multi_posts", [ $this, 'get_multi_posts' ] );
        add_action( "wp_ajax_nopriv_get_multi_posts", [ $this, 'get_multi_posts' ] );
    } // end constructor

    /*--------------------------------------------------*/
    /* Widget API Functions
      /*--------------------------------------------------*/

    /**
     * Outputs the content of the widget.
     *
     * @param array  args    The array of form elements
     * @param array  instance  The current instance of the widget
     */
    public function widget( $args, $instance ) {
        echo $args[ 'before_widget' ];

        // TODO:	Here is where you manipulate your widget's values based on their input fields
        include( plugin_dir_path( __FILE__ ) . '/lib/functions.php' );
        include( plugin_dir_path( __FILE__ ) . '/views/widget-multi.php' );

        echo $args[ 'after_widget' ];
    } // end widget

    /**
     * Processes the widget's options to be saved.
     *
     * @param array    new_instance    The new instance of values to be generated via the update.
     * @param array    old_instance    The previous instance of values before the update.
     */
    public function update( $new_instance, $old_instance ) {
        $instance                       = $old_instance;
        $instance[ 'title' ]            = strip_tags( $new_instance[ 'title' ] );
        $instance[ 'subtitle' ]         = strip_tags( $new_instance[ 'subtitle' ] );
        $instance[ 'flavor' ]           = strip_tags( $new_instance[ 'flavor' ] );
        $instance[ 'taxonomy' ]         = strip_tags( $new_instance[ 'taxonomy' ] );
        $instance[ 'term' ]             = strip_tags( $new_instance[ 'term' ] );
        $instance[ 'number' ]           = strip_tags( $new_instance[ 'number' ] );
        $instance[ 'ad_after' ]         = strip_tags( $new_instance[ 'ad_after' ] );
        $instance[ 'newsletter_after' ] = strip_tags( $new_instance[ 'newsletter_after' ] );
        $instance[ 'social_after' ]     = strip_tags( $new_instance[ 'social_after' ] );
        $instance[ 'bookstore_after' ]  = strip_tags( $new_instance[ 'bookstore_after' ] );
        $instance[ 'order' ]            = strip_tags( $new_instance[ 'order' ] );
        $instance[ 'orderby' ]          = strip_tags( $new_instance[ 'orderby' ] );
        $instance[ 'infinite' ]         = strip_tags( $new_instance[ 'infinite' ] );

        return $instance;
    } // end widget

    /**
     * Generates the administration form for the widget.
     *
     * @param array    instance    The array of keys and values for the widget.
     */
    public function form( $instance ) {
        // TODO:	Store the values of the widget in their own variable
        // Display the admin form
        include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );
    } // end form

    /*--------------------------------------------------*/
    /* Public Functions
    /*--------------------------------------------------*/

    /**
     * Loads the Widget's text domain for localization and translation.
     */
    public function widget_textdomain() {
        load_plugin_textdomain( 'Module_Multi', false, plugin_dir_path( __FILE__ ) . '/lang/' );
    } // end widget_textdomain

    /**
     * Fired when the plugin is activated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
     */
    public function activate( $network_wide ) {
        // TODO define activation functionality here
    } // end activate

    /**
     * Fired when the plugin is deactivated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
     */
    public function deactivate( $network_wide ) {
        // TODO define deactivation functionality here
    } // end deactivate

    /**
     * Registers and enqueues admin-specific styles.
     */
    public function register_admin_styles() {
        wp_enqueue_style( 'module-multi-admin-styles', get_stylesheet_directory_uri() . '/widgets/module-multi/css/admin.css' );
    } // end register_admin_styles

    /**
     * Registers and enqueues admin-specific JavaScript.
     */
    public function register_admin_scripts() {
        wp_enqueue_script( 'module-multi-admin-script', get_stylesheet_directory_uri() . '/widgets/module-multi/js/admin.js', [ 'jquery' ] );
    } // end register_admin_scripts

    /**
     * Registers and enqueues widget-specific styles.
     */
    public function register_widget_styles() {
        wp_enqueue_style( 'module-multi-widget-styles', get_stylesheet_directory_uri() . '/widgets/module-multi/css/widget.css' );
    } // end register_widget_styles

    /**
     * Registers and enqueues widget-specific scripts.
     */
    public function register_widget_scripts() {
        $file_path = '/widgets/module-multi/js/widget.js';
        if ( file_exists( $file = get_stylesheet_directory() . $file_path ) ) {
            $this->version = filemtime( get_stylesheet_directory() . $file_path );
        }
        wp_register_script( 'module-multi-script', get_stylesheet_directory_uri() . $file_path, [ 'jquery' ], $this->version, true );
        wp_localize_script( 'module-multi-script', 'modmulti', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
        wp_enqueue_script( 'module-multi-script' );
    } // end register_widget_scripts

    /**
     * Get posts by several qualifiers:
     *
     * featured-default
     * featured-author
     * online-only
     * author
     * issue
     * tag
     *
     * @param string $flavor
     * @param int $number
     * @param string $order
     * @param string $orderby
     *
     * @return array WP_Post
     */
    function get_multi_posts( $flavor = 'online-only', $number = -1, $ad_after = 0, $order = 'DESC', $orderby = 'date', $newsletter_after = 0, $social_after = 0, $bookstore_after = 0, $taxonomy = null, $term = null, $meta_key = null ) {
        $flavor           = isset( $_REQUEST[ 'flavor' ] ) ? $_REQUEST[ 'flavor' ] : $flavor;
        $taxonomy         = isset( $_REQUEST[ 'taxonomy' ] ) ? $_REQUEST[ 'taxonomy' ] : $taxonomy;
        $term             = isset( $_REQUEST[ 'term' ] ) ? $_REQUEST[ 'term' ] : $term;
        $number           = isset( $_REQUEST[ 'number' ] ) ? intval( $_REQUEST[ 'number' ] ) : $number;
        $ad_after         = isset( $_REQUEST[ 'ad_after' ] ) ? intval( $_REQUEST[ 'ad_after' ] ) : $ad_after;
        $newsletter_after = isset( $_REQUEST[ 'newsletter_after' ] ) ? intval( $_REQUEST[ 'newsletter_after' ] ) : $newsletter_after;
        $social_after     = isset( $_REQUEST[ 'social_after' ] ) ? intval( $_REQUEST[ 'social_after' ] ) : $social_after;
        $bookstore_after  = isset( $_REQUEST[ 'bookstore_after' ] ) ? intval( $_REQUEST[ 'bookstore_after' ] ) : $social_after;
        $order            = isset( $_REQUEST[ 'order' ] ) ? $_REQUEST[ 'order' ] : $order;
        $orderby          = isset( $_REQUEST[ 'orderby' ] ) ? $_REQUEST[ 'orderby' ] : $orderby;
        $paged            = isset( $_REQUEST[ 'paged' ] ) ? intval( $_REQUEST[ 'paged' ] ) : null;
        $meta_key         = isset( $_REQUEST[ 'meta_key' ] ) ? $_REQUEST[ 'meta_key' ] : $meta_key;

        // default args
        $this->default_args = [
            'post_type'      => 'article',
            'post_status'    => 'publish',
            'posts_per_page' => $number,
            'order'          => $order,
            'orderby'        => $orderby,
            'meta_key'       => $meta_key,
            'paged'          => $paged,
        ];

        $flavor_args = [];
        switch ( $flavor ) {
            case 'home-feature-1':
            case 'home-feature-2':
            case 'home-flow':
                $flavor_args = $this->get_home_flow_args( $flavor );
                break;
            case 'home-hero':
                $flavor_args = $this->get_home_hero_args();
                break;
            case 'featured-default':
                $flavor_args = $this->get_featured_default_args();
                break;
            case 'featured-author':
                $flavor_args = $this->get_featured_author_args();
                break;
            case 'archive':
                $flavor_args = $this->get_archive_args( $taxonomy, $term );
                break;
            case 'sticky':
                $flavor_args = $this->get_sticky_args( $taxonomy, $term );
                break;
            case 'online-only':
                $flavor_args = $this->get_scroll_args();
                break;
            case 'author':
                $flavor_args = $this->get_author_args();
                break;
            case 'issue':
                $flavor_args = $this->get_issue_args();
                break;
            case 'tag':
                $flavor_args = $this->get_tag_args();
                break;
            default:
        }

        $query_args = wp_parse_args( $flavor_args, $this->default_args );
        // $query_args        = array_merge( (array)$flavor_args, (array)$this->default_args );
        $this->multi_query = new \WP_Query( $query_args );
        $the_posts         = $this->multi_query->posts;

        // is this is an ajax call?
        global $pagenow;
        if ( $pagenow == 'admin-ajax.php' ) {
            if ( count( $the_posts ) ) {
                $response[ 'type' ] = 'success';
                ob_start();
                $this->print_multi_posts( $the_posts, $ad_after, $flavor, $newsletter_after, $social_after, $bookstore_after );
                $response[ 'content' ] = ob_get_clean();
            } else {
                $response[ 'type' ]    = 'fail';
                $response[ 'content' ] = "<p><small>" . __( 'end of transmission.' ) . "</small></p>";
            }
            echo json_encode( $response );
            die();
        }

        return $the_posts;
    }

    /**
     * Outputs Multi-posts according to format criteria
     *
     */

    /** START HERE **/

    function print_multi_posts( $the_posts, $ad_after = 0, $flavor = 'archive', $newsletter_after = 0, $social_after = 0, $bookstore_after = 0 ) {
        $did_ad           = false;
        $ad_after         = $ad_after == 0 ? false : intval( $ad_after );
        $newsletter_after = $newsletter_after == 0 ? false : intval( $newsletter_after );
        $social_after     = $social_after == 0 ? false : intval( $social_after );
        $bookstore_after  = $bookstore_after == 0 ? false : intval( $bookstore_after );
        $post_counter     = 0;
        foreach ( $the_posts as $the_p ) {
            if ( $post_counter === $ad_after ) {
                Adrotate::display( 1 );
                $did_ad = true;
            }
            if ( $post_counter === $newsletter_after ) {
                the_widget( '\N1_Durable_Goods\Module_Newsletter' );
            }
            if ( $post_counter === $social_after ) {
                the_widget( '\N1_Durable_Goods\Module_Social' );
            }
            if ( $post_counter === $bookstore_after ) {
                the_widget( '\N1_Durable_Goods\Module_Bookstore' );
            }
            $this->print_post( $flavor, $the_p );

            $post_counter++;
        }

        if ( ! $did_ad && $ad_after > 0 ) {
            Adrotate::display( 1 );
        }
    }

    function print_post( $flavor, $the_p ) {
        // Boy, this function escalated quickly.
        // Try to get the category of the post
        // Initialization for $flags
        $flags = null; // or an appropriate default value

        // Try to get the category of the post
        $terms        = wp_get_post_terms( $the_p->ID, 'issue' );
        $section      = ! empty( $terms ) ? reset( $terms ) : null; // Ensure $terms is not empty
        $taxonomy     = 'category';
        $article_type = 'magazine';

        // Additional checks for $section being an object
        if ( ! is_object( $section ) ) {
            $sections = wp_get_post_terms( $the_p->ID, 'online-only' );
            // Ensure $sections is not empty, then get the second element if count > 1 and the only element if count == 1
            $section      = ! empty( $sections ) ? ( count( $sections ) > 1 ? $sections[ 1 ] : $sections[ 0 ] ) : null;
            $taxonomy     = 'online-only';
            $article_type = 'online-only';
        }

        // Further check before accessing properties
        $is_event       = is_object( $section ) && $section->slug === 'events';
        $is_online_only = is_object( $section ) && $section->taxonomy === 'online-only';
        $is_issue       = is_object( $section ) && $section->taxonomy === 'issue';
        $is_search      = N1_Magazine::get_page_type() == 'search';

        if ( is_object( $section ) ) {
            if ( $section->taxonomy == 'category' ) {
                $the_tax = 'magazine';
            } else {
                $the_tax = $section->taxonomy;
            }
        } else {
            $the_tax = '';
        }

        $flags = $this->get_flags( $flavor, $the_p, $section );

        $format = get_field( 'article_teaser_format', $the_p->ID );
        if ( $is_event ) {
            $format = 'pullquote';
        }
        // Check if the post has a featured image. If not, use a pullquote.
        if ( $format === 'with_image' && ! has_post_thumbnail( $the_p->ID ) ) {
            $format = 'pullquote';
        }
        $format = $format ?: 'default';


        // cache content for use later
        $content  = $this->get_content( $the_p, $flavor, $format, $is_event );
        $authors  = N1_Magazine::get_authors( $the_p->ID );
        $featured = ! is_search() && get_field( 'article_featured', $the_p->ID ) ? 'article-featured' : '';
        $subhead  = get_field( 'article_subhead', $the_p->ID );

        switch ( $flavor ) {
            case 'archive':
            case 'sticky':
                switch ( $format ) {
                    case 'pullquote':
                        if ( $is_event ) {
                            $title = $this->get_pullquote( $the_p );
                        }
                        include( plugin_dir_path( __FILE__ ) . '/views/cards/pullquote.php' );
                        break;
                    case 'with_image':
                    default:
                        include( plugin_dir_path( __FILE__ ) . '/views/cards/with_image.php' );
                        break;
                }
                break;
            case 'online-only-home':
            case 'home-flow':
            case 'featured-default':
            default:
                if ( $is_event ) {
                    $title = $this->get_pullquote( $the_p );
                    include( plugin_dir_path( __FILE__ ) . '/views/cards/event.php' );
                } else {
                    include( plugin_dir_path( __FILE__ ) . '/views/cards/default.php' );
                }
                break;
        }
    }

    function get_content( $the_p, $flavor, $format, $is_event = false ) {
        $content = '';

        if ( $is_event ) {
            $format = 'pullquote';
        }

        switch ( $flavor ) {
            case 'archive':
            case 'online-only-home':
            case 'home-hero':
            case 'sticky':
                $img_size = 'content-full';
                break;
            default:
                $img_size = 'multi-module';
                break;
        }

        switch ( $format ) {
            case 'with_image':
                $img_id   = get_post_thumbnail_id( $the_p->ID );
                $img_meta = wp_prepare_attachment_for_js( $img_id );
                $img_url  = $img_meta[ 'url' ] ?? '';
                $alt      = $img_meta[ 'alt' ] ?? '';
                $height   = $img_meta[ 'height' ] ?? '';
                $width    = $img_meta[ 'width' ] ?? '';
                $content  = <<<EOD
<figure class="article-image" style="" />
<img src="{$img_url}" alt="{$alt}" height="{$height}" width="{$width}" />
</figure>
EOD;
                break;
            case 'no_image':
                $content = '';
                break;
            case 'pullquote':
                if ( $is_event && $flavor !== 'archive' ) {
                    $quote = $the_p->post_title;
                } else {
                    $quote = $this->get_pullquote( $the_p );
                }
                $content = '<p class="pullquote">' . $this->shorten_str_by_word( $quote, 130 ) . '</p>';
                $content = apply_filters( 'the_excerpt', $content );
                break;
            default:
        }

        return $content;
    }

    function get_pullquote( $the_p ) {
        $found = false;
        $quote = '';

        if ( $pullquotes = get_field( 'article_pullquote_repeater', $the_p->ID ) ) {
            foreach ( $pullquotes as $pullquote ) {
                if ( $pullquote[ 'article_pullquote_featured' ] ) {
                    $quote = $pullquote[ 'article_pullquote' ];
                    $found = true;
                    break;
                }
            }
            if ( ! $found ) {
                $quote = $pullquotes[ 0 ][ 'article_pullquote' ];
            }
        }

        return $quote != '' ? $quote : $the_p->post_excerpt;
    }

    function print_post_head( $the_p, $article_type, $section, $authors ) {
        $section_slug = $section->slug ?? '';
        switch ( $article_type ) {
            case 'online-only':
                $date = $section_slug == 'events' ? get_field( 'event_date', $the_p->ID ) : $the_p->post_date ?>
                <p class="date"><?= date( 'F j, Y', strtotime( $date ) ) ?></p>
                <?php break;
            case 'magazine':
                if ( ! $issue = N1_Magazine::get_issue_by_slug( $section_slug ) ) {
                    break;
                }
                $issue_art = get_field( 'issue_art', $issue->ID );
                ?>
                <figure class="issue-icon">
                    <a href="<?= home_url() ?>/magazine/<?= $issue->post_name ?>">
                        <img src="<?= $issue_art[ 'sizes' ][ 'issue-art' ] ?>"
                             alt="<?php _e( 'Art for' ) ?> <?= $issue->post_title ?>">
                    </a>
                </figure>
                <?php break;
            case 'page':
                break;
        }
    }

    function get_flags( $flavor, $the_p, $section ): string {
        // Initialize $flags to avoid "undefined variable" warning.
        $flags = '';

        // Ensure $section is an object before accessing its properties.
        if ( ! is_object( $section ) ) {
            return '<div class="flags"></div>';
        }

        $is_event       = $section->slug === 'events';
        $is_online_only = $section->taxonomy === 'online-only';
        $is_issue       = $section->taxonomy === 'issue';

        if ( $is_event ) {
            if ( $date = $section->slug == 'events' ? get_field( 'event_date', $the_p->ID ) : '' ) {
                $flags = "<strong>{$section->name}</strong> " . date( 'F j, Y', strtotime( $date ) );
            } else {
                $flags = "<strong>{$section->name}</strong>";
            }
        } elseif ( $is_online_only ) {
            $flags = "<strong>{$section->name}</strong>";
        } elseif ( $is_issue ) {
            $issue        = N1_Magazine::get_issue_by_slug( $section->slug );
            $issue_number = $issue->post_title;
            $issue_name   = get_field( 'issue_name', $issue->ID );
            $flags        = "<strong>{$issue_number}</strong> {$issue_name}";
        }

        return '<div class="flags">' . $flags . '</div>';
    }

    function shorten_str_by_word( $str, $limit, $ellipsis = '...' ) {
        if ( strlen( $str ) >= ( $limit + strlen( $ellipsis ) ) ) { // add 4 for ellipsis
            return preg_replace( '/ [^ ]*$/', $ellipsis, substr( $str, 0, $limit ) );
        } else {
            return $str;
        }
    }

    /**
     * Returns array of args to retrieve posts related by tag.
     */
    function get_sticky_args( $taxonomy, $term ): array {
        $st = [];
        if ( $term ) {
            array_push( $st, $term );
        } else {
            foreach ( get_terms( $taxonomy ) as $temp ) {
                array_push( $st, $temp->slug );
            }
        }
        $sticky_posts = get_option( 'sticky_posts' );

        return [
            'posts_per_page'      => -1,
            'post__in'            => $sticky_posts,
            'ignore_sticky_posts' => 1,
            'tax_query'           => [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $st,
                    'operator' => 'IN'
                ]
            ]
        ];
    }

    /**
     * Returns array of args to retrieve posts related by tag.
     */
    function get_tag_args(): array {
        global $post;

        $id = ! empty( $post ) ? $post->ID : 0;

        $tags = [];
        foreach ( wp_get_post_terms( $id, 'post_tag' ) as $temp ) {
            array_push( $tags, $temp->term_id );
        }

        return [
            'post__not_in' => [ $id ],
            'tax_query'    => [
                [
                    'taxonomy' => 'post_tag',
                    'field'    => 'id',
                    'terms'    => $tags,
                    'operator' => 'IN'
                ]
            ]
        ];
    }

    /**
     * Returns array of args to retrieve posts in current issue.
     */
    function get_issue_args(): array {
        global $post;

        $id = ! empty( $post ) ? $post->ID : 0;

        $context_issue = N1_Magazine::get_context_issue();

        return [
            'post__not_in' => [ $id ],
            'tax_query'    => [
                [
                    'taxonomy' => 'issue',
                    'field'    => 'slug',
                    'terms'    => $context_issue->post_name,
                    'operator' => 'IN'
                ]
            ]
        ];
    }

    /**
     * Returns array of args to retrieve posts by current
     * article's author.
     */
    function get_author_args(): array {
        global $post;

        $id = ! empty( $post ) ? $post->ID : 0;

        $at = [];
        foreach ( wp_get_post_terms( $id, 'authors' ) as $temp ) {
            array_push( $at, $temp->term_id );
        }

        return [
            'post__not_in' => [ $id ],
            'tax_query'    => [
                [
                    'taxonomy' => 'authors',
                    'field'    => 'id',
                    'terms'    => $at,
                    'operator' => 'IN'
                ]
            ]
        ];
    }

    /**
     * Returns array of args to retrieve posts in the
     * Online Only.
     *
     */
    function get_scroll_args(): array {
        global $post;

        $id = ! empty( $post ) ? $post->ID : 0;

        $st = [];
        foreach ( get_terms( 'online-only' ) as $temp ) {
            array_push( $st, $temp->term_id );
        }

        return [
            'post__not_in' => [ $id ],
            'tax_query'    => [
                [
                    'taxonomy' => 'online-only',
                    'field'    => 'id',
                    'terms'    => $st,
                    'operator' => 'IN'
                ]
            ]
        ];
    }

    /**
     * Returns array of args to retrieve posts by taxonomy & term.
     *
     */
    function get_archive_args( $taxonomy, $term ): array {
        $st = [];
        if ( $term ) {
            array_push( $st, $term );
        } else {
            $terms = get_terms( $taxonomy );
            if ( ! is_wp_error( $terms ) ) {
                foreach ( $terms as $temp ) {
                    // Ensure each term is an object before accessing its properties.
                    if ( is_object( $temp ) ) {
                        array_push( $st, $temp->slug );
                    }
                }
            }
        }

        return [
            'post__not_in' => get_option( 'sticky_posts' ),
            'tax_query'    => [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $st,
                    'operator' => 'IN'
                ]
            ]
        ];
    }

    /**
     * Returns array of args to retrieve posts by authors
     * who have articles published in the current issue.
     *
     */
    function get_featured_author_args(): array {
        global $wpdb;
        $qry      = "SELECT DISTINCT f.ID FROM (
					SELECT DISTINCT p.*, t.name as author FROM $wpdb->posts AS p
					INNER JOIN $wpdb->term_relationships AS tr ON tr.object_id = p.ID
					INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.parent = 0
					INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id 
					AND p.post_status = 'publish'
					AND t.term_id IN (
						SELECT DISTINCT t1.term_id FROM $wpdb->terms t1
						INNER JOIN $wpdb->term_taxonomy tt1 ON tt1.term_id = t1.term_id
						INNER JOIN $wpdb->term_relationships tr1 ON tr1.term_taxonomy_id = tt1.term_taxonomy_id
						WHERE tt1.taxonomy = 'authors'
						#block The Editors?
						AND t1.slug != 'editors-the'
						AND tr1.object_id IN (
							SELECT DISTINCT tr2.object_id from $wpdb->term_relationships tr2 
							INNER JOIN $wpdb->term_taxonomy tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
							INNER JOIN $wpdb->terms t2 ON t2.term_id = tt2.term_id
							WHERE t2.slug = '" . N1_Magazine::get_context_issue()->post_name . "'
						)
					)  
				) as f
				GROUP BY f.author
				ORDER BY f.post_date DESC;";
        $posts_in = $wpdb->get_col( $qry );

        $context_issue = N1_Magazine::get_context_issue();

        return [
            'post__in'  => $posts_in,
            'tax_query' => [
                [
                    'taxonomy' => 'issue',
                    'field'    => 'slug',
                    'terms'    => $context_issue,
                    'operator' => 'NOT IN'
                ]
            ]
        ];
    }

    /**
     * Returns post selected in Site Settings Home Hero.
     */
    function get_home_hero_args(): array {
        // query wordpress database for 'home_hero' option
        return [
            'post__in' => [ get_field( 'home_hero', 'options' ) ]
        ];
    }

    /**
     * Returns posts selected in Site Settings Home Flow.
     */
    function get_home_flow_args( $flavor ): array {
        $flavor          = str_replace( '-', '_', $flavor );
        $scroll_args     = $this->get_scroll_args();
        $query_args      = array_merge( (array)$this->default_args, (array)$scroll_args );
        $the_query       = new \WP_Query( $query_args );
        $scroll_posts    = $the_query->posts;
        $scroll_post_ids = [];
        foreach ( $scroll_posts as $scroll_post ) {
            $scroll_post_ids[] = $scroll_post->ID;
        }


        // First, get the post IDs in order from the ACF field
        $flow_posts = get_field( $flavor, 'option' );

        $flow_post_ids = [];

        if ( $flow_posts ) {
            foreach ( $flow_posts as $flow_post ) {
                $flow_post_ids[] = $flow_post[ 'article' ];
            }
        }

        $flow_args = [
            'post__in' => $flow_post_ids,
            'orderby'  => 'post__in'
        ];

        return $flow_args;

        // // Grab the first 3 posts from the scroll
        // $first_set  = array_slice( $scroll_post_ids, 0, 3 );
        // $second_set = array_slice( $scroll_post_ids, 3, 4 );
        // $third_set  = array_slice( $scroll_post_ids, 7, 2 );
        //
        // // Merge the first set of scroll posts with the flow posts
        // $post__in    = $first_set;
        // $post__in [] = $flow_post_ids[ 0 ];
        // $post__in    = array_merge( $post__in, $second_set );
        // $post__in [] = $flow_post_ids[ 1 ];
        // $post__in    = array_merge( $post__in, $third_set );
        //
        // return [
        //     'post__in' => $post__in,
        //     'orderby'  => 'post__in'
        // ];
    }

    /**
     * Returns array of args to retrieve Featured Articles
     * which are NOT in the 'online-only' taxonomy.
     *
     * @return unknown
     */
    function get_featured_default_args() {
        $context_issue = N1_Magazine::get_context_issue();
        $st            = [];
        foreach ( get_terms( 'online-only' ) as $temp ) {
            array_push( $st, $temp->term_id );
        }

        return [
            'meta_query' => [
                [
                    'key'     => 'article_featured',
                    'value'   => '1',
                    'compare' => '=',
                ]
            ],
            'tax_query'  => [
                [
                    'taxonomy' => 'online-only',
                    'field'    => 'id',
                    'terms'    => $st,
                    'operator' => 'NOT IN'
                ],
                [
                    'taxonomy' => 'issue',
                    'field'    => 'slug',
                    'terms'    => $context_issue,
                    'operator' => 'IN'
                ]
            ]
        ];
    }

} // end class

add_action( 'widgets_init', function () {
    register_widget( "\N1_Durable_Goods\Module_Multi" );
} );
