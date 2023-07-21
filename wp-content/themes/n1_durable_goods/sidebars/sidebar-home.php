<?php // Home Left Modules
if ( is_active_sidebar( 'sidebar-home-0' ) ) { ?>
    <div class="home-left gcol-1 grow-5">
        <?php dynamic_sidebar( 'sidebar-home-0' ); ?>
    </div>
<?php } ?>

<?php // Home Hero Module
if ( is_active_sidebar( 'sidebar-home-featured-article' ) ) { ?>
    <div class="home-featured gcol-2-md grow-1">
        <?php dynamic_sidebar( 'sidebar-home-featured-article' ); ?>
    </div>
<?php } ?>

<?php // Home Center Modules
if ( is_active_sidebar( 'sidebar-home-1' ) ) { ?>
    <div class="home-1 gcol-1 grow-1">
        <?php dynamic_sidebar( 'sidebar-home-1' ); ?>
    </div>
<?php } ?>

<?php // Home Right Modules
if ( is_active_sidebar( 'sidebar-home-2' ) ) { ?>
    <div class="home-1 gcol-1">
        <?php dynamic_sidebar( 'sidebar-home-2' ); ?>
    </div>
<?php } ?>
