<?php

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/membermouse/includes/mm-constants.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/membermouse/includes/init.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/membermouse/includes/php_interface.php' );


class N1_Magazine {
	var $issues, $context_issue, $current_issue, $page_type, $page_class;
	static $is_institution;

	/**
	 * Call this method to get singleton
	 *
	 * @return N1_Magazine
	 */
	public static function Instance() {
		static $inst = NULL;
		if ( $inst === NULL ) {
			$inst = new N1_Magazine();
		}

		return $inst;
	}

	private function __construct() {
		$this->set_issues();
		$this->set_current_issue();
		$this->set_context_issue();
		$this->set_page_type();
		$this->add_shortcodes();
	}


	function add_shortcodes() {
		add_shortcode( 'latest-issue', function () {
			return $this->get_current_issue_url();
		} );
	}

	/**
	 * Check IP of client against registered IPs
	 * of Institution level members. If there is
	 * a match, paywall is defeated even if not
	 * logged in.
	 *
	 * @return bool
	 */
	static function is_institution() {
		if ( isset( self::$is_institution ) ) {
			return self::$is_institution;
		}

		self::$is_institution = FALSE;

		global $wpdb;

		$sql = /** @lang sql */
			"SELECT mmc.value
        FROM mm_custom_field_data mmc
        JOIN mm_user_data mmud
        ON mmc.user_id = mmud.wp_user_id
        WHERE mmc.custom_field_id IN(1) # IP range
        AND mmud.`status` IN (1); # active subscription";

		$institutions = $wpdb->get_results( $sql );

		foreach ( $institutions as $institution ) {
			$ips = $institution->value;
			$ips = explode( PHP_EOL, $ips );
			foreach ( $ips as $ip ) {
				if ( '' != $ip ) {
					$ip_clean = str_replace( '*', '', trim( $ip ) );
					if ( '' != $ip_clean && FALSE !== @stristr( self::get_client_ip(), $ip_clean ) ) {
						self::$is_institution = TRUE;
						break;
					}/*else{
                        echo "<!-- 1: ".$ip."-->";
                        echo "<!-- 2: ".$ip_clean."-->";
                        echo "<!-- 3: ".self::get_client_ip()."-->";
                        echo "<!-- 4: ".@stristr(self::get_client_ip(), $ip_clean)."-->\n";
                      }*/
				}
			}
		}

		return self::$is_institution;
	}

	/**
	 * Returns type of WP page (front, archive, etc).
	 */
	function set_page_type() {
		global $wp_query;
		if ( is_front_page() ) {
			$this->page_type  = 'home';
			$this->page_class = 'home';
		} else if ( self::is_issue_front() ) {
			$this->page_type  = 'magazine issue-landing';
			$this->page_class = 'magazine issue-landing';
		} else if ( ! empty( $wp_query->query['pagename'] ) && $wp_query->query['pagename'] == 'online-only' ) {
			$this->page_type  = 'online-only-home';
			$this->page_class = 'online-only-home';
		} else if ( is_archive() ) {
			$this->page_type  = 'archive';
			$this->page_class = 'archive';
		} else if ( is_single() ) {
			if ( ! empty( $wp_query->query['issue'] ) ) {
				$this->page_type  = 'magazine';
				$this->page_class = 'magazine';
			} else if ( is_preview() && ! empty( wp_get_post_terms( $_REQUEST['preview_id'], 'category' ) ) ) {
				$this->page_type  = 'magazine';
				$this->page_class = 'magazine';
			} else /*if(!empty($wp_query->query['online-only']))*/ {
				$this->page_type  = 'online-only';
				$this->page_class = 'online-only';
			}
		} else if ( is_page( 'magazine' ) ) {
			$this->page_type  = 'magazine landing';
			$this->page_class = 'magazine landing';
		} else if ( isset( $wp_query->query['s'] ) && '' != $wp_query->query['s'] ) {
			$this->page_type  = 'search';
			$this->page_class = 'archive';
		} else {
			$this->page_type  = 'static-page';
			$this->page_class = 'static-page';
		}
	}

	/**
	 * Get all issues by year and section.  This array will contain all sections and
	 * years whether or not there is content associated with each.
	 *
	 * @return void
	 */
	function set_issues() {
		$this->issues = get_posts( array(
			'post_type'      => 'toc_desc',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			// This no longer works in WP4
			/*'orderby'      => 'post_name',
			'order'        => 'DESC',*/
		) );
	}

	/**
	 * Gets issue of magazine edition - either latest issue
	 * (as set in theme options) or currently viewed issue.
	 *
	 * @return mixed array(slug, name) or false
	 */
	function set_context_issue( $slug = NULL ) {
		global $wp_query;
		if ( ! $slug && ! empty( $wp_query->query_vars['issue'] ) ) {
			$slug = $wp_query->query_vars['issue'] ? $wp_query->query_vars['issue'] : $this->current_issue->post_name;
		}
		// get issue name by taxonomy slug
		$issue = $this->get_issue_by_slug( $slug );

		$this->context_issue = $issue ? $issue : FALSE;
	}

	/**
	 * Set current issue as latest
	 *
	 */
	function set_current_issue() {
		$this->current_issue = current( $this->issues );
	}

	/**
	 * Do we know for a fact that we're inside an issue? Returns true or false.
	 *
	 * @return bool
	 */
	function is_issue_known() {
		global $wp_query;

		// dissect url & query vars to get our context
		$uri = $_SERVER['REQUEST_URI'];

		if ( $uri == '/' ) {
			// front page is always the latest edition
			return TRUE;
		}

		return $wp_query->query_vars['issue'] ? TRUE : FALSE;
	}

	/**
	 * Returns posts for section.
	 *
	 * @param $slug string eg. 'name'
	 *
	 * @return false|int|WP_Post toc_desc post
	 */
	function get_issue_by_slug( $slug ) {
		$args = array(
			'post_type'      => 'toc_desc',
			'post_status'    => 'publish',
			'name'           => $slug,
			'posts_per_page' => 1,
		);

		$posts = current( get_posts( $args ) );

		return $posts;
	}

	/**
	 * Returns link to most recent issue, as defined by the theme options.
	 *
	 * @return string
	 */
	function get_context_issue() {
		return $this->context_issue;
	}

	/**
	 * Returns link to most recent issue, as defined by the theme options.
	 *
	 * @return string
	 */
	function get_current_issue() {
		return $this->current_issue;
	}

	/**
	 * Returns link to most recent issue, as defined by the theme options.
	 *
	 * @return string
	 */
	function get_current_issue_url() {
		return home_url() . '/' . $this->current_issue->post_name . '/';
	}

	/**
	 * Returns link to most recent issue, as defined by the theme options.
	 *
	 * @return string
	 */
	function get_context_issue_url() {
		return home_url() . '/' . $this->context_issue->post_name . '/';
	}

	/**
	 * Prints post tags.
	 *
	 * @param int $post_id Post ID
	 * @param bool $header Print header?
	 */
	function print_post_tags( $post_id, $header = FALSE ) {
		$post_tags = wp_get_post_terms( $post_id );
		if ( count( $post_tags ) ) {
			if ( $header ) {
				?>
                <section class="post-meta-tags">
                <h4 class="post-meta-section post-meta-hed"><?php _e( 'Tags' ) ?></h4>
			<?php } ?>
            <ul class="post-meta-tags-list">
				<?php
				$alltags = array();
				foreach ( $post_tags as $pt ) {

					array_push( $alltags, $pt->slug );

					if ( $pt->slug === 'unpaywalled' ) {
						continue;
					} ?>


                    <li class="post-meta-tags-item">
                        <a class="tag <?php echo $pt->slug ?>"
                           href="<?php echo get_term_link( $pt, 'post' ) ?>"><?php echo $pt->name ?></a>
                    </li>
				<?php } ?>
                <script type="text/javascript">
                  _sf_async_config.sections = '<?php echo implode( ',', $alltags ) ?>';
                </script>
            </ul>
			<?php if ( $header ) { ?>
                </section> <!-- .post-meta-tags -->
			<?php } ?>
		<?php }
	}

	function print_social( $post_id ) {
		?>
        <section
                class="post-meta-social">
            <h4 class="post-meta-section post-meta-hed">Share and Save</h4>
            <ul>
				<?php
				$url     = urlencode( get_permalink( $post_id ) );
				$tw_href = 'https://twitter.com/share?via=nplusonemag&lang=en&url=' . $url;
				$fb_href = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
				$g_href  = 'https://plus.google.com/share?url=' . $url;
				$p_href  = 'https://getpocket.com/v3/add?url=' . $url;
				$i_href  = 'https://www.instapaper.com/hello2?url=' . $url;
				?>
                <li class="post meta social">
                    <a title="Twitter"
                       class="social icon twitter"
                       href="<?php echo $tw_href ?>"
                       onclick="return popitup('<?php echo $tw_href ?>')">Twitter
                    </a>
                </li>
                <li class="post meta social">
                    <a title="Facebook"
                       class="social icon facebook"
                       href="<?php echo $fb_href ?>"
                       onclick="return popitup('<?php echo $fb_href ?>')">Facebook
                    </a>
                </li>
                <li class="post meta social">
                    <a title="Google Plus"
                       class="social icon google"
                       href="<?php echo $g_href ?>"
                       onclick="return popitup('<?php echo $g_href ?>')">Google
                                                                         Plus
                    </a>
                </li>
                <li class="post meta social"
                    title="Pocket">
                    <a data-pocket-label="pocket"
                       data-pocket-count="none"
                       class="pocket-btn"
                       data-lang="en"></a>
                    <script type="text/javascript">!function(d, i) {
                        if (!d.getElementById(i)) {
                          var j = d.createElement('script');
                          j.id = i;
                          j.src = 'https://widgets.getpocket.com/v1/j/btn.js?v=1';
                          var w = d.getElementById(i);
                          d.body.appendChild(j);
                        }
                      }(document, 'pocket-btn-js');</script>
                </li>
                <!--<li class="post meta social"><a title="Pocket" class="social icon pocket" href="<?php echo $p_href ?>" onclick="return popitup('<?php echo $p_href ?>')">Pocket</a></li>-->
                <li class="post meta social">
                    <a title="Instapaper"
                       class="social icon instapaper"
                       href="<?php echo $i_href ?>"
                       onclick="return popitup('<?php echo $i_href ?>')">Instapaper
                    </a>
                </li>
            </ul>
        </section> <!-- .post-meta-social -->
	<?php }

	/**
	 * Is the context issue the current issue?
	 *
	 * @return bool
	 */
	function is_current_issue() {
		$context_issue = N1_Magazine::Instance()->context_issue;
		$current_issue = N1_Magazine::Instance()->current_issue;
		if ( $current_issue->ID === $context_issue->ID ) {
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Returns if the given ip is on the given whitelist.
	 *
	 * @param string $ip The ip to check.
	 * @param array $whitelist The ip whitelist. An array of strings.
	 *
	 * @return bool
	 */
	function isAllowedIp( $ip, array $whitelist ) {
		$ip = (string) $ip;
		if ( in_array( $ip, $whitelist, TRUE ) ) {
			// the given ip is found directly on the whitelist --allowed
			return TRUE;
		}
		// go through all whitelisted ips
		foreach ( $whitelist as $whitelistedIp ) {
			$whitelistedIp = (string) $whitelistedIp;
			// find the wild card * in whitelisted ip (f.e. find position in "127.0.*" or "127*")
			$wildcardPosition = strpos( $whitelistedIp, "*" );
			if ( $wildcardPosition === FALSE ) {
				// no wild card in whitelisted ip --continue searching
				continue;
			}
			// cut ip at the position where we got the wild card on the whitelisted ip
			// and add the wold card to get the same pattern
			if ( substr( $ip, 0, $wildcardPosition ) . "*" === $whitelistedIp ) {
				// f.e. we got
				//  ip "127.0.0.1"
				//  whitelisted ip "127.0.*"
				// then we compared "127.0.*" with "127.0.*"
				// return success
				return TRUE;
			}
		}

		// return false on default
		return FALSE;
	}

	/**
	 * Is this the home page for the issue?
	 *
	 * @return bool
	 */
	function is_issue_front() {
		global $wp_query;
		$query_array = $wp_query->query;

		return ( 1 === count( $query_array ) && array_key_exists( 'issue', $query_array ) );
	}

	function is_paywalled( $post_id = NULL ) {
		$paywall = TRUE;
		// If the article doesn't have a post ID, it's coming from the Multi Module
		if ( ! $post_id ) {
			$paywall = TRUE;
		}
		// If the article has a term in the default category taxonomy (these are protected).
		if ( $post_id && count( wp_get_post_terms( $post_id, 'category' ) ) ) {
			$paywall = TRUE;
		}
		// If the member is an institution
		if ( $this->is_institution() ) {
			$paywall = FALSE;
		}
		// If the user can edit posts.
		if ( current_user_can( 'edit_posts' ) ) {
			$paywall = FALSE;
		}
		// If this is a MM Core page.
		if ( $post_id && MM_CorePage::getCorePageInfo( $post_id ) ) {
			$paywall = FALSE;
		}
		// If a member is logged in
		if ( mm_member_decision( array( "isMember" => "true", "status" => "active|pending_cancel" ) ) ) {
			$paywall = FALSE;
		}
		// If a member is a Gift Sub Giver or Free Membership, paywall is true.
		if ( mm_member_decision( array(
			'isMember'     => 'true',
			'status'       => 'active|pending_cancel',
			'membershipID' => "1|29"
		) ) ) {
			$paywall = TRUE;
		}
		// If the article has been tagged publicly viewable.
		if ( $post_id && get_field( 'article_free', $post_id ) ) {
			$paywall = FALSE;
		}
		// If an article is also in an Online Only category it is not protected.
		if ( $post_id && count( wp_get_post_terms( $post_id, 'online-only' ) ) ) {
			$paywall = TRUE;
		}
		// If an article is also in an Online Only category it is not protected.
		global $pagename;
		if ( $pagename == 'online-only' ) {
			$paywall = TRUE;
		}
		// If the site settings force a paywall.
		if ( current_user_can( 'edit_posts' ) && get_field( 'options_force_paywall', 'options' ) ) {
			$paywall = TRUE;
		}

		return $paywall;
	}

	/**
	 * Get the client ip address
	 *
	 * @return unknown
	 *
	 * Read more: http://techtalk.virendrachandak.com/getting-real-client-ip-address-in-php-2/#ixzz2wiVvD15e
	 * Follow us: @virendrachandak on Twitter
	 */
	static function get_client_ip() {
		$ipaddress = FALSE;
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if ( ! empty( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if ( ! empty( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if ( ! empty( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}

		return $ipaddress;
	}

	/**
	 * Return all posts for a magazine category.
	 *
	 * @param string $section
	 *
	 * @return
	 */
	function get_section_posts( $section, $post_name = NULL ) {
		$args = array(
			'post_type'      => 'article',
			'tax_query'      => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $section
				),
				array(
					'taxonomy' => 'issue',
					'field'    => 'slug',
					'terms'    => $post_name ? $post_name : $this->context_issue->post_name
				)
			),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => - 1
		);

		return get_posts( $args );
	}

	/**
	 * Return list or array of article authors
	 *
	 * @param int $id post ID
	 * @param bool $str output string
	 *
	 * @return string formatted, linked author names or false
	 */
	function get_authors( $id, $str = TRUE, $linked = TRUE ) {
		if ( ! $authors = wp_get_object_terms( $id, 'authors', array( 'orderby' => 'name', 'order' => 'ASC' ) ) ) {
			return FALSE;
		}
		if ( $str ) {
			$authors_array = array();
			foreach ( $authors as &$author ) {
				$author->name = self::format_author_name( $author->name );
				$author       = $linked == TRUE ? self::get_author_link( $author ) : $author->name;
				array_push( $authors_array, $author );
			}
			$authors = implode( ', ', $authors_array );
		}

		return $authors;
	}

	/**
	 * Corrects Last/First to First Last.
	 *
	 * @param string $name
	 *
	 * @return string Author's name.
	 */
	function format_author_name( $name ) {
		$name = explode( '/', $name );

		$first_name = ! empty( $name[1] ) ? $name[1] : '';
		$last_name  = ! empty( $name[0] ) ? $name[0] : '';

		return trim( $first_name . ' ' . $last_name );
	}

	function get_author_link( $author ) {
		return '<a href="' . get_term_link( $author, 'authors' ) . '" title="' . $author->name . '">' . $author->name . '</a>';
	}

	/**
	 * Get magazine Table of Contents
	 *
	 */
	function print_issue_toc() {
		?>
        <div class="issue-header cf">
            <h1><?php echo $this->context_issue->post_title ?></h1>
            <h2><?php echo get_field( 'issue_name', $this->context_issue->ID ) ?></h2>
        </div>
		<?php
		if ( $toc_post = $this->get_issue_by_slug( $this->context_issue->post_title ) ) {
			if ( get_field( 'issue_sections', $toc_post->ID ) ) {
				?>
                <ul class="toc-sections cf"><?php
					$first_section = TRUE;
					while ( the_repeater_field( 'issue_sections', $toc_post->ID ) ) {
						$section       = get_sub_field( 'issue_section' );
						$section_posts = $this->get_section_posts( $section->slug );
						?>
                        <li class="cf">
                            <h3 class="toc-section">
                                <!--<a class="issue-link" href="<?php echo home_url() . '/' . $this->context_issue->post_name . '/' . $section->slug ?>">-->
								<?php echo $section->name ?>
                                <!--</a>-->
                            </h3>
							<?php foreach ( $section_posts as $sp ) { ?>
                                <div class="toc-article">
                                    <p class="toc-author"><?php echo $this->get_authors( $sp->ID ) ?></p>
                                    <h4>
                                        <a href="<?php echo get_permalink( $sp->ID ) ?>"
                                           title="<?php echo $sp->post_title ?>"><?php echo $sp->post_title ?></a>
                                    </h4>
                                    <h5><?php echo get_field( 'article_subhead', $sp->ID ) ?></h5>
									<?php echo apply_filters( 'the_content', $sp->post_excerpt ) ?>
                                </div><!-- .issue_article -->
							<?php } ?>
                        </li>
						<?php if ( $first_section === TRUE ) {// PRINT SOCIAL SIGNUP ?>
                            <li class="entry-content">
                                <img src="http://placehold.it/400x150/e8e8e8/ffffff/&text=Social+Signup"
                                     class="aligncenter"
                                     alt=""/>
                            </li>
							<?php
						}
						$first_section = FALSE;
					} ?>
                </ul><!-- .toc-sections --><?php
			}
		}
	}

	/**
	 * Display adjacent post link.
	 *
	 * @param string $format
	 * @param string $link
	 * @param bool $previous
	 */
	function same_edition_and_section_adjacent_post_link( $format = '&laquo; %link', $link = '%title', $previous = TRUE, $echo = TRUE ) {
		global $wp_query, $wpdb, $post;

		if ( isset( $wp_query->query['category_name'] ) ) {
			$issue   = get_term_by( 'slug', $wp_query->query['issue'], 'issue' );
			$issue   = $issue->term_taxonomy_id;
			$section = get_term_by( 'slug', $wp_query->query['category_name'], 'category' );
		} else {
			$section = get_term_by( 'slug', $wp_query->query['online-only'], 'online-only' );
		}

		$query = "SELECT DISTINCT p.ID FROM {$wpdb->posts} p
        JOIN {$wpdb->term_relationships} tr1 ON p.ID = tr1.object_id
        JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id";
		if ( isset( $issue ) ) { // Online Only doesn't have an issue
			$query   .= " AND tr1.term_taxonomy_id = $issue";
			$orderby = " ORDER BY p.menu_order;";
		} else if ( 'events' === $section->slug ) {
			$query   .= " JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)";
			$query   .= " WHERE pm.meta_key = 'event_date'";
			$orderby = " ORDER BY STR_TO_DATE(pm.meta_value, '%Y%m%d') ASC";
		} else { // The Magazine doesn't care about categories
			$query   .= ! empty( $section ) ? " AND tr2.term_taxonomy_id = {$section->term_taxonomy_id}" : '';
			$orderby = " ORDER BY p.post_date ASC;";
		}

		$query .= " AND p.post_status = 'publish'";
		$query .= $orderby;

		$results = $wpdb->get_col( $query );

		if ( ! $id = self::get_adjacent_value( $post->ID, $results, $previous ) ) {
			return NULL;
		}

		$rel = $previous ? 'prev' : 'next';

		$title  = get_the_title( $id );
		$string = '<a href="' . get_permalink( $id ) . '" rel="' . $rel . '"><p class=" article-title">';
		$link   = str_replace( '%title', $title, $link );
		$link   = $string . $link . '</p></a>';

		$format = str_replace( '%link', $link, $format );

		$adjacent = $previous ? 'previous' : 'next';
		$the_link = apply_filters( "{$adjacent}_post_link", $format, $link );

		if ( $echo ) {
			echo $the_link;
		} else {
			return $the_link;
		}
	}

	/**
	 * Get next and previous elements in an array
	 *
	 * @param mixed $needle
	 * @param mixed $haystack
	 * @param bool $previous
	 *
	 * @return string Next/Previous element or null if no value
	 */
	static function get_adjacent_value( $needle, $haystack, $previous = TRUE, $wrap = FALSE ) {
		$current_index = array_search( $needle, $haystack );

		// Find the index of the next/prev items
		if ( $previous ) {
			if ( $wrap ) {
				$output = $haystack[ ( $current_index - 1 < 0 ) ? count( $haystack ) - 1 : $current_index - 1 ];
			} else {
				$output = $haystack[ ( $current_index - 1 < 0 ) ? NULL : $current_index - 1 ];
			}
		} else {
			if ( $wrap ) {
				$output = $haystack[ ( $current_index + 1 == count( $haystack ) ) ? 0 : $current_index + 1 ];
			} else {
				$output = $haystack[ ( $current_index + 1 == count( $haystack ) ) ? NULL : $current_index + 1 ];
			}
		}

		return $output;
	}
}
