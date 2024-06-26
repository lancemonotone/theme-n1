<?php namespace N1_Durable_Goods;
$section_name = $section->name ?? '';
?>

<article class="card-pullquote flavor-<?= $flavor ?? '' ?> type-<?= $article_type ?? '' ?> term-<?= $section->slug ?? '' ?> format-<?= $format ?? '' ?> tax-<?= $the_tax ?? '' ?> featured-<?= $featured ?? '' ?>">

    <span class="debug">card-pullquote flavor-<?= $flavor ?? '' ?> type-<?= $article_type ?? '' ?> term-<?= $section->slug ?? '' ?> format-<?= $format ?? '' ?> tax-<?= $the_tax ?? '' ?> featured-<?= $featured ?? '' ?></span>

    <?php $this->print_post_head( $the_p, $article_type, $section, $authors ) ?>

    <?= $section_name ? '<p class="category"><a href="' . get_term_link( $section, $taxonomy ) . '">' . $section_name . '</a></p>' : '' ?>

    <a href="<?= get_permalink( $the_p->ID ) ?>"><?= $content ?></a>

    <?php N1_Magazine::print_post_tags( $the_p->ID ); ?>

    <h3 class="title">
        <a href="<?= get_permalink( $the_p->ID ) ?>"><?= $the_p->post_title ?></a>
    </h3>

    <?= $authors ? '<p class="author">' . $authors . '</p>' : '' ?>

    <?php edit_post_link( __( 'Edit' ), null, null, $the_p->ID ); ?>

</article>
