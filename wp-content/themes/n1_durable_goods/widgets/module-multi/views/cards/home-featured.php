<?php namespace N1_Durable_Goods; ?>

<article class="type-<?= $article_type ?? '' ?> format-<?= $format ?? '' ?> tax-<?= $the_tax ?? '' ?> term-<?= $section->slug ?? '' ?>">

    <span class="debug">home-featured <?= $article_type ?? '' ?> <?= $format ?? '' ?> <?= $featured ?? '' ?></span>

    <div class="ribbon ribbon-top-left">
        <span>Featured</span>
    </div>

    <a href="<?= get_permalink( $the_p->ID ) ?>">

        <?= $content; ?>

        <?= $flags; ?>

        <ul class="meta">
            <li class="title"><?= $the_p->post_title ?></li>
            <li class="author"><?= N1_Magazine::get_authors( $the_p->ID, true, false ) ?></li>
        </ul>

    </a>

    <?php if ( current_user_can( 'edit_post', $the_p->ID ) ) { ?>
        <a class="post-edit-link" href="<?= admin_url( 'post.php?post=' . $the_p->ID . '&action=edit' ) ?>">Edit</a>
    <?php } ?>
</article>
