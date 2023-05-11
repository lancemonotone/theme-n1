<?php

class N1_Relevanssi {
    public function __construct() {
        $this->add_hooks();
    }

    protected function add_hooks() {
        add_filter('relevanssi_match', function ($match) {
            if (!empty($match->taxonomy_detail->authors) && $match->taxonomy_detail->authors === 1) {
                $match->weight *= 500;
            }
            return $match;
        });

        add_filter('pre_option_relevanssi_default_orderby', function($value, $option, $default){
            if (!is_admin()) {
            return array(
                'relevance' => 'desc',
                'post_date' => 'desc'
            );
            }else{
                return $value;
            }
        }, 20, 3);
    }
}

new N1_Relevanssi();