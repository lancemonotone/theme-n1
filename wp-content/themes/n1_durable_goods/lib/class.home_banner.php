<?php namespace N1_Durable_Goods;

class Home_Banner {
    public static function get_home_banner() {
        // Fetch ACF fields for image URLs from options page
        $hero_banner_full = get_field( 'hero_banner_full', 'option' );
        $hero_banner_lg   = get_field( 'hero_banner_lg', 'option' );
        $hero_banner_md   = get_field( 'hero_banner_md', 'option' );
        $hero_banner_sm   = get_field( 'hero_banner_sm', 'option' );

        // Other data (you can fetch these from ACF as well if needed)
        $link   = get_field( 'hero_banner_link', 'option' );
        $title  = get_field( 'hero_banner_title', 'option' );
        $target = "_blank";

        // Set fallbacks from the next largest image
        if ( ! $hero_banner_sm ) {
            $hero_banner_sm = $hero_banner_md ? $hero_banner_md : ( $hero_banner_lg ? $hero_banner_lg : $hero_banner_full );
        }
        if ( ! $hero_banner_md ) {
            $hero_banner_md = $hero_banner_lg ? $hero_banner_lg : $hero_banner_full;
        }
        if ( ! $hero_banner_lg ) {
            $hero_banner_lg = $hero_banner_full;
        }

        $template = <<<EOD
<a class="home-hero-banner" href="$link" target="$target" title="$title"><picture>
    <source media="(min-width:1024px)" srcset="$hero_banner_full">                 
    <source media="(min-width:768px)" srcset="$hero_banner_lg">
    <source media="(min-width:440px)" srcset="$hero_banner_md">
    <img src="$hero_banner_sm" width="1940" height="194" alt="" style="width:auto;">
</picture></a>
EOD;

        echo $template;
    }
}

