<?php
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

class Module_Multi extends WP_Widget {
	var $version;
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
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// TODO:	update classname and description
		// TODO:	replace 'Module_Multi' to be named more plugin specific. Other instances exist throughout the code, too.
		parent::__construct(
			'Module_Multi',
			__( 'Multi Module', 'Module_Multi' ),
			array(
				'classname'   => 'module-multi',
				'description' => __( 'More By this Author, More from Issue [N], Related Content, Featured Article, and Online Only widgets.',
					'Module_Multi' )
			)
		);

		// Register admin styles and scripts
		//add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		//add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Register AJAX
		add_action( "wp_ajax_get_multi_posts", array( $this, 'get_multi_posts' ) );
		add_action( "wp_ajax_nopriv_get_multi_posts", array( $this, 'get_multi_posts' ) );

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

		echo $args['before_widget'];

		// TODO:	Here is where you manipulate your widget's values based on their input fields
		include( plugin_dir_path( __FILE__ ) . '/lib/functions.php' );
		include( plugin_dir_path( __FILE__ ) . '/views/widget-multi.php' );

		echo $args['after_widget'];

	} // end widget

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array    new_instance    The new instance of values to be generated via the update.
	 * @param array    old_instance    The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance                     = $old_instance;
		$instance['title']            = strip_tags( $new_instance['title'] );
		$instance['subtitle']         = strip_tags( $new_instance['subtitle'] );
		$instance['flavor']           = strip_tags( $new_instance['flavor'] );
		$instance['taxonomy']         = strip_tags( $new_instance['taxonomy'] );
		$instance['term']             = strip_tags( $new_instance['term'] );
		$instance['number']           = strip_tags( $new_instance['number'] );
		$instance['ad_after']         = strip_tags( $new_instance['ad_after'] );
		$instance['newsletter_after'] = strip_tags( $new_instance['newsletter_after'] );
		$instance['social_after']     = strip_tags( $new_instance['social_after'] );
		$instance['order']            = strip_tags( $new_instance['order'] );
		$instance['orderby']          = strip_tags( $new_instance['orderby'] );
		$instance['infinite']         = strip_tags( $new_instance['infinite'] );

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

		load_plugin_textdomain( 'Module_Multi', FALSE, plugin_dir_path( __FILE__ ) . '/lang/' );

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

		wp_enqueue_script( 'module-multi-admin-script', get_stylesheet_directory_uri() . '/widgets/module-multi/js/admin.js', array( 'jquery' ) );

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
		wp_register_script( 'module-multi-script', get_stylesheet_directory_uri() . '/widgets/module-multi/js/widget.js', array( 'jquery' ), $this->version, TRUE );
		wp_localize_script( 'module-multi-script', 'modmulti', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
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
	function get_multi_posts( $flavor = 'online-only', $number = - 1, $ad_after = 0, $order = 'DESC', $orderby = 'date', $newsletter_after = 0, $social_after = 0, $taxonomy = NULL, $term = NULL, $meta_key = NULL ) {
		// $flavor = $_REQUEST['flavor'] ? $_REQUEST['flavor'] : $flavor;
		// $taxonomy = $_REQUEST['taxonomy'] ? $_REQUEST['taxonomy'] : $taxonomy;
		// $term = $_REQUEST['term'] ? $_REQUEST['term'] : $term;
		// $number = $_REQUEST['number'] ? intval($_REQUEST['number']) : intval($number);
		// $ad_after = $_REQUEST['ad_after'] ? intval($_REQUEST['ad_after']) : intval($ad_after);
		// $newsletter_after = $_REQUEST['newsletter_after'] ? intval($_REQUEST['newsletter_after']) : intval($newsletter_after);
		// $social_after = $_REQUEST['social_after'] ? intval($_REQUEST['social_after']) : intval($social_after);
		// $order = $_REQUEST['order'] ? $_REQUEST['order'] : $order;
		// $orderby = $_REQUEST['orderby'] ? $_REQUEST['orderby'] : $orderby;
		// $paged = $_REQUEST['paged'];
		// $meta_key = $_REQUEST['meta_key'] ? $_REQUEST['meta_key'] : $meta_key;

		$flavor           = isset( $_REQUEST['flavor'] ) ? $_REQUEST['flavor'] : $flavor;
		$taxonomy         = isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : $taxonomy;
		$term             = isset( $_REQUEST['term'] ) ? $_REQUEST['term'] : $term;
		$number           = isset( $_REQUEST['number'] ) ? intval( $_REQUEST['number'] ) : $number;
		$ad_after         = isset( $_REQUEST['ad_after'] ) ? intval( $_REQUEST['ad_after'] ) : $ad_after;
		$newsletter_after = isset( $_REQUEST['newsletter_after'] ) ? intval( $_REQUEST['newsletter_after'] ) : $newsletter_after;
		$social_after     = isset( $_REQUEST['social_after'] ) ? intval( $_REQUEST['social_after'] ) : $social_after;
		$order            = isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : $order;
		$orderby          = isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : $orderby;
		$paged            = isset( $_REQUEST['paged'] ) ? intval( $_REQUEST['paged'] ) : NULL;
		$meta_key         = isset( $_REQUEST['meta_key'] ) ? $_REQUEST['meta_key'] : $meta_key;


		$flavor_args = array();
		switch ( $flavor ) {
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

		// default args
		$default_args = array(
			'post_type'      => 'article',
			'post_status'    => 'publish',
			'posts_per_page' => $number,
			'order'          => $order,
			'orderby'        => $orderby,
			'meta_key'       => $meta_key,
			'paged'          => $paged,
		);

		$this->multi_query = new WP_Query( array_merge( (array) $flavor_args, (array) $default_args ) );
		$the_posts         = $this->multi_query->posts;

		// is this is an ajax call?
		global $pagenow;
		if ( $pagenow == 'admin-ajax.php' ) {
			if ( count( $the_posts ) ) {
				$response['type'] = 'success';
				ob_start();
				$this->print_multi_posts( $the_posts, $ad_after, $flavor, $newsletter_after, $social_after );
				$response['content'] = ob_get_clean();
			} else {
				$response['type']    = 'fail';
				$response['content'] = "<p><small>" . __( 'end of transmission.' ) . "</small></p>";
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

	function print_multi_posts( $the_posts, $ad_after, $flavor, $newsletter_after, $social_after ) {
		$ad_after         = $ad_after == 0 ? FALSE : intval( $ad_after );
		$newsletter_after = $newsletter_after == 0 ? FALSE : intval( $newsletter_after );
		$social_after     = $social_after == 0 ? FALSE : intval( $social_after );
		$post_counter     = 0;
		foreach ( $the_posts as $the_p ) {
			if ( $post_counter === $ad_after ) {
				if ( N1_Magazine::Instance()->is_paywalled() ) {
					echo adrotate_group( 1 );
				}
			}
			if ( $post_counter === $newsletter_after ) {
				the_widget( 'Module_Newsletter' );
			}
			if ( $post_counter === $social_after ) {
				the_widget( 'Module_Social' );
			}
			$this->print_post( $flavor, $the_p );

			$post_counter ++;
		}
	}

	function print_post( $flavor, $the_p ) {
		// Boy, this function escalated quickly.
		// Try to get the category of the post
		$array        = wp_get_post_terms( $the_p->ID, 'issue' );
		$section      = reset( $array );
		$taxonomy     = 'category';
		$article_type = 'magazine';
		// If there is no category, then it might be an Online Only post.
		if ( empty( $section ) ) {
			$sections = wp_get_post_terms( $the_p->ID, 'online-only' );
			if ( count( $sections ) > 1 ) {
				$section = $sections[1];
			} else {
				$section = $sections[0];
			}
			$taxonomy     = 'online-only';
			$article_type = 'online-only';
		}
		// If it's not in the scroll it must be a page.
		if ( empty( $section ) ) {
			$article_type = 'page';
		}
		$flags = $this->get_flags( $flavor, $the_p, $section );
		// get the teaser style
		$format = get_field( 'article_teaser_format', $the_p->ID );
		$format = $format ? $format : 'no_image';
		// cache content for use later
		$content  = $this->get_content( $the_p, $flavor, $format );
		$authors  = N1_Magazine::get_authors( $the_p->ID, TRUE, TRUE );
		$featured = ! is_search() && get_field( 'article_featured', $the_p->ID ) ? 'featured' : '';
		$subhead  = get_field( 'article_subhead', $the_p->ID );
		switch ( $flavor ) {
			case 'archive':
			case 'sticky':
				// Force excerpt display on search results page.
				if ( N1_Magazine::Instance()->page_type == 'search' ) {
					$format = '';
				}
				switch ( $format ) {
					case 'pullquote':
						?>
                        <article
                                class="post tweetquote <?php echo $article_type ?> term-<?php echo $section->slug ?>">
							<?php $this->print_post_head( $the_p, $article_type, $section, $authors ) ?>
							<?php echo $section->name ? '<p class="post-category"><a href="' . get_term_link( $section, $taxonomy ) . '">' . $section->name . '</a></p>' : '' ?>
                            <a href="<?php echo get_permalink( $the_p->ID ) ?>"><?php echo $content ?></a>
							<?php N1_Magazine::Instance()->print_post_tags( $the_p->ID ); ?>
                            <h1 class="post-title">
                                <a
                                        href="<?php echo get_permalink( $the_p->ID ) ?>"><?php echo $the_p->post_title ?></a>
                            </h1>
							<?php echo $authors ? '<p class="post-author">' . $authors . '</p>' : '' ?>
                            <div class="jump">
                                <a href="<?php echo get_permalink( $the_p->ID ) ?>"
                                   class="jump"><?php _e( 'Read More' ) ?></a>
                            </div>
							<?php edit_post_link( __( 'Edit' ), '<span class="edit-link">', '</span>', $the_p->ID ); ?>
                        </article>
						<?php break;
					default:
						?>
                        <article
                                class="post <?php echo $featured ?> <?php echo $article_type ?> term-<?php echo $section->slug ?>">
							<?php $this->print_post_head( $the_p, $article_type, $section, $authors ) ?>
							<?php if ( $article_type == 'magazine' && ( ! ! $featured || $format == 'with_image' ) ) {
								$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_p->ID ), 'content-full' );
								if ( is_array( $img_src ) ) {
									?>
                                    <figure class="post-figure">
                                        <img class="post-figure"
                                             src="<?php echo $img_src[0] ?>"
                                             alt="<?php echo $the_p->post_title ?>"/>
                                    </figure>
								<?php }
							} else {
								if ( $format == 'with_image' ) { ?>
                                    <a
                                            href="<?php echo get_permalink( $the_p->ID ) ?>"><?php echo $content ?></a>
								<?php }
							} ?>
							<?php echo $section->name ? '<p class="post-category"><a href="' . get_term_link( $section, $taxonomy ) . '">' . $section->name . '</a></p>' : '' ?>
							<?php echo $authors ? '<p class="post-author">' . $authors . '</p>' : '' ?>
                            <h1 class="post-title">
                                <a
                                        href="<?php echo get_permalink( $the_p->ID ) ?>"><?php echo $the_p->post_title ?></a>
                            </h1>
							<?php N1_Magazine::Instance()->print_post_tags( $the_p->ID ); ?>
                            <p class="post-dek"><?php echo $subhead ?></p>

                            <div
                                    class="post-excerpt"><?php echo apply_filters( 'the_excerpt', $the_p->post_excerpt ) ?></div>
                            <div class="jump">
                                <a href="<?php echo get_permalink( $the_p->ID ) ?>"
                                   class="jump"><?php _e( 'Read More' ) ?></a>
                            </div>
							<?php edit_post_link( __( 'Edit' ), '<span class="edit-link">', '</span>', $the_p->ID ); ?>
                        </article><!-- /.post -->
						<?php break;
				}
				break;
			default:
				$the_tax = $section->taxonomy == 'category' ? 'magazine' : $section->taxonomy ?>
                <div
                        class="module article <?php echo $format ?> <?php echo $the_tax ?> term-<?php echo $section->slug ?>">
                    <a class="module article wrapper"
                       href="<?php echo get_permalink( $the_p->ID ) ?>">
						<?php echo $flags; ?>
						<?php echo $content; ?>
                        <ul class="module article meta article-info">
                            <li class="module article meta category"><?php echo $section->name ?></li>
                            <li class="module article meta title"><?php echo $the_p->post_title ?></li>
                            <li
                                    class="module article meta author"><?php echo N1_Magazine::Instance()->get_authors( $the_p->ID, TRUE, FALSE ) ?></li>
                        </ul>
                    </a>
					<?php edit_post_link( __( 'Edit' ), '<span class="edit-link">', '</span>', $the_p->ID ); ?>
                </div><!-- .module.article -->
				<?php break;
		}
	}

	function get_content( $the_p, $flavor, $format ) {
		switch ( $flavor ) {
			case 'archive':
			case 'online-only-home':
			case 'sticky':
				$class    = 'post-figure';
				$img_size = 'content-full';
				break;
			default:
				$class    = 'module article thumbnail';
				$img_size = 'multi-module';
				break;
		}
		switch ( $format ) {
			case 'with_image':
				$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_p->ID ), $img_size );
				if ( is_array( $img_src ) ) {
					$content = '<figure class="' . $class . '"><img class="' . $class . '" src="' . $img_src[0] . '" alt="' . $the_p->post_title . '" /></figure>';
				} else {
					$content = '';
				}
				break;
			case 'no_image':
				$content = '';
				break;
			case 'pullquote':
				$found = FALSE;
				$start = '<p class="module article tweetquote">';
				$end   = '</p>';
				if ( $pullquotes = get_field( 'article_pullquote_repeater', $the_p->ID ) ) {
					foreach ( $pullquotes as $pullquote ) {
						if ( $pullquote['article_pullquote_featured'] ) {
							$quote = $pullquote['article_pullquote'];
							$found = TRUE;
							break;
						}
					}
					if ( ! $found ) {
						$quote = $pullquotes[0]['article_pullquote'];
					}
				}
				$quote   = $quote != '' ? $quote : $the_p->post_excerpt;
				$content = $start . $this->shorten_str_by_word( $quote, 130 ) . $end;
				$content = apply_filters( 'the_excerpt', $content );
				break;
			default:

		}

		return $content;
	}

	function print_post_head( $the_p, $article_type, $section, $authors ) {
		switch ( $article_type ) {
			case 'online-only':
				$date = $section->slug == 'events' ? get_field( 'event_date', $the_p->ID ) : $the_p->post_date ?>
                <p class="post-date"><?php echo date( 'F j, Y', strtotime( $date ) ) ?></p>
				<?php break;
			case 'magazine':
				$issue = N1_Magazine::get_issue_by_slug( $section->slug );
				$issue_art = get_field( 'issue_art', $issue->ID );
				?>
                <div class="issue-icon">
                    <figure class="issue thumb">
                        <a href="<?php echo home_url() ?>/magazine/<?php echo $issue->post_name ?>">
                            <img src="<?php echo $issue_art['sizes']['issue-art'] ?>"
                                 alt="<?php _e( 'Art for' ) ?> <?php echo $issue->post_title ?>">
                        </a>
                    </figure>
                </div><!-- .issue-icon -->
				<?php break;
			case 'page':
				?>

				<?php break;
		}
	}

	function get_flags( $flavor, $the_p, $section ) {
		$online_terms   = wp_get_post_terms( $the_p->ID, array( 'online-only' ) );
		$is_scroll_post = ! ! reset( $online_terms );
		$issue_terms    = wp_get_post_terms( $the_p->ID, array( 'issue' ) );
		$is_issue       = ! ! reset( $issue_terms );
		if ( $is_scroll_post ) {
			$date  = $section->slug == 'events' ? get_field( 'event_date', $the_p->ID ) : $the_p->post_date;
			$flags = '<div class="flags"><span class="date">' . date( 'F j, Y', strtotime( $date ) ) . '</span></div>';
		} elseif ( $is_issue ) {
			$issue     = reset( $issue_terms );
			$issue_obj = N1_Magazine::get_issue_by_slug( $issue->slug );
			$flags     = '<div class="flags">
					<span class="issuenumber">' . $issue_obj->post_title . '</span>
					<span class="issuetitle">' . get_field( 'issue_name', $issue_obj->ID ) . '</span>
				</div>';
		}

		return $flags;
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
	function get_sticky_args( $taxonomy, $term ) {
		$st = array();
		if ( $term ) {
			array_push( $st, $term );
		} else {
			foreach ( get_terms( $taxonomy ) as $temp ) {
				array_push( $st, $temp->slug );
			}
		}
		$sticky_posts = get_option( 'sticky_posts' );

		return array(
			'posts_per_page'      => - 1,
			'post__in'            => $sticky_posts,
			'ignore_sticky_posts' => 1,
			'tax_query'           => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $st,
					'operator' => 'IN'
				)
			)
		);
	}

	/**
	 * Returns array of args to retrieve posts related by tag.
	 */
	function get_tag_args() {
		global $post;

        $id = !empty($post) ? $post->ID : 0;

		$tags = array();
		foreach ( wp_get_post_terms( $id, 'post_tag' ) as $temp ) {
			array_push( $tags, $temp->term_id );
		}

		return array(
			'post__not_in' => array( $id ),
			'tax_query'    => array(
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'id',
					'terms'    => $tags,
					'operator' => 'IN'
				)
			)
		);
	}

	/**
	 * Returns array of args to retrieve posts in current issue.
	 */
	function get_issue_args() {
		global $post;

		$id = !empty($post) ? $post->ID : 0;

		$context_issue = N1_Magazine::Instance()->get_context_issue();

		return array(
			'post__not_in' => array( $id ),
			'tax_query'    => array(
				array(
					'taxonomy' => 'issue',
					'field'    => 'slug',
					'terms'    => $context_issue->post_name,
					'operator' => 'IN'
				)
			)
		);
	}

	/**
	 * Returns array of args to retrieve posts by current
	 * article's author.
	 */
	function get_author_args() {
		global $post;

		$id = !empty($post) ? $post->ID : 0;

		$at = array();
		foreach ( wp_get_post_terms( $id, 'authors' ) as $temp ) {
			array_push( $at, $temp->term_id );
		}

		return array(
			'post__not_in' => array( $id ),
			'tax_query'    => array(
				array(
					'taxonomy' => 'authors',
					'field'    => 'id',
					'terms'    => $at,
					'operator' => 'IN'
				)
			)
		);
	}

	/**
	 * Returns array of args to retrieve posts in the
	 * Online Only.
	 *
	 */
	function get_scroll_args() {
		global $post;

		$id = !empty($post) ? $post->ID : 0;

		$st = array();
		foreach ( get_terms( 'online-only' ) as $temp ) {
			array_push( $st, $temp->term_id );
		}

		return array(
			'post__not_in' => array( $id ),
			'tax_query'    => array(
				array(
					'taxonomy' => 'online-only',
					'field'    => 'id',
					'terms'    => $st,
					'operator' => 'IN'
				)
			)
		);
	}

	/**
	 * Returns array of args to retrieve posts by taxonomy & term.
	 *
	 */
	function get_archive_args( $taxonomy, $term ) {
		$st = array();
		if ( $term ) {
			array_push( $st, $term );
		} else {
			foreach ( get_terms( $taxonomy ) as $temp ) {
				array_push( $st, $temp->slug );
			}
		}

		return array(
			'post__not_in' => get_option( 'sticky_posts' ),
			'tax_query'    => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $st,
					'operator' => 'IN'
				)
			)
		);
	}

	/**
	 * Returns array of args to retrieve posts by authors
	 * who have articles published in the current issue.
	 *
	 */
	function get_featured_author_args() {
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
							WHERE t2.slug = '" . N1_Magazine::Instance()->context_issue->post_name . "'
						)
					)  
				) as f
				GROUP BY f.author
				ORDER BY f.post_date DESC;";
		$posts_in = $wpdb->get_col( $qry );

		$context_issue = N1_Magazine::Instance()->get_context_issue();

		return array(
			'post__in'  => $posts_in,
			'tax_query' => array(
				array(
					'taxonomy' => 'issue',
					'field'    => 'slug',
					'terms'    => $context_issue,
					'operator' => 'NOT IN'
				)
			)
		);
	}

	/**
	 * Returns array of args to retrieve Featured Articles
	 * which are NOT in the 'online-only' taxonomy.
	 *
	 * @return unknown
	 */
	function get_featured_default_args() {
		$context_issue = N1_Magazine::Instance()->get_context_issue();
		$st            = array();
		foreach ( get_terms( 'online-only' ) as $temp ) {
			array_push( $st, $temp->term_id );
		}

		return array(
			'meta_query' => array(
				array(
					'key'     => 'article_featured',
					'value'   => '1',
					'compare' => '=',
				)
			),
			'tax_query'  => array(
				array(
					'taxonomy' => 'online-only',
					'field'    => 'id',
					'terms'    => $st,
					'operator' => 'NOT IN'
				),
				array(
					'taxonomy' => 'issue',
					'field'    => 'slug',
					'terms'    => $context_issue,
					'operator' => 'IN'
				)
			)
		);
	}

} // end class

add_action( 'widgets_init', function () {
	register_widget( "Module_Multi" );
} );
