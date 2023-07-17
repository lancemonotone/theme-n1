<?php namespace N1_Durable_Goods;
if ( ! is_home() ) {
    return;
}
$hero_module_id = $instance[ 'home_hero_module' ];

// get home_hero_content from ACF options
$article       = get_field( 'home_hero_content', 'option' );
$article_title = $article->post_title;
$article_url   = get_permalink( $article->ID );
$article_link  = '<a href="' . $article_url . '">' . $article_title . '</a>';
$subhead       = get_field( 'article_subhead', $article->ID );
$authors       = N1_Magazine::get_authors( $article->ID );

if ( $terms = wp_get_post_terms( $article->ID, 'category' ) ) {
    // Issue
    $issue = wp_get_post_terms( $article->ID, 'issue' );
    /** @var WP_Term $term */
    $issue      = reset( $issue );
    $issue_name = $issue->name;
    $issue_url  = get_term_link( $issue, 'issue' );

    // Article
    /** @var WP_Term $term */
    $term      = reset( $terms );
    $term_name = $term->name;
    $term_url  = get_term_link( $term, 'category' );

    $type = 'issue';
} elseif ( $terms = wp_get_post_terms( $article->ID, 'online-only' ) ) {
    // Online Only
    /** @var WP_Term $term */
    $term      = reset( $terms );
    $term_name = $term->name;
    $term_url  = get_term_link( $term, 'online-only' );

    $type = $term->slug === 'events' ? 'events' : 'online-only';
}

// Thumbnail
$img_id   = get_post_thumbnail_id( $article->ID );
$img_meta = wp_prepare_attachment_for_js( $img_id );
$img_url  = $img_meta[ 'url' ];
$img      = wp_get_attachment_image_src( $img_id, 'content-full' );
?>
<section class="home-hero <?= $type ?>">
    <figure style="background-image: url(<?= $img_url ?>);">
        <figcaption>
            <h4 class="title"><?= $article_link ?></h4>
            <?php if ( $authors ) { ?>
                <div class="authors"><?= $authors ?></div>
            <?php } ?>
        </figcaption>
    </figure>
    <div class="term">
        <a href="<?= $term_url ?>"><?= $term_name ?></a>
    </div>
</section>
