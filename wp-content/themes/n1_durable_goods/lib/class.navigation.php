<?php namespace N1_Durable_Goods;

class Navigation {
	public function __construct() {
		add_filter( 'nav_menu_css_class', [ $this, 'add_current_nav_ancestor_class' ], 10, 3 );
	}

	public function add_current_nav_ancestor_class( $classes, $item, $args ) {
		if ( is_singular() ) {
			global $post;

			// Get item url
			$item_url = $item->url;
			// Get last segment of item url
			if ( preg_match( '~/([^/]+)(?=/[^/]*$)~', $item_url, $matches ) ) {
				$item_page = $matches[1];

				// Get current page
				$page_class = N1_Magazine::get_page_class();

				// Massage the page class to match the taxonomy
				$current_taxonomy = $page_class === 'magazine' ? 'category' : $page_class;

				// get current post terms
				$terms = get_the_terms( $post->ID, $current_taxonomy );

				// check if current post belongs to current taxonomy
				foreach ( $terms as $term ) {
					if ( ! empty( $term->taxonomy ) && $term->taxonomy === $current_taxonomy && $item_page === $page_class ) {
						$classes[] = 'current-ancestor';
					}
				}
			}

		}

		return $classes;
	}
}

new Navigation();
