<?php namespace N1_Durable_Goods;

class Tag_Colors {
    public function __construct() {
        add_action( 'wp_head', [ $this, 'generate_tag_colors' ] );
    }

    function generate_tag_colors() {
        $tags = get_tags();
        $css  = '<style id="generated-tag-colors">';

        foreach ( $tags as $tag ) {
            // Generate a color based on the tag ID
            $color = '#' . substr( md5( $tag->term_id ), 0, 6 );

            // Create a CSS variable for the color
            $css .= ".tag-{$tag->slug}{background-color:{$color}};";
        }

        $css .= '</style>';

        echo $css;
    }
}

// new Tag_Colors();
