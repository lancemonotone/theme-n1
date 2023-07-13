<?php namespace N1_Durable_Goods;

use MM_User;

/**
 * Class Login
 *
 * @package N1_Durable_Goods
 */
class Login {
    public function __construct(){
        // Disable email check interval, which returns user to sign-in page.
        add_filter('admin_email_check_interval', '__return_false');

        add_filter( 'login_redirect', [ &$this, 'redirect_to_request' ], 10, 3 );
        add_filter( 'wp_head', [ &$this, 'redirect_logout_to_home' ] );
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
        if ( user_can( $user->ID, 'edit_posts' ) ) {
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
}

new Login();
