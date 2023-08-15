<?php namespace N1_Durable_Goods;
// $authors = N1_Magazine::get_authors( $the_p->ID, true, false );
if ( $date = get_field( 'event_date', $the_p->ID ) ?? '' ) {
    $newDate = \DateTime::createFromFormat( 'Ymd', $date );

    $day   = $newDate->format( 'd' );
    $month = strtoupper( $newDate->format( 'M' ) );
    $year  = $newDate->format( 'Y' );
}

?>

<article class="type-<?= $article_type ?? '' ?> format-<?= $format ?? '' ?> tax-<?= $the_tax ?? '' ?> term-<?= $section->slug ?? '' ?>">

    <span class="debug">default <?= $article_type ?? '' ?> <?= $format ?? '' ?> <?= $featured ?? '' ?></span>

    <a class="article-link" href="<?= get_permalink( $the_p->ID ) ?>">

        <?= $content; ?>

        <? //= $flags;
        ?>

        <ul class="meta">
            <?php if ( $date ) { ?>
                <li class="date-container">
                    <div class="month-day"><?= $month . ' ' . $day; ?></div>
                    <div class="year"><?= $year; ?></div>
                </li>
            <?php } ?>
            <li class="title"><?= $the_p->post_title ?></li>
        </ul>

    </a>

    <?php edit_post_link( __( 'Edit' ), null, null, $the_p->ID ); ?>

</article>
