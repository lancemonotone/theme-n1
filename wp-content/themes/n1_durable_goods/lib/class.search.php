<?php namespace N1_Durable_Goods;

class Search {
    public function __construct() {
        $this->hooks();
    }

    public function hooks() {
        add_filter('posts_search', function ($where, $wp_query) {
            return trim($where) === 'AND ((()))' ? '' : $where;
        }, 11, 2);

    }
}

new Search();
