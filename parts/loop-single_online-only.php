<?php echo adrotate_group(3);?>
<div id="main" class="main wrapper cf">
	<div class="main content">
	
	<?php get_template_part( 'sidebars/sidebar', 'single_online-only' ); ?>
	<section class="content-post" id="content"> <!-- includes POST and PREV-NEXT -->
	<?php while ( have_posts() ) { the_post(); 
		$section = reset(wp_get_post_terms($post->ID, 'online-only'));
		$issue_obj = N1_Magazine::get_issue_by_slug($issue);
	?>
		<article class="post term-<?php echo $section->slug?>">
			<script type="text/javascript">
				var _sf_async_config = _sf_async_config || {};
				_sf_async_config.authors = '<?php echo N1_Magazine::get_authors($post->ID, true, false) ?>';
			</script>	
			<div class="post-header">
				<p class="post-category"><a href="<?php echo get_term_link($section, 'online-only')?>"><?php echo $section->name?></a></p>
				<p class="post-author"><?php echo N1_Magazine::get_authors($post->ID)?></p>
				<?php if($event_date = get_field('event_date', $post->ID)){?>
					<p class="event-date"><?php echo date('F j, Y', strtotime($event_date))?></p> 
				<?php }?>
				<h1 class="post-title"><?php the_title()?></h1>
				<?php if($article_subhead = get_field('article_subhead', $post->ID)){?>
				<h2 class="post-subtitle"><?php echo $post->post_excerpt?></h2>
				<p class="post-dek"><?php echo $article_subhead?></p>
				<?php }?>
			</div><!-- .post-header-->
			
			<?php 
			$img_id = get_post_thumbnail_id( $post->ID );
			$img_meta = wp_prepare_attachment_for_js($img_id);
			$img = wp_get_attachment_image_src( $img_id, 'content-full' );
			if($img){?>
			<figure class="post-hero">
				<img alt="<?php echo $img_meta['alt']?>" src="<?php echo $img[0]?>">
				<figcaption><?php echo $img_meta['caption']?></figcaption>
			</figure>
			<?php } ?>

			<!-- in-article meta (is this OK?) -->
			<div class="post-meta cf">
				<div class="left">
					<section class="post-meta-pubinfo">
						<p class="post-meta-entry">
							<span class="post-date post-meta-hed runin">
								<?php echo date('F j, Y', strtotime($post->post_date))?>
							</span>
						</p>
					</section> <!-- /publication-info -->
					<?php N1_Magazine::Instance()->print_post_tags($post->ID,true);?>								
					<?php N1_Magazine::Instance()->print_social($post->ID);?>		
				</div>	
				<?php the_widget('Module_Newsletter');?>
			</div><!-- .post-meta-->

			<?php if($reviewed_items = get_field('reviewed_items', $post->ID)){?>
				<div class="reviews">
					<?php echo html_entity_decode($reviewed_items)?>
				</div>
			<?php } ?>
			<?php if($article_headnote = get_field('article_headnote', $post->ID)){?>
				<div class="headnote">
					<?php echo html_entity_decode($article_headnote)?>
				</div>
			<?php } ?>
			
			<div class="post-body">
				<?php 
					$the_content = apply_filters('the_content', get_the_content());
					echo Utility::insert_advertisement($the_content, 5, 2);
				?>
				<?php if($article_appendix = get_field('article_appendix', $post->ID)){?>
				<div class="appendix">
					<p><?php echo html_entity_decode($article_appendix)?></p>
				</div>
				<?php } ?>
				
				<!-- START SUBSCRIBE LINK -->
				<?php if($section->name !== 'Events' && $section->name !== 'Announcements'){?>
					<p class="small">
						<em>
							<?php _e('If you like this article, please')?> <a href="<?php echo home_url('/subscribe/')?>"><?php _e('subscribe')?></a> <?php _e('or')?><a href="<?php echo home_url('/donate/')?>"> <?php _e('donate')?></a> <?php _e('to support n+1.')?>
						</em>
					</p>
				<?php } ?>
				<!-- END SUBSCRIBE LINK -->
				
			</div><!-- .post-body-->			
		</article><!-- /.post -->
		<?php } ?>
		<?php //issue navigation
		$prev_link = N1_Magazine::same_edition_and_section_adjacent_post_link ( '%link', '%title', true, false );
		$next_link = N1_Magazine::same_edition_and_section_adjacent_post_link ( '%link', '%title', false, false );
		
		if( $prev_link || $next_link ){?>

			<nav class="prev-next">
				<ul>
					<?php if( $prev_link ){?><li class="prev"><?php echo $prev_link; ?></li><?php }?>
					<?php if( $next_link ){?><li class="next"><?php echo $next_link; ?></li><?php }?>
				</ul>
			</nav>
			<?php  

			echo $after_widget;
		} // end if?>
	</section><!-- /#content -->
	
	<!-- right sidebar content (currently blank) -->
	<section class="sidebar"> 
	</section>
</div><!-- .main-content -->	
</div><!-- #main -->
<?php get_template_part( 'sidebars/sidebar', 'single_online-only_bottom' ); ?>
