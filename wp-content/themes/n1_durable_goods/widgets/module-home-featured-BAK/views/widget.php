<?php namespace N1_Durable_Goods;
if ( ! is_home() ) {
    return;
}

// get home_featured_content from ACF options
$article       = get_field( 'home_featured', 'option' );
$article_title = $article->post_title;
$article_url   = get_permalink( $article->ID );
$article_link  = '<a href="' . $article_url . '">' . $article_title . '</a>';
$subhead       = get_field( 'article_subhead', $article->ID );
$authors       = N1_Magazine::get_authors( $article->ID );

if ( $terms = wp_get_post_terms( $article->ID, 'category' ) ) {
    // Issue
    $issue      = wp_get_post_terms( $article->ID, 'issue' );
    $issue      = reset( $issue );
    $issue_name = $issue->name;
    $issue_url  = get_term_link( $issue, 'issue' );

    // Article
    $term      = reset( $terms );
    $term_name = $term->name;
    $term_url  = get_term_link( $term, 'category' );

    $type = 'issue';
} elseif ( $terms = wp_get_post_terms( $article->ID, 'online-only' ) ) {
    // Online Only
    $term      = reset( $terms );
    $term_name = $term->name;
    $term_url  = get_term_link( $term, 'online-only' );

    $type = $term->slug === 'events' ? 'events' : 'online-only';
}

// Thumbnail
$img_id   = get_post_thumbnail_id( $article->ID );
$img_meta = wp_prepare_attachment_for_js( $img_id );
$img_url  = $img_meta[ 'url' ];
?>
<article class="type-<?= $type ?>">
    <figure style="background-image: url(<?= $img_url ?>);">
        <figcaption>
            <h4 class="title"><?= $article_link ?></h4>
            <?php if ( $authors ) { ?>
                <div class="authors"><?= $authors ?></div>
            <?php } ?>
        </figcaption>
    </figure>
    <div class="flags">
        <a href="<?= $term_url ?>"><?= $term_name ?></a>
    </div>
    <?php if ( current_user_can( 'edit_post', $the_p->ID ) ) { ?>
    <a class="post-edit-link" href="<?= admin_url( 'admin.php?page=acf-options' ) ?>">Edit</a>
    <?php } ?>
</article>