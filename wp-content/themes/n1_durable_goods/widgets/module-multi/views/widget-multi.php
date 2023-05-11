<?php
// $title 				= strip_tags($instance['title']);
// $subtitle 			= strip_tags($instance['subtitle']);
// $flavor 			= strip_tags($instance['flavor']);
// $taxonomy	 		= $instance['taxonomy'] ? strip_tags($instance['taxonomy']) : null;
// $term		 		= $instance['term'] ? strip_tags($instance['term']) : null;
// $number 			= intval(strip_tags($instance['number']));
// $ad_after 			= intval(strip_tags($instance['ad_after']));
// $newsletter_after 	= intval(strip_tags($instance['newsletter_after']));
// $social_after 		= intval(strip_tags($instance['social_after']));
// $order 				= strip_tags($instance['order']);
// $orderby 			= strip_tags($instance['orderby']);
// $meta_key			= strip_tags($instance['meta_key']);
// $infinite 			= bool_from_yn(strip_tags($instance['infinite']));

$title = isset($instance['title']) ? strip_tags($instance['title']) : '';
$subtitle = isset($instance['subtitle']) ? strip_tags($instance['subtitle']) : '';
$flavor = isset($instance['flavor']) ? strip_tags($instance['flavor']) : '';
$taxonomy = isset($instance['taxonomy']) ? strip_tags($instance['taxonomy']) : null;
$term = isset($instance['term']) ? strip_tags($instance['term']) : null;
$number = isset($instance['number']) ? intval(strip_tags($instance['number'])) : 0;
$ad_after = isset($instance['ad_after']) ? intval(strip_tags($instance['ad_after'])) : 0;
$newsletter_after = isset($instance['newsletter_after']) ? intval(strip_tags($instance['newsletter_after'])) : 0;
$social_after = isset($instance['social_after']) ? intval(strip_tags($instance['social_after'])) : 0;
$order = isset($instance['order']) ? strip_tags($instance['order']) : '';
$orderby = isset($instance['orderby']) ? strip_tags($instance['orderby']) : '';
$meta_key = isset($instance['meta_key']) ? strip_tags($instance['meta_key']) : '';
$infinite = isset( $instance['infinite'] ) && bool_from_yn( strip_tags( $instance['infinite'] ) );

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
$the_posts = $this->get_multi_posts($flavor, $number, $ad_after, $order, $orderby, $newsletter_after, $social_after, $taxonomy, $term, $meta_key);

if(count($the_posts)){
	$class = $id = '';
	switch($flavor){
		case 'featured-author':
			$class = 'supp-more more-by';
			break;
		case 'featured-default':
			$class = 'current-issue-featured';
			$loadmore = ' Features';
			break;
		case 'issue':
			$context_title = N1_Magazine::Instance()->context_issue->post_title;
			$title = $title . ' ' . $context_title;
			$class = 'supplementary more more-from';
			$loadmore = ' from ' . $context_title;
			break;
		case 'online-only':
			$class = 'featured-online-only';
			$loadmore = ' Online Only';
			break;
		case 'archive':
		case 'sticky':
			$class = 'content-post';
			$id = 'content';
			if($taxonomy == 'online-only' && $term == null){
				$loadmore = ' Online Only';
			}else{
				$the_term = get_term_by('slug', $term, $taxonomy);
				$loadmore = ' from ' . N1_Magazine::format_author_name($the_term->name);
			}
			break;
		default:
			$class = "supplementary more";
			$loadmore = '';
			break;
	}?>
<section id="<?php echo $id?>" class="<?php echo $class?> flavor-<?php echo $flavor;?>">
	<?php echo $title ? '<h3 class="supplementary more title"><span class="section-hed">' . $title .'</span></h3>' : '';?>
	<?php echo $subtitle ? '<p class="online-only-archive dek">'. $subtitle .'</p>' : '';?>
		<?php echo ($flavor != 'archive' && $flavor != 'sticky') ? '<section class="articles">' : '';?>
			<?php
			$this->print_multi_posts($the_posts, $ad_after, $flavor, $newsletter_after, $social_after, $taxonomy, $term);

			if($infinite){
			$totalpages = floor($this->multi_query->found_posts / $number);
			?>
			<div class="online-only loadmore">
		    	<a class="infinite loadmore jump trigger" href="<?php echo home_url()?>/online-only"
					data-paged="1"
					data-totalpages="<?php echo $totalpages?>"
					data-flavor="<?php echo $flavor?>"
					data-number="<?php echo $number?>"
					data-ad_after="<?php echo $ad_after?>"
					data-newsletter_after="<?php echo $newsletter_after?>"
					data-social_after="<?php echo $social_after?>"
					data-order="<?php echo $order?>"
					data-orderby="<?php echo $orderby?>"
					data-taxonomy="<?php echo $taxonomy?>"
					data-term="<?php echo $term?>"
					data-meta_key="<?php echo $meta_key?>">
					<?php _e('Load more' . $loadmore)?>
				</a>
				<div class="spinner"><img src="<?php echo get_template_directory_uri()?>/img/spinner.gif"></div>
			</div><!-- .online-only.loadmore -->
			<?php }?>
		<?php echo ($flavor != 'archive' && $flavor != 'sticky') ? '</section><!-- .articles-->' : '';?>
</section><!-- .supplementary -->
<?php } //end if?>
