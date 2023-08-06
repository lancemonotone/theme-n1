<?php namespace N1_Durable_Goods;

class Navigation {
    public function __construct() {
        add_filter( 'nav_menu_css_class', [ $this, 'add_current_nav_ancestor_class' ], 10, 3 );
    }

    public function add_current_nav_ancestor_class( $classes, $item, $args ) {
        $page_class = N1_Magazine::get_page_class();

        // Convert the menu item classes string into an array.
        $menu_item_classes_array = explode(' ', join(' ', $classes));

        // Convert the page class string into an array.
        $page_classes_array = explode(' ', $page_class);

        // Check if all individual classes in $page_class exist in $menu_item_class.
        $contains_all = true;
        foreach ($page_classes_array as $class) {
            if (!in_array($class, $menu_item_classes_array)) {
                $contains_all = false;
                break;
            }
        }

        // Special case for online-only without events
        if ($page_class == 'online-only' && in_array('events', $menu_item_classes_array)) {
            $contains_all = false;
        }

        if ($contains_all) {
            $classes[] = 'current-ancestor';
        }

        return $classes;
    }


}

new Navigation();
