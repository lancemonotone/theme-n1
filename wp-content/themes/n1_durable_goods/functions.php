<?php namespace N1_Durable_Goods;
/**
 * Function to log data to the console
 *
 * @param $key
 * @param $data
 * @param bool $to_error_log
 *
 * @return void
 */
function console_log($key, $data = null, bool $to_error_log = false): void {
    $output = json_encode(! empty($data) ? array($key, $data) : $key);
    error_log(stripslashes($output));
}

class N1 {
    public function __construct() {
        include_once( 'lib/class.constants.php' );
        //error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
        // error reporting errors only
        //error_reporting( E_NOTICE | E_WARNING | E_ERROR | E_PARSE );
        // add_action( 'send_headers', [$this, 'send_headers'] );
        add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
        add_action( 'init', [ $this, 'init' ] );
        add_action( 'widgets_init', [ $this, 'remove_widgets' ] );
        add_filter( 'pre_get_posts', [ $this, 'only_published_in_feed' ] );
        add_filter( 'wp_title', [ $this, 'twentytwelve_wp_title' ], 10, 2 );
        //add_filter( 'feed_link', array(&$this, 'http_feed', 10));
        //add_filter( 'force_ssl',  array(&$this, 'http_feed_force_ssl', 10, 3));
        add_shortcode( 'pullquote', [ $this, 'article_pullquote' ] );
        add_shortcode( 'date_today', [ $this, 'date_shortcode' ] );
        include_once( 'lib/twentytwelve_functions.php' );
    }

    function send_headers() {
        // check if header is already sent
        if ( ! headers_sent() ) {
            header( "Cache-Control: max-age=14400" );
        }
    }

    function setup_theme() {
        include_once( 'lib/class.comments.php' );
        $this->setup_widgets();
        $this->setup_plugins();
    }

    function init() {
        include_once( 'lib/utility.php' );
        include_once( 'lib/class.login.php' );
        include_once( 'lib/custom_post_types.php' );
        include_once( 'lib/custom_taxonomies.php' );
        include_once( 'lib/class.metered_paywall.php' );
        include_once( 'lib/magazine.php' );
        include_once( 'lib/class.assets.php' );
        include_once( 'lib/class.search.php' );
        include_once( 'lib/class.adrotate.php' );
        include_once( 'lib/class.navigation.php' );
        include_once( 'lib/class.home_banner.php' );
        include_once( 'lib/class.tag-colors.php' );
    }

    function setup_plugins() {
        include_once( 'lib/class.plugins.php' );
        include_once( 'lib/class.relevanssi.php' );
        include_once( 'lib/class.searchwp-live-search.php' );
    }

    function setup_widgets() {
        include_once( 'lib/widgets.php' );
        // include_once( 'widgets/module-books/plugin.php' );
        include_once( 'widgets/module-bookstore/plugin.php' );
        include_once( 'widgets/module-paper-monument/plugin.php' );
        include_once( 'widgets/module-issue-archives/plugin.php' );
        include_once( 'widgets/module-multi/plugin.php' );
        include_once( 'widgets/module-toc/plugin.php' );
        include_once( 'widgets/module-subscribe/plugin.php' );
        include_once( 'widgets/module-newsletter/plugin.php' );
        include_once( 'widgets/module-social/plugin.php' );
        include_once( 'widgets/module-download/plugin.php' );
    }

    function remove_widgets() {
        $wp_widgets = [
            'WP_Widget_Pages'           => 'Pages Widget',
            'WP_Widget_Calendar'        => 'Calendar Widget',
            'WP_Widget_Archives'        => ' Archives Widget',
            'WP_Widget_Links'           => ' Links Widget',
            'WP_Widget_Meta'            => ' Meta Widget',
            //'WP_Widget_Search' => ' Search Widget',
            //'WP_Widget_Text' => ' Text Widget',
            'WP_Widget_Categories'      => ' Categories Widget',
            'WP_Widget_Recent_Posts'    => ' Recent Posts Widget',
            'WP_Widget_Recent_Comments' => ' Recent Comments Widget',
            'WP_Widget_RSS'             => ' RSS Widget',
            'WP_Widget_Tag_Cloud'       => ' Tag Cloud Widget',
            'WP_Nav_Menu_Widget'        => 'Menus Widget',
        ];
        foreach ( $wp_widgets as $k => $v ) {
            unregister_widget( $k );
        }
    }

    /**
     * Make feed http://
     */
    function http_feed( $url ) {
        return str_replace( 'https://', 'http://', $url );
    }

    function http_feed_force_ssl( $force_ssl, $post_id = 0, $url = '' ) {
        if ( strpos( $url, '/feed/' ) !== false ) {
            $force_ssl = false;
        }

        return $force_ssl;
    }

    /**
     * Add date shortcode
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    function date_shortcode( $atts, $content = null ) {
        return $content ? date( $content ) : date( "F d, Y", current_time( 'timestamp' ) );
    }

    /**
     * Add Pullquote shortcode
     */
    function article_pullquote( $which ) {
        global $post;
        $pullquotes = get_field( 'article_pullquote_repeater', $post->ID );
        $which      = intval( $which[ 0 ] ) - 1;
        if (!isset($pullquotes[$which])) {
            // Handle the case when the index does not exist in the array
            // You can return a default value or an error message
            return '';
        }

        $pq         = $pullquotes[ $which ][ 'article_pullquote' ];
        $url        = urlencode( wp_get_shortlink( $post->ID ) );
        $href       = 'https://twitter.com/share?text=' . urlencode( $pq . ' | n+1 |' ) . /*&via=nplusonemag*/
                      '&lang=en&url=' . $url;
        if ( $pq ) {
            return '<blockquote class="pull tweet">' . apply_filters( 'the_content', $pq )
                   . '<a onclick="return popitup(\'' . $href . '\')" href="' . $href . '">Tweet</a>'
                   . '</blockquote>';
        }
    }

    /**
     * Prevents articles in draft issues from appearing in the feed.
     *
     * @param $query
     *
     * @return mixed
     */
    function only_published_in_feed( $query ) {
        if ( ! $query->is_feed ) {
            return $query;
        }
        $draft_issues    = get_posts( [
            'post_type'      => 'toc_desc',
            'posts_per_page' => -1,
            'post_status'    => 'draft',
            // This no longer works in WP4
            /*'orderby'			=> 'post_name',
            'order'				=> 'DESC',*/
        ] );
        $draft_issue_ids = [];
        foreach ( $draft_issues as $issue ) {
            $issue_cat = get_term_by( 'name', $issue->post_title, 'issue' );
            array_push( $draft_issue_ids, $issue_cat->term_id );
        }

        $query->set(
            'tax_query',
            [
                [
                    'taxonomy' => 'issue',
                    'field'    => 'id',
                    'terms'    => $draft_issue_ids,
                    'operator' => 'NOT IN'
                ]
            ]
        );

        return $query;
    }

    /**
     * Creates a nicely formatted and more specific title element text
     * for output in head of document, based on current view.
     *
     * @param string $title Default title text for current view.
     * @param string $sep Optional separator.
     *
     * @return string Filtered title.
     * @since Twenty Twelve 1.0
     *
     */
    function twentytwelve_wp_title( $title, $sep ) {
        global $paged, $page, $post, $authors;

        if ( is_feed() ) {
            return $title;
        }

        if ( ! ! $authors ) {
            $title = explode( $sep, $title );
            // format author name
            $title[ 0 ] = N1_Magazine::format_author_name( $title[ 0 ] ) . ' ';
            $title      = implode( $sep, $title );
        } elseif ( ! empty( $post->ID ) ) {
            $array = wp_get_post_terms( $post->ID, [ 'online-only' ] );
            if ( $is_scroll = ! ! reset( $array ) ) {
                $title .= "Online Only $sep ";
            } elseif ( is_single() && $context_issue = N1_Magazine::get_context_issue() ) {
                $title .= "$context_issue->post_title $sep ";
            }
        }

        // remove 'Issues' and 'Online Only Categories' from title
        $title = str_replace( [ "Issues $sep ", "Online Only Categories $sep " ], [ '', '' ], $title );

        // Add the site name.
        $title .= get_bloginfo( 'name' );

        // Add the site description for the home/front page.
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) ) {
            $title = "$title $sep $site_description";
        }

        // Add a page number if necessary.
        if ( $paged >= 2 || $page >= 2 ) {
            $title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );
        }

        return $title;
    }
}

new N1();
