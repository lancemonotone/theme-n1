<?php if ( N1_Magazine::Instance()->is_paywalled() && function_exists( 'adrotate_group' ) ) {
	echo adrotate_group( 3 );
} ?>

<?php 
$current_page = get_queried_object();
$is_scroll = is_a($current_page, 'WP_Post');
$archive_name = $is_scroll ? $current_page->post_title : (isset($authors) ? N1_Magazine::Instance()->format_author_name($current_page->name) : $current_page->name);
$archive_class = $is_scroll ? 'online-only' : 'archive';
$archive_description = $is_scroll ? get_field('options_online-only_landing_page_dek','options') : $current_page->description;
if($current_page->taxonomy == 'authors') $archive_description = _('All articles by this author');
$taxonomy = $is_scroll ? $pagename : ($taxonomy ? $taxonomy : $current_page->taxonomy);
$term = $term ? $term : $current_page->slug;
?>
<div id="main" class="main wrapper cf">
	<div class="<?php echo $archive_class?>-wrapper cf">
	
		<section class="bug">
			<h1 class="<?php echo $archive_class?>"><?php _e($archive_name)?></h1>
			<p class="dek <?php echo $archive_class?>-dek"><?php _e($archive_description)?></p>
		</section>
		
		<div class="main content cf">
			<?php get_template_part( 'sidebars/sidebar', 'archive' ); ?>
			<?php 
			$multi_args = array(
				'flavor' 			=> 'archive',
				'number'			=> '12',
				'social_after'		=> '2',
				'newsletter_after'	=> '2',
				'ad_after'			=> '0',
				'taxonomy'			=> $taxonomy,	
				'term'				=> $term,
				'infinite'			=> 'y',
				'orderby'			=> $term == 'events' ? 'meta_value_num' : null,
				'order'				=> $term == 'events' ? 'DESC' : null,
				'meta_key'			=> $term == 'events' ? 'event_date' : null,
			);
			the_widget('Module_Multi', $multi_args);
			?>
		</div><!-- .main-content -->	

		<div class="online-only-terms">
			<h2><?php _e('All Categories')?></h2>
			<ul>
			<?php foreach(get_terms('online-only') as $cat){?>
				<li class="post-meta-term-item">
					<a class="category <?php echo $cat->slug?>" href="<?php echo get_term_link($cat, 'post')?>"><?php echo $cat->name?></a>
				</li>
			<?php } ?>
			</ul>			
		</div><!-- .online-only-cats -->
		
		<div class="online-only-terms">
			<h2><?php _e('All Tags')?></h2>
			<ul>
			<?php foreach(get_terms('post_tag') as $pt){


                                        if($pt->slug === 'unpaywalled') {
                                                continue;
                                        }?>

				<li class="post-meta-term-item">
					<a class="tag <?php echo $pt->slug?>" href="<?php echo get_term_link($pt, 'post')?>"><?php echo $pt->name?></a>
				</li>
			<?php } ?>
			</ul>			
		</div><!-- .online-only-tags -->
	</div><!-- .<?php echo $archive_class?>-wrapper -->
</div><!-- #main -->
