<?php namespace N1_Durable_Goods;

/**
 * Static class for magazine functionality
 */

require_once( $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-load.php' );
require_once( $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-content/plugins/membermouse/includes/mm-constants.php' );
require_once( $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-content/plugins/membermouse/includes/init.php' );
require_once( $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-content/plugins/membermouse/includes/php_interface.php' );

class N1_Magazine {
    static bool $is_institution;
    static array $issues;
    static \WP_Post $current_issue;
    static \WP_Post $context_issue;
    static string $page_type;
    static string $page_class;

    private function __construct() {
        // $this->set_issues();
        // $this->set_current_issue();
        // $this->set_context_issue();
        // $this->set_page_type();
        $this->add_shortcodes();
    }

    function add_shortcodes() {
        add_shortcode( 'latest-issue', function () {
            return self::get_current_issue_url();
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
    static function is_institution(): bool {
        if ( isset( self::$is_institution ) ) {
            return self::$is_institution;
        }

        self::$is_institution = false;

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
                    if ( '' != $ip_clean && false !== @stristr( self::get_client_ip(), $ip_clean ) ) {
                        self::$is_institution = true;
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
    static function set_page_type() {
        global $wp_query;
        if ( is_front_page() ) {
            self::$page_type  = 'home';
            self::$page_class = 'home';
        } elseif ( self::is_issue_front() ) {
            self::$page_type  = 'magazine issue-landing';
            self::$page_class = 'magazine issue-landing';
        } elseif ( ! empty( $wp_query->query[ 'pagename' ] ) && $wp_query->query[ 'pagename' ] == 'online-only' ) {
            self::$page_type  = 'online-only-home';
            self::$page_class = 'online-only-home';
        } elseif ( is_archive() ) {
            self::$page_type  = 'archive';
            self::$page_class = 'archive';
        } elseif ( is_single() ) {
            if ( ! empty( $wp_query->query[ 'issue' ] ) ) {
                self::$page_type  = 'magazine';
                self::$page_class = 'magazine';
            } elseif ( is_preview() && ! empty( wp_get_post_terms( $_REQUEST[ 'preview_id' ], 'category' ) ) ) {
                self::$page_type  = 'magazine';
                self::$page_class = 'magazine';
            } elseif ( ! empty( $wp_query->query[ 'online-only' ] ) ) {
                if ( $wp_query->query[ 'online-only' ] == 'events' ) {
                    self::$page_type  = 'online-only';
                    self::$page_class = 'events online-only';
                } else {
                    self::$page_type  = 'online-only';
                    self::$page_class = 'online-only';
                }
            }
        } elseif ( is_page( 'magazine' ) ) {
            self::$page_type  = 'magazine landing';
            self::$page_class = 'magazine landing';
        } elseif ( isset( $wp_query->query[ 's' ] ) && '' != $wp_query->query[ 's' ] ) {
            self::$page_type  = 'search';
            self::$page_class = 'archive';
        } else {
            self::$page_type  = 'static-page';
            self::$page_class = 'static-page';
        }
    }

    static function get_page_type(): string {
        if ( ! isset( self::$page_type ) ) {
            self::set_page_type();
        }

        return self::$page_type;
    }

    static function get_page_class(): string {
        if ( ! isset( self::$page_class ) ) {
            self::set_page_type();
        }

        return self::$page_class;
    }

    /**
     * Get all issues by year and section.  This array will contain all sections and
     * years whether or not there is content associated with each.
     *
     * @return void
     */
    static function set_issues() {
        self::$issues = get_posts( [
            'post_type'      => 'toc_desc',
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        ] );
    }

    static function get_issues(): array {
        if ( ! isset( self::$issues ) ) {
            self::set_issues();
        }

        return self::$issues;
    }

    /**
     * Gets issue of magazine edition - either latest issue
     * (as set in theme options) or currently viewed issue.
     *
     * @return void
     */
    static function set_context_issue( $slug = null ) {
        global $wp_query;
        if ( ! $slug && ! empty( $wp_query->query_vars[ 'issue' ] ) ) {
            $slug = $wp_query->query_vars[ 'issue' ] ? $wp_query->query_vars[ 'issue' ] : self::get_current_issue()->post_name;
        }
        // get issue name by taxonomy slug
        $issue = self::get_issue_by_slug( $slug );

        self::$context_issue = $issue ? $issue : false;
    }

    /**
     * Set current issue as latest
     *
     */
    static function set_current_issue() {
        self::$current_issue = current( self::get_issues() );
    }

    /**
     * Do we know for a fact that we're inside an issue? Returns true or false.
     *
     * @return bool
     */
    static function is_issue_known(): bool {
        global $wp_query;

        // dissect url & query vars to get our context
        $uri = $_SERVER[ 'REQUEST_URI' ];

        if ( $uri == '/' ) {
            // front page is always the latest edition
            return true;
        }

        return (bool)$wp_query->query_vars[ 'issue' ];
    }

    /**
     * Returns posts for section.
     *
     * @param $slug string eg. 'name'
     *
     * @return false|int|WP_Post toc_desc post
     */
    static function get_issue_by_slug( $slug ) {
        $args = [
            'post_type'      => 'toc_desc',
            'post_status'    => 'publish',
            'name'           => $slug,
            'posts_per_page' => 1,
        ];

        return current( get_posts( $args ) );
    }

    /**
     * Returns link to most recent issue, as defined by the theme options.
     *
     * @return \WP_Post
     */
    static function get_context_issue(): \WP_Post {
        if ( ! isset( self::$context_issue ) ) {
            self::set_context_issue();
        }

        return self::$context_issue;
    }

    /**
     * Returns link to most recent issue, as defined by the theme options.
     *
     * @return \WP_Post
     */
    static function get_current_issue(): \WP_Post {
        if ( ! isset( self::$current_issue ) ) {
            self::set_current_issue();
        }

        return self::$current_issue;
    }

    /**
     * Returns link to most recent issue, as defined by the theme options.
     *
     * @return string
     */
    static function get_current_issue_url(): string {
        return home_url() . '/' . self::get_current_issue()->post_name . '/';
    }

    /**
     * Returns link to most recent issue, as defined by the theme options.
     *
     * @return string
     */
    static function get_context_issue_url(): string {
        return home_url() . '/' . self::get_context_issue()->post_name . '/';
    }

    /**
     * Prints post tags.
     *
     * @param int $post_id Post ID
     * @param bool $header Print header?
     */
    static function print_post_tags( int $post_id, bool $header = false ) {
        $post_tags = wp_get_post_terms( $post_id );
        if ( count( $post_tags ) ) {
            if ( $header ) {
                ?>
                <section class="post-meta-tags">
                <h4 class="post-meta-section post-meta-hed"><?php _e( 'Tags' ) ?></h4>
            <?php } ?>
            <ul class="post-meta-tags-list">
                <?php
                $all_tags = [];
                foreach ( $post_tags as $pt ) {
                    array_push( $all_tags, $pt->slug );

                    if ( $pt->slug === 'unpaywalled' ) {
                        continue;
                    } ?>

                    <li class="post-meta-tags-item">
                        <a class="tag <?= $pt->slug ?>"
                           href="<?= get_term_link( $pt, 'post' ) ?>"><?= $pt->name ?></a>
                    </li>
                <?php } ?>
                <script type="text/javascript">
                    _sf_async_config.sections = '<?= implode( ',', $all_tags ) ?>'
                </script>
            </ul>
            <?php if ( $header ) { ?>
                </section> <!-- .post-meta-tags -->
            <?php } ?>
        <?php }
    }

    static function print_social( $post_id ) {
        ?>
        <section class="post-meta-social">
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
                       href="<?= $tw_href ?>"
                       onclick="return popitup('<?= $tw_href ?>')">Twitter
                    </a>
                </li>
                <li class="post meta social">
                    <a title="Facebook"
                       class="social icon facebook"
                       href="<?= $fb_href ?>"
                       onclick="return popitup('<?= $fb_href ?>')">Facebook
                    </a>
                </li>
                <li class="post meta social">
                    <a title="Google Plus"
                       class="social icon google"
                       href="<?= $g_href ?>"
                       onclick="return popitup('<?= $g_href ?>')">Google Plus
                    </a>
                </li>
                <li class="post meta social"
                    title="Pocket">
                    <a data-pocket-label="pocket"
                       data-pocket-count="none"
                       class="pocket-btn"
                       data-lang="en"></a>
                    <script type="text/javascript">!function (d, i) {
                            if (!d.getElementById(i)) {
                                var j = d.createElement('script')
                                j.id = i
                                j.src = 'https://widgets.getpocket.com/v1/j/btn.js?v=1'
                                var w = d.getElementById(i)
                                d.body.appendChild(j)
                            }
                        }(document, 'pocket-btn-js')</script>
                </li>
                <!--<li class="post meta social"><a title="Pocket" class="social icon pocket" href="<?= $p_href ?>" onclick="return popitup('<?= $p_href ?>')">Pocket</a></li>-->
                <li class="post meta social">
                    <a title="Instapaper"
                       class="social icon instapaper"
                       href="<?= $i_href ?>"
                       onclick="return popitup('<?= $i_href ?>')">Instapaper
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
    static function is_current_issue(): bool {
        if ( self::get_current_issue()->ID === self::get_context_issue()->ID ) {
            return true;
        }

        return false;
    }

    /**
     * Is this the home page for the issue?
     *
     * @return bool
     */
    static function is_issue_front(): bool {
        global $wp_query;
        $query_array = $wp_query->query;

        return ( 1 === count( $query_array ) && array_key_exists( 'issue', $query_array ) );
    }

    static function is_paywalled( $post_id = null ): bool {
        $paywall = true;
        // If the article doesn't have a post ID, it's coming from the Multi Module
        if ( ! $post_id ) {
            $paywall = true;
        }
        // If the article has a term in the default category taxonomy (these are protected).
        if ( $post_id && count( wp_get_post_terms( $post_id, 'category' ) ) ) {
            $paywall = true;
        }
        // If the member is an institution
        if ( self::is_institution() ) {
            $paywall = false;
        }
        // If the user can edit posts.
        if ( current_user_can( 'edit_posts' ) ) {
            $paywall = false;
        }
        // If this is a MM Core page.
        if ( $post_id && \MM_CorePage::getCorePageInfo( $post_id ) ) {
            $paywall = false;
        }
        // If a member is logged in
        if ( mm_member_decision( [ "isMember" => "true", "status" => "active|pending_cancel" ] ) ) {
            $paywall = false;
        }
        // If a member is a Gift Sub Giver or Free Membership, paywall is true.
        if ( mm_member_decision( [
            'isMember'     => 'true',
            'status'       => 'active|pending_cancel',
            'membershipID' => "1|29"
        ] ) ) {
            $paywall = true;
        }
        // If the article has been tagged publicly viewable.
        if ( $post_id && get_field( 'article_free', $post_id ) ) {
            $paywall = false;
        }
        // If an article is also in an Online Only category it is not protected.
        if ( $post_id && count( wp_get_post_terms( $post_id, 'online-only' ) ) ) {
            $paywall = true;
        }
        // If an article is also in an Online Only category it is not protected.
        global $pagename;
        if ( $pagename == 'online-only' ) {
            $paywall = true;
        }
        // If the site settings force a paywall.
        if ( current_user_can( 'edit_posts' ) && get_field( 'options_force_paywall', 'options' ) ) {
            $paywall = true;
        }

        return $paywall;
    }

    /**
     * Get the client ip address
     *
     * @return string IP address
     *
     * Read more: http://techtalk.virendrachandak.com/getting-real-client-ip-address-in-php-2/#ixzz2wiVvD15e
     * Follow us: @virendrachandak on Twitter
     */
    static function get_client_ip() {
        $ip_address = false;
        if ( ! empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
            $ip_address = $_SERVER[ 'HTTP_CLIENT_IP' ];
        } elseif ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
            $ip_address = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
        } elseif ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED' ] ) ) {
            $ip_address = $_SERVER[ 'HTTP_X_FORWARDED' ];
        } elseif ( ! empty( $_SERVER[ 'HTTP_FORWARDED_FOR' ] ) ) {
            $ip_address = $_SERVER[ 'HTTP_FORWARDED_FOR' ];
        } elseif ( ! empty( $_SERVER[ 'HTTP_FORWARDED' ] ) ) {
            $ip_address = $_SERVER[ 'HTTP_FORWARDED' ];
        } elseif ( ! empty( $_SERVER[ 'REMOTE_ADDR' ] ) ) {
            $ip_address = $_SERVER[ 'REMOTE_ADDR' ];
        }

        return $ip_address;
    }

    /**
     * Return all posts for a magazine category.
     *
     * @param string $section
     * @param null $post_name
     *
     * @return array
     */
    static function get_section_posts( string $section, $post_name = null ): array {
        $args = [
            'post_type'      => 'article',
            'tax_query'      => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $section
                ],
                [
                    'taxonomy' => 'issue',
                    'field'    => 'slug',
                    'terms'    => $post_name ? $post_name : self::get_context_issue()->post_name
                ]
            ],
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'posts_per_page' => -1
        ];

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
    static function get_authors( int $id, bool $str = true, $linked = true ) {
        if ( ! $authors = wp_get_object_terms( $id, 'authors', [ 'orderby' => 'name', 'order' => 'ASC' ] ) ) {
            return false;
        }
        if ( $str ) {
            $authors_array = [];
            foreach ( $authors as &$author ) {
                $author->name = self::format_author_name( $author->name );
                $author       = $linked == true ? self::get_author_link( $author ) : $author->name;
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
    static function format_author_name( string $name ): string {
        $name = explode( '/', $name );

        $first_name = ! empty( $name[ 1 ] ) ? $name[ 1 ] : '';
        $last_name  = ! empty( $name[ 0 ] ) ? $name[ 0 ] : '';

        return trim( $first_name . ' ' . $last_name );
    }

    static function get_author_link( $author ): string {
        return '<a href="' . get_term_link( $author, 'authors' ) . '" title="' . $author->name . '">' . $author->name . '</a>';
    }

    /**
     * Display adjacent post link.
     *
     * @param string $format
     * @param string $link
     * @param bool $previous
     * @param bool $echo
     *
     * @return mixed|void|null
     */
    static function same_edition_and_section_adjacent_post_link( string $format = '&laquo; %link', string $link = '%title', bool $previous = true, bool $echo = true ) {
        global $wp_query, $wpdb, $post;

        if ( isset( $wp_query->query[ 'category_name' ] ) ) {
            $issue   = get_term_by( 'slug', $wp_query->query[ 'issue' ], 'issue' );
            $issue   = $issue->term_taxonomy_id;
            $section = get_term_by( 'slug', $wp_query->query[ 'category_name' ], 'category' );
        } else {
            $section = get_term_by( 'slug', $wp_query->query[ 'online-only' ], 'online-only' );
        }

        $query = "SELECT DISTINCT p.ID FROM {$wpdb->posts} p
        JOIN {$wpdb->term_relationships} tr1 ON p.ID = tr1.object_id
        JOIN {$wpdb->term_relationships} tr2 ON p.ID = tr2.object_id";
        if ( isset( $issue ) ) { // Online Only doesn't have an issue
            $query   .= " AND tr1.term_taxonomy_id = $issue";
            $orderby = " ORDER BY p.menu_order;";
        } elseif ( 'events' === $section->slug ) {
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
            return null;
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
     * Gets the adjacent value in an array based on given parameters.
     *
     * @param mixed $needle The value to find in the haystack.
     * @param array $haystack The array to search within.
     * @param bool $previous If true, search for the previous item; if false, search for the next item.
     * @param bool $wrap If true, wrap around to the start or end of the array when an edge is reached.
     *
     * @return string The adjacent value, or an empty string if not found.
     */
    static function get_adjacent_value( $needle, $haystack, bool $previous = true, bool $wrap = false ): string {
        // Find the index of the needle in the haystack
        $current_index = array_search( $needle, $haystack );

        // Calculate the offset based on the direction of search (previous or next)
        $offset = $previous ? -1 : 1;

        // Determine the default index if wrapping is enabled and an edge is reached
        $defaultIndex = $wrap ? ( ( $offset == -1 ? count( $haystack ) - 1 : 0 ) ) : null;

        // Calculate the new index based on the current index and offset, or set to the default index if an edge is reached
        $newIndex = ( $current_index + $offset < 0 || $current_index + $offset === count( $haystack ) ) ? $defaultIndex : $current_index + $offset;

        // Return the value at the new index if it exists, or an empty string if not
        return array_key_exists( $newIndex, $haystack ) ? $haystack[ $newIndex ] : '';
    }

}
