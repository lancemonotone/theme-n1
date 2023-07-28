<div class="toc">
    <?php // Home Left Modules
    if ( is_active_sidebar( 'sidebar-home-0' ) ) { ?>
        <?php dynamic_sidebar( 'sidebar-home-0' ); ?>
    <?php } ?>
</div>

<div class="featured">
    <?php // Home Hero Module
    if ( is_active_sidebar( 'sidebar-home-featured-article' ) ) { ?>
        <?php dynamic_sidebar( 'sidebar-home-featured-article' ); ?>
    <?php } ?>
</div>

<div class="flow">
    <?php // Bookstore
    if ( is_active_sidebar( 'sidebar-home-1' ) ) { ?>
        <?php dynamic_sidebar( 'sidebar-home-1' ); ?>
    <?php } ?>

    <?php // Multi
    if ( is_active_sidebar( 'sidebar-home-2' ) ) { ?>
        <?php dynamic_sidebar( 'sidebar-home-2' ); ?>
    <?php } ?>
</div>
