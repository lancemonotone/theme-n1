<div class="strux-tab-320fill strux-desk-320fill strux-wide-25">
    <?php if ( is_active_sidebar( 'sidebar-home-0' ) ) { ?>
        <?php dynamic_sidebar( 'sidebar-home-0' ); ?>
    <?php } ?>
</div><!-- .strux-wide-50 -->

<div class="strux-tab-320fill strux-desk-320fill strux-wide-75">
    <?php if ( is_active_sidebar( 'sidebar-home-hero' ) ) { ?>
        <?php dynamic_sidebar( 'sidebar-home-hero' ); ?>
    <?php } ?>

    <div class="strux-tab-320fill strux-desk-320fill strux-wide-50">
        <?php if ( is_active_sidebar( 'sidebar-home-1' ) ) { ?>
            <?php dynamic_sidebar( 'sidebar-home-1' ); ?>
        <?php } ?>
    </div><!-- .strux-wide-25 -->

    <div class="featured strux-tab-320min strux-desk-320min strux-wide-50">
        <?php if ( is_active_sidebar( 'sidebar-home-2' ) ) { ?>
            <?php dynamic_sidebar( 'sidebar-home-2' ); ?>
        <?php } ?>
    </div><!-- .strux-wide-25 -->
</div><!-- .strux-wide-50 -->
