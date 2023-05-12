<?php

class N1 {
	var $version;
	private static $instances = array();

	protected function __clone() {
	}

	public function __wakeup() {
		throw new Exception( "Cannot unserialize singleton" );
	}

	/**
	 * Call this method to get singleton
	 *
	 * @return N1
	 */
	public static function Instance() {
		$cls = get_called_class(); // late-static-bound class name
		if ( ! isset( self::$instances[ $cls ] ) ) {
			self::$instances[ $cls ] = new self();
		}

		return self::$instances[ $cls ];
	}

	/**
	 * Private constructor so nobody else can instance it
	 *
	 */
	private function __construct() {
		$this->version = "1.2";
		//error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
		// error reporting errors only
		//error_reporting( E_NOTICE | E_WARNING | E_ERROR | E_PARSE );
		// add_action( 'send_headers', array( &$this, 'send_headers' ) );
		add_action( 'after_setup_theme', array( &$this, 'setup_widgets' ) );
		add_action( 'after_setup_theme', array( &$this, 'setup_plugins' ) );
		add_action( 'init', array( &$this, 'setup_utility' ) );
		add_action( 'after_setup_theme', array( &$this, 'setup_comments' ) );
		add_action( 'init', array( &$this, 'setup_post_types' ) );
		add_action( 'init', array( &$this, 'setup_taxonomies' ) );
		add_action( 'init', array( &$this, 'setup_magazine' ) );
		add_action( 'admin_init', array( &$this, 'setup_search' ) );
		add_action( 'widgets_init', array( &$this, 'remove_widgets' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'setup_scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'setup_admin_style' ) );
		add_action( 'login_enqueue_scripts', array( &$this, 'setup_admin_style' ) );
		add_filter( 'pre_get_posts', array( &$this, 'only_published_in_feed' ) );
		add_filter( 'post_link', array( 'Custom_Taxonomies', 'filter_post_link' ), 10, 2 );
		add_filter( 'post_type_link', array( 'Custom_Taxonomies', 'filter_post_type_link' ), 10, 2 );
		add_filter( 'wp_title', array( &$this, 'twentytwelve_wp_title' ), 10, 2 );
		add_filter( 'loginout', array( &$this, 'loginout_text_change' ) );
		//add_filter( 'feed_link', array(&$this, 'http_feed', 10));
		//add_filter( 'force_ssl',  array(&$this, 'http_feed_force_ssl', 10, 3));
		add_shortcode( 'pullquote', array( &$this, 'article_pullquote' ) );
		add_shortcode( 'date_today', array( &$this, 'date_shortcode' ) );
		include_once( 'lib/twentytwelve_functions.php' );
	}

	function send_headers() {
		// check if header is already sent
		if ( ! headers_sent() ) {
			header( "Cache-Control: max-age=14400" );
		}
	}

	function setup_scripts_styles() {
		// scripts
		wp_enqueue_script( 'classie', get_stylesheet_directory_uri() . '/js/classie.js', array(), $this->version, TRUE );
		wp_enqueue_script( 'fastclick', get_stylesheet_directory_uri() . '/js/fastclick.js', array(), $this->version, TRUE );
		wp_enqueue_script( 'menus', get_stylesheet_directory_uri() . '/js/menus.js', array( 'classie' ), $this->version, TRUE );
		wp_enqueue_script( 'caroufredsel', get_stylesheet_directory_uri() . '/js/caroufredsel/jquery.carouFredSel-6.2.1-packed.js', array(), $this->version, TRUE );
		wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', array( 'caroufredsel' ), $this->version, TRUE );
		// styles
		wp_enqueue_style( 'n1-styles', get_stylesheet_uri(), array(), $this->version );
	}


	function setup_admin_style() {
		// scripts
		// AdRotate script load is buggy, don't know why.
		// We'll load our own versions here and hope they don't need to be updated.
		wp_enqueue_script( 'raphael-n1', get_stylesheet_directory_uri() . '/js/r.js', array( 'jquery' ) );
		wp_enqueue_script( 'elycharts-n1', get_stylesheet_directory_uri() . '/js/e.js', array( 'jquery', 'raphael-n1' ) );
		wp_enqueue_script( 'textatcursor-n1', get_stylesheet_directory_uri() . '/js/t.js' );
		wp_enqueue_script( 'clicktracker-n1', get_stylesheet_directory_uri() . '/js/jquery.clicktracker.js' );
		wp_enqueue_script( 'jshowoff-n1', get_stylesheet_directory_uri() . '/js/jquery.jshowoff.min.js' );
		wp_enqueue_script( 'uploader-hook-n1', get_stylesheet_directory_uri() . '/js/uploader-hook.js' );
		// styles
		wp_enqueue_style( 'n1-admin-style', get_stylesheet_directory_uri() . '/css/admin-style.css', array(), $this->version );
	}

	function setup_utility() {
		include_once( 'lib/utility.php' );
	}

	function setup_comments() {
		include_once( 'lib/class.comments.php' );
	}

	function setup_search() {
		include_once( 'lib/class.search.php' );
	}

	function setup_taxonomies() {
		include_once( 'lib/custom_taxonomies.php' );
	}

	function setup_post_types() {
		include_once( 'lib/custom_post_types.php' );
	}

	function setup_magazine() {
		include_once( 'lib/magazine.php' );
	}

	function setup_plugins() {
		include_once( 'lib/class.plugins.php' );
		include_once( 'lib/class.relevanssi.php' );
		include_once( 'lib/class.searchwp-live-search.php' );
	}

	function setup_widgets() {
		include_once( 'lib/widgets.php' );
		include_once( 'widgets/module-books/plugin.php' );
		include_once( 'widgets/module-paper-monument/plugin.php' );
		include_once( 'widgets/module-issue-archives/plugin.php' );
		include_once( 'widgets/module-multi/plugin.php' );
		include_once( 'widgets/module-toc/plugin.php' );
		include_once( 'widgets/module-hero/plugin.php' );
		include_once( 'widgets/module-subscribe/plugin.php' );
		include_once( 'widgets/module-newsletter/plugin.php' );
		include_once( 'widgets/module-social/plugin.php' );
		include_once( 'widgets/module-download/plugin.php' );
	}

	function remove_widgets() {
		$wp_widgets = array(
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
		);
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
		if ( strpos( $url, '/feed/' ) !== FALSE ) {
			$force_ssl = FALSE;
		}

		return $force_ssl;
	}

	/**
	 * Change loginout text
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	function loginout_text_change( $text ) {
		$text = str_replace( 'Log in', 'Sign In', $text );
		$text = str_replace( 'Log out', 'Sign Out', $text );

		return $text;
	}

	/**
	 * Add date shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function date_shortcode( $atts, $content = NULL ) {
		return $content ? date( $content ) : date( "F d, Y", current_time( 'timestamp' ) );
	}

	/**
	 * Add Pullquote shortcode
	 */
	function article_pullquote( $which ) {
		global $post;
		$pullquotes = get_field( 'article_pullquote_repeater', $post->ID );
		$which      = intval( $which[0] ) - 1;
		$pq         = $pullquotes[ $which ]['article_pullquote'];
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
		$draft_issues    = get_posts( array(
			'post_type'      => 'toc_desc',
			'posts_per_page' => - 1,
			'post_status'    => 'draft',
			// This no longer works in WP4
			/*'orderby'			=> 'post_name',
			'order'				=> 'DESC',*/
		) );
		$draft_issue_ids = array();
		foreach ( $draft_issues as $issue ) {
			$issue_cat = get_term_by( 'name', $issue->post_title, 'issue' );
			array_push( $draft_issue_ids, $issue_cat->term_id );
		}

		$query->set(
			'tax_query',
			array(
				array(
					'taxonomy' => 'issue',
					'field'    => 'id',
					'terms'    => $draft_issue_ids,
					'operator' => 'NOT IN'
				)
			)
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
			$title[0] = N1_Magazine::format_author_name( $title[0] ) . ' ';
			$title    = implode( $sep, $title );
		} else if ( ! empty( $post->ID ) ) {
			$array = wp_get_post_terms( $post->ID, array( 'online-only' ) );
			if ( $is_scroll = ! ! reset( $array ) ) {
				$title .= "Online Only $sep ";
			} else if ( is_single() && $context_issue = N1_Magazine::Instance()->context_issue ) {
				$title .= "$context_issue->post_title $sep ";
			}
		}

		// remove 'Issues' and 'Online Only Categories' from title
		$title = str_replace( array( "Issues $sep ", "Online Only Categories $sep " ), array( '', '' ), $title );

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

N1::Instance();
