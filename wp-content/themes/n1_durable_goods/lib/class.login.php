<?php namespace N1_Durable_Goods;

use MM_User;

/**
 * Class Login
 *
 * @package N1_Durable_Goods
 */
class Login {
    public function __construct() {
        // Disable email check interval, which returns user to sign-in page.
        add_filter( 'admin_email_check_interval', '__return_false' );
        add_filter( 'login_redirect', [ &$this, 'redirect_to_request' ], 10, 3 );
        add_filter( 'wp_head', [ &$this, 'redirect_logout_to_home' ] );
        add_filter( 'loginout', [ $this, 'loginout_text_change' ] );
        add_shortcode( 'login_error_message', [ $this, 'login_error_shortcode' ] );
    }

    function redirect_logout_to_home() {
        if ( is_page( 'signout' ) ) {
            $home = home_url();
            echo <<<EOD
			<script type="text/javascript">
			(function(){ setTimeout(function(){
                // alert("You have been logged out.")
                location.href="{$home}"} , 100
             )}());
			</script>
EOD;
        }
    }

    function redirect_to_request( $redirect_to, $request, $user ) {
        if ( ! class_exists( '\MM_User' ) ) {
            return $request;
        }
        /**
         * @todo Check that $request is not a 404. If so, go home.
         */
        $member     = new MM_User( $user->ID );
        $status_map = [
            '1' => $request,
            '2' => home_url( '/action?status=Canceled' ),
            '3' => home_url( '/action?status=Locked' ),
            '4' => home_url( '/action?status=Paused' ),
            '5' => home_url( '/action?status=Overdue' ),
            '6' => home_url( '/action?status=Pending+Activation' ),
            '7' => home_url( '/action?status=Error' ),
            '8' => home_url( '/action?status=Expired' ),
            '9' => home_url( '/action?status=Pending+Cancellation' ),
        ];

        // instead of using $redirect_to we're redirecting back to $request
        if ( ! Metered_Paywall::is_paywalled() && user_can( $user->ID, 'edit_posts' ) ) {
            $where = $redirect_to;
        } elseif ( $member->getMembershipId() === "1" ) {
            // Push free members to renew when they log in.
            if ( ! $where = get_permalink( get_field( 'free_member_redirect', 'option' ) ) ) {
                // If the redirect page is not set, go to the home page.
                $where = $redirect_to;
            }
        } elseif ( $request == '' ) {
            $where = $redirect_to;
        } elseif ( $member->isValid() ) {
            $where = $status_map[ $member->getStatus() ];
        } else {
            $where = $request;
        }

        return $where;
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
     * Massage login error to provide link to login page
     * NOTE: Requires `$is_login_error = $_GET["message"] ?: false;`
     * to be present in the header of the page template using this shortcode.
     */
    function login_error_shortcode() {
        global $is_login_error;

        if ( ! $is_login_error ) {
            return;
        }

        // retrieve current error message
        $crntErrorMessage = stripslashes( $is_login_error );

        // There is an existing account associated with the email nicoleklipman+test@gmail.com but the password entered is invalid. Please try placing your order again using the correct password.
        // check if error message contains the string 'Incorrect username or password'
        if ( strpos( $crntErrorMessage, "Please try placing your order again using the correct password" ) !== false ) {
            // extract the email address from the $crntErrorMessage string using regex
            $pattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/';
            preg_match( $pattern, $crntErrorMessage, $matches );
            $email = $matches[ 0 ];

            unset( $_GET[ 'message' ] );

            /**
             * Hide the error message immediately following
             * `.mm-formError.prime` so we can replace it with our own.
             */
            return <<<EOT
<style> .mm-formError.prime + .mm-formError { display: none; } </style>
<p class="mm-formError prime"> There is an existing account associated with the email <em>{$email}</em> but the password entered is invalid. <strong>Please place your order again using the correct password or try <a href="https://www.nplusonemag.com/forgot-password/">resetting your password</a></strong>.</p>
EOT;
        }
    }
}

new Login();
