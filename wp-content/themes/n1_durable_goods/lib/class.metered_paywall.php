<?php namespace N1_Durable_Goods;

class Metered_Paywall {
    static bool $meter_enabled = false;
    static bool $meter_reached = false;
    static string $metered_message = 'Init metering.';
    private static array $is_paywalled = [];

    public function __construct() {
        add_action( 'admin_bar_menu', [ $this, 'add_reset_metered_paywall_button' ], 999 );
        add_action( 'wp_ajax_reset_metered_paywall', [ $this, 'reset_metered_paywall_handler' ] ); // For logged-in users
        add_action( 'wp_ajax_nopriv_reset_metered_paywall', [ $this, 'reset_metered_paywall_handler' ] ); // For logged-out users
        add_action( 'wp_footer', [ $this, 'print_inline_script' ] ); // Print inline JavaScript
    }

    public function add_reset_metered_paywall_button( $wp_admin_bar ) {
        if ( current_user_can( 'edit_posts' ) ) {
            $args = [
                'id'     => 'reset-metered-paywall',
                'title'  => '<a href="#" class="reset-metered-paywall-button">Reset Meter</a>',
                'parent' => false,
                'meta'   => [ 'html' => false ], // Allow HTML in the title
            ];
            $wp_admin_bar->add_node( $args );
        }
    }

    public function print_inline_script() {
        $nonce = wp_create_nonce( 'reset-metered-paywall' ); // Create nonce for security
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                const resetButton = document.querySelector('.reset-metered-paywall-button')
                if (resetButton) {
                    resetButton.addEventListener('click', function (e) {
                        e.preventDefault()

                        var xhr = new XMLHttpRequest()
                        xhr.open('POST', '<?php echo admin_url( 'admin-ajax.php' ); ?>', true)
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
                        xhr.onload = function () {
                            if (xhr.status >= 200 && xhr.status < 400) {
                                location.reload() // Refresh the page if the AJAX call is successful
                            } else {
                                alert('Failed to reset visits. Please try again.')
                            }
                        }
                        xhr.send('action=reset_metered_paywall&nonce=<?php echo $nonce; ?>')
                    })
                }
            })
        </script>
        <?php
    }

    public function reset_metered_paywall_handler() {
        check_ajax_referer( 'reset-metered-paywall', 'nonce' ); // Verify nonce
        self::reset_session_for_testing();
        wp_send_json_success(); // Send success response
    }

    static function reset_session_for_testing() {
        // Unset all session variables
        $_SESSION = [];

        // If a session cookie exists, destroy it
        if ( ini_get( "session.use_cookies" ) ) {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 42000,
                $params[ "path" ], $params[ "domain" ],
                $params[ "secure" ], $params[ "httponly" ]
            );
        }

        // Finally, destroy the session
        session_destroy();

        // Optionally, you might want to start a new session immediately
        session_start();
    }

    static function is_meter_enabled(): bool {
        return self::$meter_enabled;
    }

    static function get_meter_reached(): bool {
        return self::$meter_reached;
    }

    static function get_metered_message(): string {
        return self::$metered_message;
    }

    /**
     * This must be called before any output is sent to the browser.
     */
    static function set_meter_reached(): void {
        global $post;

        if ( ! Metered_Paywall::is_paywalled( $post->ID ) ) {
            self::$meter_reached   = false;
            self::$metered_message = "Article is not paywalled.";

            return;
        }

        // get ACF option enable_metered_paywall
        if ( ! self::$meter_enabled = get_field( 'enable_metered_paywall', 'option' ) ) {
            self::$meter_reached   = true;
            self::$metered_message = "Metered paywall is disabled.";

            return;
        }

        if ( session_status() === PHP_SESSION_DISABLED ) {
            // Sessions are not available, set a default message
            self::$meter_reached   = false;
            self::$metered_message = "Sessions are not enabled, so metering is unavailable.";

            return;
        }

        if ( session_status() === PHP_SESSION_NONE ) {
            ini_set( 'session.cookie_lifetime', 30 * 24 * 60 * 60 );
            session_start();
        }

        // Set a secret string to make the session more obscure
        $secret = __NAMESPACE__;

        // Determine the expiration time
        if ( ! isset( $_SESSION[ 'expiration_time' ] ) ) {
            $_SESSION[ 'expiration_time' ] = time() + ( 30 * 24 * 60 * 60 ); // 30 days from now
        }

        // Check if the session has expired
        if ( time() > $_SESSION[ 'expiration_time' ] ) {
            session_destroy();
            session_start();
            $_SESSION[ 'expiration_time' ] = time() + ( 30 * 24 * 60 * 60 );
        }

        // Validate IP and User-Agent
        if ( ! isset( $_SESSION[ 'ip' ] ) || $_SESSION[ 'ip' ] !== $_SERVER[ 'REMOTE_ADDR' ] ||
             ! isset( $_SESSION[ 'user_agent' ] ) || $_SESSION[ 'user_agent' ] !== $_SERVER[ 'HTTP_USER_AGENT' ] ) {
            session_destroy();
            session_start();
            $_SESSION[ 'ip' ]              = $_SERVER[ 'REMOTE_ADDR' ];
            $_SESSION[ 'user_agent' ]      = $_SERVER[ 'HTTP_USER_AGENT' ];
            $_SESSION[ 'expiration_time' ] = time() + ( 30 * 24 * 60 * 60 );
        }

        // Get the visited articles from the session, or initialize an empty array
        $visited_articles_key = md5( $secret );
        $visited_articles     = $_SESSION[ $visited_articles_key ] ?? [];

        // Check if the current article has been visited
        if ( ! in_array( $post->ID, $visited_articles ) ) {
            $visited_articles[]                = $post->ID; // Add the current article to the visited list
            $_SESSION[ $visited_articles_key ] = $visited_articles;
        }

        // Check if the visitor has reached the limit of visits
        $visit_count = count( $visited_articles );
        // Determine the meter limit
        $limit = intval( get_field( 'meter_limit', 'option' ) );
        // Determine the plural suffix
        $s = ( $visit_count === 1 ) ? '' : 's';

        // Replace the placeholders in the template
        $replacements = [
            '{count}' => $visit_count,
            '{limit}' => $limit,
            '{s}'     => $s,
        ];

        if ( $visit_count > $limit ) {
            self::$meter_reached = true;
            $message             = get_field( 'post_meter_message', 'option' );
        } else {
            self::$meter_reached = false;
            $message             = get_field( 'pre_meter_message', 'option' );
        }

        self::$metered_message = str_replace( array_keys( $replacements ), array_values( $replacements ), $message );
    }

    static function is_paywalled( $post_id = null ): bool {
        // global $post;
        // $post_id = $post->ID ?? null;

        if ( isset( self::$is_paywalled[ $post_id ] ) ) {
            return self::$is_paywalled[ $post_id ];
        }

        $paywall = true;

        $force_paywall = get_field( 'options_force_paywall', 'options' );

        // If the article doesn't have a post ID, it's coming from the Multi Module
        if ( ! $post_id ) {
            $paywall = true;
        }
        // If the article has a term in the default category taxonomy (these are protected).
        if ( $post_id && count( wp_get_post_terms( $post_id, 'category' ) ) ) {
            $paywall = true;
        }
        // If the member is an institution
        if ( N1_Magazine::is_institution() ) {
            $paywall = false;
        }
        // If the user can edit posts.
        if ( current_user_can( 'edit_posts' ) && ! $force_paywall ) {
            $paywall = false;
        }

        if ( function_exists( 'mm_member_decision' ) ) {
            // If this is an MM Core page.
            if ( $post_id && \MM_CorePage::getCorePageInfo( $post_id ) ) {
                $paywall = false;
            }
            // If a member is logged in
            if ( mm_member_decision( [ "isMember" => "true", "status" => "active|pending_cancel" ] ) && ! $force_paywall ) {
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

        if ( $post_id ) {
            self::$is_paywalled[ $post_id ] = $paywall;
        }

        return $paywall;
    }

    public static function paywall_meter_reached( $post_id ): bool {
        return self::is_paywalled( $post_id ) && self::get_meter_reached();
    }
}

new Metered_Paywall();
