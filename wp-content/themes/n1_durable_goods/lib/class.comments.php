<?php namespace N1_Durable_Goods;

class Comments {
	public function __construct() {
		add_action( 'init', [ $this, 'disable_comments_on_new_posts' ] );
		add_action( 'admin_menu', [ $this, 'remove_comments_from_dashboard' ] );
		add_filter( 'post_updated_messages', [ $this, 'remove_comments_message' ] );
		add_action( 'admin_init', [ $this, 'remove_comment_metaboxes' ] );
		add_action( 'init', [ $this, 'disable_comment_feeds' ] );
		add_action( 'init', [ $this, 'disable_comments_on_existing_posts' ] );
		add_action( 'init', [$this, 'delete_all_comments'] );
	}

	function delete_all_comments() {
		$comments = get_comments();
		foreach ( $comments as $comment ) {
			wp_delete_comment( $comment->comment_ID, TRUE );
		}
	}


	function disable_comments_on_existing_posts() {
		global $wpdb;
		// Set the comment_status of all existing posts to closed
		$wpdb->query( "UPDATE $wpdb->posts SET comment_status = 'closed'" );
		// Remove the comment and pingback post types from all existing posts
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_pingme'" );
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_encloseme'" );
	}


	function disable_comment_feeds() {
		// Disable comment feeds
		add_filter( 'feed_links_show_comments_feed', '__return_false' );
	}


	function disable_comments_on_new_posts() {
		// Disable support for comments and trackbacks on new posts
		// Note: This will only affect new posts, not existing ones
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}

	function remove_comments_from_dashboard() {
		// Remove comments from the WordPress dashboard
		remove_menu_page( 'edit-comments.php' );
	}

	function remove_comments_message( $messages ) {
		unset( $messages['post'][6] );
		unset( $messages['post'][7] );
		unset( $messages['post'][8] );
		unset( $messages['post'][9] );

		return $messages;
	}

	function remove_comment_metaboxes() {
		remove_meta_box( 'commentsdiv', 'post', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
	}
}

new Comments();
