<?php namespace N1_Durable_Goods;
// $authors = N1_Magazine::get_authors( $the_p->ID, true, false );
if ( $date = get_field( 'event_date', $the_p->ID ) ?? '' ) {
    $newDate = \DateTime::createFromFormat( 'Ymd', $date );

    $day   = $newDate->format( 'd' );
    $month = strtoupper( $newDate->format( 'M' ) );
    $year  = $newDate->format( 'Y' );
}

?>

<article class="card-event flavor-<?= $flavor ?? '' ?> type-<?= $article_type ?? '' ?> term-<?= $section->slug ?? '' ?> format-<?= $format ?? ''?> tax-<?= $the_tax ?? '' ?> featured-<?= $featured ?? '' ?>">

    <span class="debug">card-event flavor-<?= $flavor ?? '' ?> type-<?= $article_type ?? '' ?> term-<?= $section->slug ?? '' ?> format-<?= $format ?? ''?> tax-<?= $the_tax ?? '' ?> featured-<?= $featured ?? '' ?></span>

    <a class="article-link" href="<?= get_permalink( $the_p->ID ) ?>">

        <?= $content; ?>

        <ul class="meta<?= $date ? '': ' no-date'?>">
            <?php if ( $date ) { ?>
                <li class="date-container">
                    <div class="month-day"><?= $month . ' ' . $day; ?></div>
                    <div class="year"><?= $year; ?></div>
                </li>
            <?php } ?>
            <li class="title"><?= $title ?></li>
        </ul>

    </a>

    <?php edit_post_link( __( 'Edit' ), null, null, $the_p->ID ); ?>

</article>
