<?php namespace N1_Durable_Goods;

$context_issue = N1_Magazine::get_context_issue();
$hero_img      = get_field( 'issue_toc_image', $context_issue->ID );
?>
<section class="hero">
    <figure style="background-image:url(<?php echo $hero_img[ 'sizes' ][ 'hero-module' ] ?>);"></figure>
    <div class="hero-cell">
        <div class="hero-category">
            <span class="module-hed"><?php echo $context_issue->post_title ?></span>
        </div>
        <h2 class="hero-title"><?php echo get_field( 'issue_name', $context_issue->ID ) ?></h2>
        <p class="hero-dek"><?php echo get_field( 'issue_dek', $context_issue->ID ) ?></p>
    </div><!-- .hero-cell -->
</section>
<div id="main" class="main cf">
    <div class="archive-wrapper">
        <?php get_template_part( 'sidebars/sidebar', 'toc' ) ?>
    </div><!-- .content-wrapper -->
</div><!-- #main -->
