<?php namespace N1_Durable_Goods;
/**
 * Search results are contained within a div.searchwp-live-search-results
 * which you can style accordingly as you would any other element on your site
 *
 * Some base styles are output in wp_footer that do nothing but position the
 * results container and apply a default transition, you can disable that by
 * adding the following to your theme's functions.php:
 *
 * add_filter( 'searchwp_live_search_base_styles', '__return_false' );
 *
 * There is a separate stylesheet that is also enqueued that applies the default
 * results theme (the visual styles) but you can disable that too by adding
 * the following to your theme's functions.php:
 *
 * wp_dequeue_style( 'searchwp-live-search' );
 *
 * You can use ~/searchwp-live-search/assets/styles/style.css as a guide to customize
 */
?>

<?php if (have_posts()) {
    while (have_posts()) {
        the_post();
        $post_type = get_post_type_object(get_post_type());
        $authors = N1_Magazine::get_authors(get_the_ID());
        ?>
      <div class="searchwp-live-search-result" role="option" id="" aria-selected="false">
        <p class="post-author"><?php echo $authors ?></p>
        <h1 class="post-title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h1>
        <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_excerpt() ?></a>
      </div>
    <?php }?>
    <div class="searchwp-live-search-result" role="option" id="" aria-selected="false">
      <p><a href="<?php echo get_site_url()?>?s=<?php echo $swpquery;?>">More results...</a></p>
    </div>
<?php } else { ?>
  <p class="searchwp-live-search-no-results" role="option">
    <em><?php esc_html_e('No results found.', 'searchwp-live-ajax-search'); ?></em>
  </p>
<?php } ?>
