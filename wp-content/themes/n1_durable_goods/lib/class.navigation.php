<?php namespace N1_Durable_Goods;

class Navigation {
    public function __construct() {
        add_filter( 'nav_menu_css_class', [ $this, 'add_current_nav_ancestor_class' ], 10, 3 );
    }

    public function add_current_nav_ancestor_class( $classes, $item, $args ) {
        $page_class = N1_Magazine::get_page_class();

        // Flatten the array of classes.
        $body_class = join( ' ', $classes );

        // Check if the page class is in the classes array.
        // This is a bit of a fudge because Events is also Online
        // Only, and has a class of 'events online-only', so we
        // need to check for both.
        if ( stristr( $body_class, $page_class ) ) {
            $classes[] = 'current-ancestor';
        }

        return $classes;
    }
}

new Navigation();
