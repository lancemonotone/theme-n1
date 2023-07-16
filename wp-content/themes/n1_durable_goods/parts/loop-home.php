<?php namespace N1_Durable_Goods; ?>

<?php //dynamic_sidebar( 'sidebar-hero' ); ?>

<?php Adrotate::display( 3 ) ?>

<div id="main" class="main main-home cf">
    <section id="content" class="main-home content">
        <?php get_template_part( 'sidebars/sidebar', 'home' ); ?>
    </section><!-- /#content -->
</div><!-- /#main -->

<div id="supplementary" class="supp">
    <?php dynamic_sidebar( 'sidebar-home-3' ); ?>
</div><!-- #supplementary -->
