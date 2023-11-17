<?php

/**
 * issue, magazine, online-only, tagged, author
 *
 */
class Custom_Taxonomies {
    var $custom_taxonomies;

    /**
     * Call this method to get singleton
     *
     * @return Custom_Taxonomies
     */
    public static function Instance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new Custom_Taxonomies();
        }
        return $inst;
    }

    function __construct() {
        $this->setup();
    }

    function setup() {
        global $wp_rewrite;
        $this->unregister_taxonomies('post_tag');
        $this->custom_taxonomies['issue'] = array(
            'labels' => array(
                'name' => 'Issues',
                'singular_name' => 'Issue',
                'menu_name' => 'Issues',
                'all_items' => 'All Issues',
                'parent_item' => 'Parent Issue',
                'parent_item_colon' => 'Parent Issue:',
                'new_item_name' => 'New Issue Name',
                'add_new_item' => 'Add New Issue',
                'edit_item' => 'Edit Issue',
                'update_item' => 'Update Issue',
                'separate_items_with_commas' => 'Separate issues with commas',
                'search_items' => 'Search issues',
                'add_or_remove_items' => 'Add or remove issues',
                'choose_from_most_used' => 'Choose from the most used issues',
            ),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'magazine')
        );
        $this->custom_taxonomies['online-only'] = array(
            'labels' => array(
                'name' => 'Online Only Categories',
                'singular_name' => 'Category',
                'menu_name' => 'Online Only Categories',
                'all_items' => 'All Online Only Categories',
                'parent_item' => 'Parent Category',
                'parent_item_colon' => 'Parent Category:',
                'new_item_name' => 'New Category Name',
                'add_new_item' => 'Add New Category',
                'edit_item' => 'Edit Category',
                'update_item' => 'Update Category',
                'separate_items_with_commas' => 'Separate Categories with commas',
                'search_items' => 'Search Online Only Categories',
                'add_or_remove_items' => 'Add or remove Online Only Categories',
                'choose_from_most_used' => 'Choose from the most used Online Only Categories',
            ),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'online-only')
        );
        $this->custom_taxonomies['authors'] = array(
            'labels' => array(
                'name' => 'Authors',
                'singular_name' => 'Author',
                'menu_name' => 'Authors',
                'all_items' => 'All Authors',
                'parent_item' => 'Parent Author',
                'parent_item_colon' => 'Parent Author:',
                'new_item_name' => 'New Author Name',
                'add_new_item' => 'Add New Author',
                'edit_item' => 'Edit Author',
                'update_item' => 'Update Author',
                'separate_items_with_commas' => 'Separate Authors with commas',
                'search_items' => 'Search Authors',
                'add_or_remove_items' => 'Add or remove Authors',
                'choose_from_most_used' => 'Choose from the most used Authors',
            ),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'authors')
        );
        $this->custom_taxonomies['post_tag'] = array(
            'labels' => array(
                'name' => 'Tags',
                'singular_name' => 'Tag',
                'menu_name' => 'Tags',
                'all_items' => 'All Tags',
                'parent_item' => 'Parent Tag',
                'parent_item_colon' => 'Parent Tag:',
                'new_item_name' => 'New Tag Name',
                'add_new_item' => 'Add New Tag',
                'edit_item' => 'Edit Tag',
                'update_item' => 'Update Tag',
                'separate_items_with_commas' => 'Separate Tags with commas',
                'search_items' => 'Search Tags',
                'add_or_remove_items' => 'Add or remove Tags',
                'choose_from_most_used' => 'Choose from the most used Tags',
            ),
            'hierarchical' => true,
            'query_var' => 'tag',
            'rewrite' => array(
                'slug' => get_option('tag_base') ? get_option('tag_base') : 'tag',
                'with_front' => !get_option('tag_base') || $wp_rewrite->using_index_permalinks(),
                'ep_mask' => EP_TAGS,
            ),
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            '_builtin' => true,
        );
        foreach ($this->custom_taxonomies as $k => $v) {
            register_taxonomy($k, array('article'), $v);
        }

        //http://wp.tutsplus.com/tutorials/creative-coding/the-rewrite-api-the-basics/
        add_rewrite_tag('%issue%', '(issue\-[\d]+)', 'issue=');
        add_rewrite_tag('%authors%', '((.+?)(/[0-9]+)?)', 'authors=');
        add_rewrite_rule('(issue\-[\d]+)/(.+?)/$', 'index.php?issue=$matches[1]&category_name=$matches[2]&post_type=article', 'top');
        add_rewrite_rule('online-only/(.+?)/(.+?)$', 'index.php?online-only=$matches[1]&article=$matches[2]', 'top');
    }

    /**
     * Correct permalinks for custom taxonomies
     *
     * @param string $permalink
     * @param object $post
     * @return string
     */
    function filter_post_link($permalink, $post) {
        $custom_cats = array('issue', 'category', 'post_tag', 'authors', 'online-only');
        foreach ($custom_cats as $cc) {
            // Check if the tag is present in the url:
            if (false === strpos($permalink, '%' . $cc . '%')) continue;
            if ($cats = get_the_terms($post->ID, $cc))
                $permalink = str_replace('%' . $cc . '%', array_pop($cats)->slug, $permalink);
        }
        return $permalink;
    } // end function

    /**
     * Correct permalinks for custom post types
     *
     * @param string $permalink
     * @param object $post
     * @return string
     */
    function filter_post_type_link($permalink, $post) {
        $cpts = array('article');
        $custom_cats = array('issue', 'category', 'post_tag', 'authors', 'online-only');

        if (in_array($post->post_type, $cpts)) {
            foreach ($custom_cats as $c) {
                if ($cats = get_the_terms($post->ID, $c)) {
                    if ($c == 'online-only') { // total kludge, but what the hell, it works.
                        $permalink = str_replace('%issue%', 'online-only', $permalink);
                        $permalink = str_replace('%category%', array_pop($cats)->slug, $permalink);
                        break;
                    } else {
                        $permalink = str_replace('%' . $c . '%', array_pop($cats)->slug, $permalink);
                    }
                }
            }
        }
        return $permalink;
    } // end function

    /**
     * By default WP doesn't include CPTs in queries.  Let's add them in.
     * THIS IS A VERY IMPORTANT FUNCTION.  IT NEEDS TO INCLUDE ALL
     * TAXONOMIES USED IN THE SITE.
     *
     * @param object $query
     * @return object
     */
    function get_all_category_posts($query) {
        $cat_name = get_query_var('category_name');
        $tag_name = get_query_var('tag');
        $scroll_name = get_query_var('online-only');
        $author_name = get_query_var('authors');

        $has_cat = $cat_name || $tag_name || $scroll_name || $author_name;
        // Used for taxonomy listing page.  If $pagename matches a taxonomy, we're on.
        // Note, make sure there's a page in the DB with the same slug as the taxonomy.
        $taxterms = get_terms($query->query['pagename'], 'hide_empty=0&hierarchical=0&parent=0');

        if (!($taxterms instanceof WP_Error) || $has_cat && false == $query->query_vars['suppress_filters']) {
            $post_types = get_post_types();
            $query->set('post_type', $post_types);
        }
        return $query;
    } // end function

    /**
     * Remove built in taxonomies
     * @author: Franz Josef Kaiser
     */
    function unregister_taxonomies($taxonomy) {
        global $wp_taxonomies;
        foreach (wp_list_pluck($wp_taxonomies, '_builtin') as $tax => $data) {
            // Now let's unset "category"
            if ($tax === $taxonomy) {
                // Check to be sure if we're dealing with the right one
                # print_r( $tax );
                unset($wp_taxonomies[$tax]);
            }
        }
    }
}

Custom_Taxonomies::Instance();
?>