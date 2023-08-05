<?php namespace N1_Durable_Goods; ?>

<article class="type-<?= $article_type ?? '' ?> term-<?= $section->slug ?? '' ?> format-<?= $format ?? '' ?> <?= $featured ?? '' ?>">

    <span class="debug">pullquote <?= $article_type ?? '' ?> <?= $format ?? '' ?> <?= $featured ?? '' ?></span>

    <?php $this->print_post_head( $the_p, $article_type, $section, $authors ) ?>

    <?= $section->name ? '<p class="category"><a href="' . get_term_link( $section, $taxonomy ) . '">' . $section->name . '</a></p>' : '' ?>

    <a href="<?= get_permalink( $the_p->ID ) ?>"><?= $content ?></a>

    <?php N1_Magazine::print_post_tags( $the_p->ID ); ?>

    <h3 class="title">
        <a href="<?= get_permalink( $the_p->ID ) ?>"><?= $the_p->post_title ?></a>
    </h3>

    <?= $authors ? '<p class="post-author">' . $authors . '</p>' : '' ?>

    <a href="<?= get_permalink( $the_p->ID ) ?>" class="jump"><?php _e( 'Read More' ) ?></a>

    <?php edit_post_link( __( 'Edit' ), null, null, $the_p->ID ); ?>

</article>
