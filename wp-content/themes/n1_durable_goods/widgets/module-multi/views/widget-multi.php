<?php namespace N1_Durable_Goods;
$title            = isset( $instance[ 'title' ] ) ? strip_tags( $instance[ 'title' ] ) : '';
$subtitle         = isset( $instance[ 'subtitle' ] ) ? strip_tags( $instance[ 'subtitle' ] ) : '';
$flavor           = isset( $instance[ 'flavor' ] ) ? strip_tags( $instance[ 'flavor' ] ) : '';
$taxonomy         = isset( $instance[ 'taxonomy' ] ) ? strip_tags( $instance[ 'taxonomy' ] ) : null;
$term             = isset( $instance[ 'term' ] ) ? strip_tags( $instance[ 'term' ] ) : null;
$number           = isset( $instance[ 'number' ] ) ? intval( strip_tags( $instance[ 'number' ] ) ) : 0;
$ad_after         = isset( $instance[ 'ad_after' ] ) ? intval( strip_tags( $instance[ 'ad_after' ] ) ) : 0;
$newsletter_after = isset( $instance[ 'newsletter_after' ] ) ? intval( strip_tags( $instance[ 'newsletter_after' ] ) ) : 0;
$social_after     = isset( $instance[ 'social_after' ] ) ? intval( strip_tags( $instance[ 'social_after' ] ) ) : 0;
$bookstore_after  = isset( $instance[ 'bookstore_after' ] ) ? intval( strip_tags( $instance[ 'bookstore_after' ] ) ) : 0;
$order            = isset( $instance[ 'order' ] ) ? strip_tags( $instance[ 'order' ] ) : '';
$orderby          = isset( $instance[ 'orderby' ] ) ? strip_tags( $instance[ 'orderby' ] ) : '';
$meta_key         = isset( $instance[ 'meta_key' ] ) ? strip_tags( $instance[ 'meta_key' ] ) : '';
$infinite         = isset( $instance[ 'infinite' ] ) && bool_from_yn( strip_tags( $instance[ 'infinite' ] ) );

/**
 * Flavors:
 * featured-default
 * featured-author
 * online-only
 * author
 * issue
 * tag
 * archive
 */
$the_posts = $this->get_multi_posts( $flavor, $number, $ad_after, $order, $orderby, $newsletter_after, $social_after, $bookstore_after, $taxonomy, $term, $meta_key );

if ( count( $the_posts ) ) {
    $loadmore = '';
    switch ( $flavor ) {
        case 'featured-default':
            $loadmore = ' Features';
            break;
        case 'issue':
            $context_title = N1_Magazine::get_context_issue()->post_title;
            $title         = $title . ' ' . $context_title;
            $loadmore      = ' from ' . $context_title;
            break;
        case 'online-only':
            $loadmore = ' Online Only';
            break;
        case 'archive':
        case 'sticky':
            if ( $taxonomy == 'online-only' && $term == null ) {
                $loadmore = ' Online Only';
            } else {
                $the_term = get_term_by( 'slug', $term, $taxonomy );
                $loadmore = ' from ' . N1_Magazine::format_author_name( $the_term->name );
            }
            break;
        case 'featured-author':
        case 'home-hero':
        default:
            break;
    } ?><? //= $flavor ?>

    <?php echo $title ? '<h3 class="widget-title">' . $title . '</h3>' : ''; ?>
    <?php echo $subtitle ? '<p class="dek">' . $subtitle . '</p>' : ''; ?>

    <section class="articles flavor-<?php echo $flavor; ?>">

        <?php $this->print_multi_posts( $the_posts, $ad_after, $flavor, $newsletter_after, $social_after, $bookstore_after, $taxonomy, $term );

        if ( $infinite ) {
            $totalpages = floor( $this->multi_query->found_posts / $number );
            ?>
            <div class="online-only loadmore">
                <a class="infinite loadmore jump trigger" href="<?php echo home_url() ?>/online-only"
                   data-paged="1"
                   data-totalpages="<?php echo $totalpages ?>"
                   data-flavor="<?php echo $flavor ?>"
                   data-number="<?php echo $number ?>"
                   data-ad_after="<?php echo $ad_after ?>"
                   data-newsletter_after="<?php echo $newsletter_after ?>"
                   data-social_after="<?php echo $social_after ?>"
                   data-bookstore_after="<?php echo $bookstore_after ?>"
                   data-order="<?php echo $order ?>"
                   data-orderby="<?php echo $orderby ?>"
                   data-taxonomy="<?php echo $taxonomy ?>"
                   data-term="<?php echo $term ?>"
                   data-meta_key="<?php echo $meta_key ?>">
                    <?php _e( 'Load more' . $loadmore ) ?>
                </a>
                <div class="spinner">
                    <img src="<?php echo get_template_directory_uri() ?>/assets/build/images/spinner.gif">
                </div>
            </div><!-- .online-only.loadmore -->
        <?php } ?>
    </section><!-- .supplementary -->
<?php } //end if
?>
