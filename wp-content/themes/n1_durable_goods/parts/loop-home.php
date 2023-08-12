<?php namespace N1_Durable_Goods; ?>

<?php Adrotate::display( 4 ) ?>

<main>
    <section class="content">
        <?php get_template_part( 'sidebars/sidebar', 'home' ); ?>
    </section><!-- /#content -->
</main><!-- /#main -->

<?php Adrotate::display( 3 ) ?>

<?php // Home Bottom Modules 1 ?>
<?php if ( is_active_sidebar( 'sidebar-home-3' ) ) { ?>
    <div id="supplementary" class="supp">
        <?php dynamic_sidebar( 'sidebar-home-3' ); ?>
    </div><!-- #supplementary -->
<?php } ?>

<?php Adrotate::display( 5 ) ?>

<?php // Home Bottom Modules 2 ?>
<?php if ( is_active_sidebar( 'sidebar-home-4' ) ) { ?>
    <div id="supplementary" class="supp">
        <?php dynamic_sidebar( 'sidebar-home-4' ); ?>
    </div><!-- #supplementary -->
<?php } ?>
