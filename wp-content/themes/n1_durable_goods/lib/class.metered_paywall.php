<?php namespace N1_Durable_Goods;

class Metered_Paywall {
    static bool $is_metered = false;
    static string $metered_message = 'Init metering.';

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
                'meta'   => [ 'html' => true ], // Allow HTML in the title
            ];
            $wp_admin_bar->add_node( $args );
        }
    }


    public function print_inline_script() {
        $nonce = wp_create_nonce( 'reset-metered-paywall' ); // Create nonce for security
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                var resetButton = document.querySelector('.reset-metered-paywall-button')
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

    static function is_metered(): bool {
        return self::$is_metered;
    }

    /**
     * This must be called before any output is sent to the browser.
     */
    static function set_is_metered() {
        if ( session_status() === PHP_SESSION_DISABLED ) {
            // Sessions are not available, set a default message
            self::$is_metered      = false;
            self::$metered_message = "Sessions are not enabled, so metering is unavailable.";

            return;
        }

        if ( session_status() === PHP_SESSION_NONE ) {
            ini_set( 'session.cookie_lifetime', 30 * 24 * 60 * 60 );
            session_start();
        }

        global $post;

        if ( ! N1_Magazine::is_paywalled( $post->ID ) ) {
            return false;
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
            self::$is_metered = true;
            $message          = get_field( 'post_meter_message', 'option' );
        } else {
            self::$is_metered = false;
            $message          = get_field( 'pre_meter_message', 'option' );
        }

        self::$metered_message = str_replace( array_keys( $replacements ), array_values( $replacements ), $message );
    }

}

new Metered_Paywall();
