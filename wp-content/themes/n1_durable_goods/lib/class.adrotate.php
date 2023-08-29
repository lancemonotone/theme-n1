<?php namespace N1_Durable_Goods;

class Adrotate {
    static function display( $group_id, $always_show = false ) {
        if ( Metered_Paywall::is_paywalled() && function_exists( 'adrotate_group' ) ) {
            echo '<aside class="nurble">' . adrotate_group( $group_id ) . '</aside>';
        }
    }
}
