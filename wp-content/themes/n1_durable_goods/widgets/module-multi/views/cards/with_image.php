<?php namespace N1_Durable_Goods; ?>

<article class="card-with_image flavor-<?= $flavor ?? '' ?> type-<?= $article_type ?? '' ?> term-<?= $section->slug ?? '' ?> format-<?= $format ?? ''?> tax-<?= $the_tax ?? '' ?> featured-<?= $featured ?? '' ?>">

    <span class="debug">card-with_image flavor-<?= $flavor ?? '' ?> type-<?= $article_type ?? '' ?> term-<?= $section->slug ?? '' ?> format-<?= $format ?? ''?> tax-<?= $the_tax ?? '' ?> featured-<?= $featured ?? '' ?></span>

    <?php $this->print_post_head( $the_p, $article_type, $section, $authors ) ?>

    <?php if ( $article_type == 'magazine' && ( ! ! $featured || $format == 'with_image' ) ) {
        $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_p->ID ), 'content-full' );
        if ( is_array( $img_src ) ) {
            ?>
            <figure class="article-image">
                <img src="<?= $img_src[ 0 ] ?>" alt="<?= $the_p->post_title ?>"/>
            </figure>
        <?php }
    } else {
        if ( $format == 'with_image' ) { ?>
            <a href="<?= get_permalink( $the_p->ID ) ?>"><?= $content ?></a>
        <?php }
    } ?>

    <?php $section_name = $section->name ?? '' ?>

    <?= $section_name ? '<p class="category"><a href="' . get_term_link( $section, $taxonomy ) . '">' . $section_name . '</a></p>' : '' ?>

    <?= $authors ? '<p class="author">' . $authors . '</p>' : '' ?>

    <h3 class="title">
        <a href="<?= get_permalink( $the_p->ID ) ?>"><?= $the_p->post_title ?></a>
    </h3>

    <?php N1_Magazine::print_post_tags( $the_p->ID ); ?>

    <p class="dek"><?= $subhead ?></p>

    <div class="excerpt"><?= apply_filters( 'the_excerpt', $the_p->post_excerpt ) ?></div>

    <a href="<?= get_permalink( $the_p->ID ) ?>" class="jump"><?php _e( 'Read More' ) ?></a>

    <?php edit_post_link( __( 'Edit' ), null, null, $the_p->ID ); ?>

</article><!-- /.post -->
