<?php namespace N1_Durable_Goods;
$authors = N1_Magazine::get_authors( $the_p->ID, true, false );
?>

<article class="type-<?= $article_type ?? '' ?> format-<?= $format  ?? ''?> tax-<?= $the_tax ?? '' ?> term-<?= $section->slug ?? '' ?>">

    <span class="debug">default <?= $article_type ?? '' ?> <?= $format ?? '' ?> <?= $featured ?? '' ?></span>

    <a class="article-link" href="<?= get_permalink( $the_p->ID ) ?>">

        <?= $content; ?>

        <?= $flags; ?>

        <ul class="meta">
            <li class="title"><?= $the_p->post_title ?></li>
            <?php if ( $authors ) { ?>
                <li class="author"><?= $authors ?></li>
            <?php } ?>
        </ul>

    </a>

    <?php edit_post_link( __( 'Edit' ), null, null, $the_p->ID ); ?>

</article>
