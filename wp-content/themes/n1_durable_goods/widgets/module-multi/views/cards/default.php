<?php namespace N1_Durable_Goods; ?>

<article class="type-<?= $article_type ?> format-<?= $format ?> tax-<?= $the_tax ?> term-<?= $section->slug ?>">

    <?= $article_type ?> <?= $format ?> <?= $featured ?>

    <a class="article-link" href="<?= get_permalink( $the_p->ID ) ?>">

        <?= $content; ?>

        <?= $flags; ?>

        <ul class="meta">
<!--            <li class="category">--><?//= $section->name ?><!--</li>-->
            <li class="title"><?= $the_p->post_title ?></li>
            <li class="author"><?= N1_Magazine::get_authors( $the_p->ID, true, false ) ?></li>
        </ul>

    </a>

    <?php edit_post_link( __( 'Edit' ), null, null, $the_p->ID ); ?>

</article>
