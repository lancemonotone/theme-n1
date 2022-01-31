<?php

class Custom_Posts {
    var $custom_posts;

    /**
     * Call this method to get singleton
     *
     * @return Custom_Posts
     */
    public static function Instance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new Custom_Posts();
        }
        return $inst;
    }

    function __construct() {
        $this->setup();
    }

    function setup() {

        $this->custom_posts['article'] = array(
            'label' => 'Articles',
            'description' => 'Magazine article',
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'hierarchical' => true,
            'rewrite' => array('slug' => '%issue%/%category%','with_front' => true,'pages' => true,'feeds' => false,),
            'exclude_from_search' => false,
            'menu_position' => 4,
            'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'trackbacks', 'page-attributes',),
            'labels' => array(
                'name' => 'Articles',
                'singular_name' => 'Article',
                'menu_name' => 'Articles',
                'parent_item_colon' => '',
                'all_items' => 'All Articles',
                'view_item' => 'View Article',
                'add_new_item' => 'Add New Article',
                'add_new' => 'New Article',
                'edit_item' => 'Edit Article',
                'update_item' => 'Update Article',
                'search_items' => 'Search Articles',
                'not_found' => 'No articles found',
                'not_found_in_trash' => 'No articles found in Trash',
            ),
            'show_in_nav_menus' => true,
            'taxonomies' => array('issue', 'category', 'authors', 'online-only', 'post_tag'),
            'show_in_admin_bar' => true,
            'menu_icon' => '',
            'can_export' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );

        $this->custom_posts['toc_desc'] = array(
            'label' => 'Issue Settings',
            'description' => 'Magazine Issue Settings',
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => false,
            'exclude_from_search' => true,
            'menu_position' => 3,
            'supports' => array('title'),
            'labels' => array(
                'name' => 'Issue Settings',
                'singular_name' => 'Issue Settings',
                'menu_name' => 'Issue Settings',
                'parent_item_colon' => '',
                'all_items' => 'All Issue Settings',
                'view_item' => 'View Issue Settings',
                'add_new_item' => 'Add New Issue Settings',
                'add_new' => 'New Issue Settings',
                'edit_item' => 'Edit Issue Settings',
                'update_item' => 'Update Issue Settings',
                'search_items' => 'Search Issue Settings',
                'not_found' => 'No Issue Settings found',
                'not_found_in_trash' => 'No Issue Settings found in Trash',
            ),
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'menu_icon' => '',
            'can_export' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
        );

        // Register 'category' taxonomy for use with article post type.
        foreach ($this->custom_posts as $k => $v) {
            register_post_type($k, $v);
        }
    }
}

Custom_Posts::Instance();
