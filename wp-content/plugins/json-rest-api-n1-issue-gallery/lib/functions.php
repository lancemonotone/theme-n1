<?php

/**
 * Template functions for this plugin
 *
 * Place all functions that may be usable in theme template files here.
 *
 * @package JSON_Rest_API_N1_Issue_Gallery
 *
 * @author Durable Goods Design
 * @version 1.0.0
 * @since 1.0.0
 */
class N1_Issue_Gallery {
    public $widget_id, $widget_name, $debug = true; // from callee
    private $use_only_gallery_order = false;
    private $json_route = '/galleries';
    private $csv_ttl = DAY_IN_SECONDS;

    function __construct( $widget_id, $widget_name, $debug ) {
        $this->widget_id = $widget_id;
        $this->widget_name = $widget_name;
        $this->debug = $debug;
        $this->init();
    }

    /**
     * Initialization function to hook into the WordPress init action
     *
     * Instantiates the class on a global variable and sets the class, actions
     * etc. up for use.
     *
     * @param $widget_id
     * @param $widget_name
     * @param $debug
     */
    static function instance( $widget_id, $widget_name, $debug ) {
        global $n1_JSON_Galleries;

        // Only instantiate the Class if it hasn't been already
        if ( !isset( $n1_JSON_Galleries ) ) {
            $n1_JSON_Galleries = new N1_Issue_Gallery( $widget_id, $widget_name, $debug );
        }
    }

    /**
     * Convert the csv to JSON
     */
    public function init() {
        $this->add_hooks();
    }

    public function add_hooks() {
        // Add JSON API classes here
        add_filter( 'json_url_prefix', array( &$this, 'json_url_prefix' ) );
        add_filter( 'json_endpoints', array( &$this, 'register_routes' ) );
        add_filter( 'posts_join', array( &$this, 'galleries_join_clause' ) );
        add_filter( 'posts_where', array( &$this, 'galleries_where_clause' ) );
        add_filter( 'posts_orderby', array( &$this, 'galleries_orderby_clause' ) );
    }

    public function json_url_prefix() {
        return 'api';
    }

    /**
     * Register the taxonomy-related routes
     * @param array $routes Existing routes
     * @return array Modified routes
     */
    public function register_routes( $routes ) {
        $gallery_routes = array(
            $this->json_route => array(
                array(
                    'callback' => array( $this, 'do_nothing' ),
                    'methods'  => WP_JSON_Server::READABLE
                )
            ),
            $this->json_route . '/(?P<issue>issue-[\d]+)' => array(
                array(
                    'callback' => array( $this, 'get_galleries_by_issue' ),
                    'methods'  => WP_JSON_Server::READABLE,
                    'args'     => array( 'context' => array( 'required' => false, ), )
                )
            )
        );

        return array_merge( $routes, $gallery_routes );
    }

    /**
     * Checks to see if the request came from the '/galleries' REST route
     * @return bool
     */
    private function is_json_route() {
        global $wp_query;
        return !!stristr( $wp_query->query_vars[ 'json_route' ], $this->json_route );
    }

    /**
     * We want to join postmeta for posts and for
     * gallery_order value if it's set.
     * @param $join
     * @return string
     */
    public function galleries_join_clause( $join ) {
        global $wpdb;
        if ( $this->is_json_route() ) {
            $join .= "
                LEFT JOIN " . $wpdb->postmeta . " ON " . $wpdb->posts . ".ID = " . $wpdb->postmeta . ".post_id
                LEFT JOIN " . $wpdb->postmeta . " pm1 ON (
                    " . $wpdb->posts . ".ID = pm1.post_id
                    AND pm1.meta_key = 'article_issue_gallery_order')
            ";
        }
        return $join;
    }

    /**
     * The posts must have a featured image, and if we're using
     * only gallery_order to order the posts, the key must be present
     * @param $where
     * @return string
     */
    public function galleries_where_clause( $where ) {
        global $wpdb;
        if ( $this->is_json_route() ) {
            $where .= "
                AND " . $wpdb->postmeta . ".meta_key = '_thumbnail_id'
            ";
            if ( $this->use_only_gallery_order ) {
                $where .= "
                    AND pm1.meta_key = 'article_issue_gallery_order'
                ";
            }
        }
        return $where;
    }

    /**
     * Order by gallery_order if set, then by menu_order
     * @param $orderby
     * @return string
     */
    public function galleries_orderby_clause( $orderby ) {
        global $wpdb;
        if ( $this->is_json_route() ) {
            $orderby = "
                pm1.meta_value ASC, " . $wpdb->posts . ".menu_order ASC
            ";
        }
        return $orderby;
    }

    /**
     * @todo This should return a schema
     */
    public function do_nothing() {
        return;
    }

    /**
     * Get posts matching $issue. The query is modified by the 'galleries_x_clause'
     * methods above. The posts are then queried for their featured images and
     * pushed into an array of images, which is returned. The original posts array
     * is discarded.
     * @param $request
     * @return bool|string
     */
    public function get_galleries_by_issue( $request ) {
        $issue = $request->get_param( 'issue' );
        $output = $this->get_transient( $issue );
        if ( !$output || $this->debug ) {
            $the_gallery = array( );

            $args = array(
                'post_type'      => 'article',
                'post_status'    => 'publish',
                'issue'          => $issue,
                'posts_per_page' => -1,
            );

            $the_query = new WP_Query( $args );
            if ( $the_query->have_posts() ) {
                while ( $the_query->have_posts() ) {
                    $the_query->the_post();
                    if ( $the_gallery_image = wp_prepare_attachment_for_js( get_post_thumbnail_id() ) ) {
                        $the_gallery_image[ 'article_title' ] = get_the_title();
                        $the_gallery_image[ 'article_url' ] = get_the_permalink();
                        array_push( $the_gallery, $the_gallery_image );
                    }
                }
            }
            $output = array( 'count' => count( $the_gallery ), 'items' => $the_gallery );
            $this->set_transient( $issue, $output );
        }
        return $output;
    }

    public function get_transient( $which ) {
        return get_transient( $this->widget_id . '_' . $which );
    }

    public function set_transient( $which, $data ) {
        set_transient( $this->widget_id . '_' . $which, $data, $this->csv_ttl );
    }
}

/**
 * Initialize REST API classes here
 */
N1_Issue_Gallery::instance( $this->id, $this->friendly_name, $this->debug );