<?php namespace N1_Durable_Goods;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

if ( is_plugin_active( 'lj-maintenance-mode/lj-maintenance-mode.php' ) ) {
    $page_type = 'static-page';
    ?>

    <div id="main" class="cf static-page">
        <section id="content">
            <article>
                <div class="entry-header">
                    <h1 class="entry-title">n+1 is upgrading!</h1>
                </div>
                <div class="entry-content">
                    <p>Our website will be available soon. We are upgrading our server and content platforms
                       for security and speed and will be back with you momentarily!</p>
                </div><!-- .entry-content -->
            </article><!-- #post -->
        </section> <!-- #content -->
    </div><!-- #main -->
<?php } else {
    $page_type = N1_Magazine::get_page_type();

    switch ( $page_type ) {
        case 'home':
            //echo 'Home Page';
            get_template_part( 'parts/loop', 'home' );
            break;
        case 'magazine issue-landing':
            //echo 'TOC Page';
            get_template_part( 'parts/loop', 'toc' );
            break;
        case 'tax':
            //echo 'Tax Landing Page';
            get_template_part( 'parts/loop', 'tax' );
            break;
        case 'archive':
            //echo 'Archive';
            get_template_part( 'parts/loop', 'archive' );
            break;
        case 'magazine':
            //echo 'Magazine';
            get_template_part( 'parts/loop', 'single_magazine' );
            break;
        case 'online-only-home':
            //echo 'Online Only Home';
            get_template_part( 'parts/loop', 'archive' );
            break;
        case 'online-only':
            //echo 'Online Only';
            get_template_part( 'parts/loop', 'single_online-only' );
            break;
        case 'search':
            //echo 'Search';
            get_template_part( 'parts/loop', 'search' );
            break;
        case 'static-page':
            //echo 'Page';
            get_template_part( 'parts/loop', 'page' );
            break;
        case 'magazine landing':
            //echo 'Magazine Landing';
            get_template_part( 'parts/loop', 'landing_magazine' );
            break;
    }
}

get_footer(); ?>
