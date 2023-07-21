<?php namespace N1_Durable_Goods; ?>

<?php Adrotate::display( 4 ) ?>

<div id="main" class="main main-home">
    <section class="content grid gap-3">
        <?php get_template_part( 'sidebars/sidebar', 'home' ); ?>
    </section><!-- /#content -->
</div><!-- /#main -->

<?php Adrotate::display( 3 ) ?>

<?php // Home Bottom Modules ?>
<?php if ( is_active_sidebar( 'sidebar-home-0' ) ) { ?>
    <div id="supplementary" class="supp">
        <?php dynamic_sidebar( 'sidebar-home-3' ); ?>
    </div><!-- #supplementary -->
<?php } ?>
