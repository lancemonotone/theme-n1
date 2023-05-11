<?php

class Utility {
	var $url_css, $path_css;

	public static function Instance() {
		static $inst = NULL;
		if ( $inst === NULL ) {
			$inst = new Utility();
		}

		return $inst;
	}

	function __construct() {
		$this->url_css  = get_stylesheet_directory_uri() . '/css';
		$this->path_css = get_stylesheet_directory() . '/css';
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_style' ) );
		add_filter( 'tiny_mce_before_init', array( &$this, 'mp6_mce_init' ), 15 );
		add_filter( 'login_redirect', array( &$this, 'redirect_to_request' ), 10, 3 );
		add_filter( 'request', array( &$this, 'feed_request' ) );
		add_filter( 'wp_head', array( &$this, 'redirect_logout_to_home' ) );
		remove_all_actions( 'do_feed_rss2' );
		add_action( 'do_feed_rss2', array( &$this, 'replace_rss2_template' ), 10, 1 );
		add_action( 'wp_footer', array( &$this, 'add_fb_conversion_pixel' ) );
		add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
	}

	function add_options_page() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page();
		}
	}

	function add_fb_conversion_pixel() {
		if ( is_front_page() ) {
			echo <<<EOD
			<!-- Facebook Pixel Code -->
			<script>
				!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
						n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
					n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
					t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
						document,'script','//connect.facebook.net/en_US/fbevents.js');
				fbq('init', '1708719166025057');
				fbq('track', "PageView");</script>
			<noscript><img height="1" width="1" style="display:none"
					src="https://www.facebook.com/tr?id=1708719166025057&ev=PageView&noscript=1"
			/></noscript>
			<!-- End Facebook Pixel Code -->
EOD;
			/**
			 * old pixel code
			 * <!-- Facebook Conversion Code for Homepage views -->
			 * <script>(function() {
			 * var _fbq = window._fbq || (window._fbq = []);
			 * if (!_fbq.loaded) {
			 * var fbds = document.createElement('script');
			 * fbds.async = true;
			 * fbds.src = '//connect.facebook.net/en_US/fbds.js';
			 * var s = document.getElementsByTagName('script')[0];
			 * s.parentNode.insertBefore(fbds, s);
			 * _fbq.loaded = true;
			 * }
			 * })();
			 * window._fbq = window._fbq || [];
			 * window._fbq.push(['track', '6030049302670', {'value':'0.00','currency':'USD'}]);
			 * </script>
			 * <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6030049302670&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" /></noscript>
			 **/
		}
	}

	function replace_rss2_template() {
		$rss_template = get_stylesheet_directory() . '/feed-rss2.php';
		if ( get_query_var( 'post_type' ) == 'article' and file_exists( $rss_template ) ) {
			load_template( $rss_template );
		} else {
			do_feed_rss2( $for_comments ); // Call default function
		}
	}

	function redirect_logout_to_home() {
		if ( is_page( 'signout' ) ) {
			$home = home_url();
			echo <<<EOD
			<script type="text/javascript">
			(function($){
				"use strict";
				$(function(){
					$('.nav-actions').hide();
				});	
			}(jQuery));
			setTimeout(function(){location.href="{$home}"} , 3000);
			</script>
EOD;
		}
	}

	function feed_request( $qv ) {
		if ( isset( $qv['feed'] ) ) {
			$qv['post_type'] = 'article';
		}

		return $qv;
	}

	function redirect_to_request( $redirect_to, $request, $user ) {
		if ( ! class_exists( 'MM_User' ) ) {
			return $request;
		}
		/**
		 * @todo Check that $request is not a 404. If so, go home.
		 */
		$member     = new MM_User( $user->ID );
		$status_map = array(
			'1' => $request,
			'2' => home_url( '/action?status=Canceled' ),
			'3' => home_url( '/action?status=Locked' ),
			'4' => home_url( '/action?status=Paused' ),
			'5' => home_url( '/action?status=Overdue' ),
			'6' => home_url( '/action?status=Pending+Activation' ),
			'7' => home_url( '/action?status=Error' ),
			'8' => home_url( '/action?status=Expired' ),
			'9' => home_url( '/action?status=Pending+Cancellation' ),
		);

		// instead of using $redirect_to we're redirecting back to $request
		if ( user_can( $user->ID, 'edit_posts' ) ) {
			$where = $redirect_to;
		} else if ( $member->getMembershipId() === "1" ) {
			// Push free members to renew when they log in.
			if ( ! $where = get_permalink( get_field( 'free_member_redirect', 'option' ) ) ) {
				// If the redirect page is not set, go to the home page.
				$where = $redirect_to;
			}
		} else if ( $request == '' ) {
			$where = $redirect_to;
		} else if ( $member->isValid() ) {
			$where = $status_map[ $member->getStatus() ];
		} else {
			$where = $request;
		}

		return $where;
	}

	// load our very own dialog.css
	function mp6_mce_init( $mce_init ) {
		$editor_css = '';
		if ( isset( $mce_init['content_css'] ) ) {
			$editor_css = $mce_init['content_css'] . ',';
		}
		$popup_css = '';
		if ( isset( $mce_init['popup_css'] ) ) {
			$popup_css = ',' . $mce_init['popup_css'];
		}
		$mce_init['content_css'] = $editor_css . $this->url_css . '/tinymce-content.css';
		$mce_init['popup_css']   = $this->url_css . '/tinymce-dialog.css' . $popup_css;

		return $mce_init;
	}

	function enqueue_admin_style() {
		$scheme = get_user_meta(
			get_current_user_id(),
			'admin_color',
			TRUE
		);
		wp_enqueue_style(
			"admin_style",
			$this->url_css . '/admin-style.css',
			array( 'wp-admin', 'ie', 'colors-' . $scheme ),
			filemtime( $this->path_css . '/admin-style.css' ),
			'all'
		);
	}

	/**
	 * Wrapper for wp_get_attachment_image_src().
	 * Returns array with URL, width, height, or false if no image.
	 *
	 * @param int $post_id
	 * @param string $size
	 *
	 * @return array or false
	 */
	static function get_featured_image( $post_id, $size = 'content-full' ) {
		$img_id   = get_post_thumbnail_id( $post_id );
		$img_meta = wp_prepare_attachment_for_js( $img_id );

		return wp_get_attachment_image_src( $img_id, $size );
	}

	/**
	 * @param array $array
	 * @param int|string $position
	 * @param mixed $insert
	 */
	function array_insert( &$array, $position, $insert ) {
		if ( is_int( $position ) ) {
			array_splice( $array, $position, 0, $insert );
		} else {
			$pos   = array_search( $position, array_keys( $array ) );
			$array = array_merge(
				array_slice( $array, 0, $pos ),
				$insert,
				array_slice( $array, $pos )
			);
		}
	}

	/**
	 * Insert an ad after the nth paragraph;
	 *
	 * @param string $content The post content
	 * @param int $position After nth paragraph (starting at 1)
	 * @param int $ad_group Which AdRotate group
	 *
	 * @return string The modified post content
	 * @uses phpQuery to traverse and manipulate HTML
	 * @uses AdRotate WP plugin
	 */
	function insert_advertisement( $content, $position, $ad_group ) {
		global $post;
		if ( ! get_field( 'hide_content_ads', $post->ID ) ) {
			if ( function_exists( 'adrotate_group' ) ) {
				require_once( 'phpQuery.php' );
				$ad = adrotate_group( $ad_group );
				$pq = phpQuery::newDocumentHTML( $content );
				phpQuery::selectDocument( $pq );
				$pq[ 'p:eq(' . ( $position - 1 ) . ')' ]->after( $ad );
				$content = $pq->html();
			}
		}

		return $content;
	}
}

Utility::instance();
?>
