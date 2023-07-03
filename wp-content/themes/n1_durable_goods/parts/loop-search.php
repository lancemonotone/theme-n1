<?php
$archive_name = 'Search results for';
$archive_description = $wp_query->query['s'];
$the_posts = $wp_query->posts;
$multi = new Module_Multi();
?>
<div id="main" class="cf">
	<div class="archive-wrapper cf">
		<section class="bug">
			<p class="dek archive-dek"><?php _e($archive_name)?></p>
			<h1 class="archive">&#8220;<?php _e($archive_description)?>&#8221;</h1>
		</section>
		<div class="main content cf">
			<?php //get_template_part( 'sidebars/sidebar', 'archive' ); ?>
			<section id="content" class="content-post">
				<?php if(count($the_posts)){
					$multi->print_multi_posts($the_posts, 0, 'archive', 0, 0);
				}else{?>
				<div class="static-page">
					<div class="entry-header">
					</div>
					<article>
						<div class="entry-content">
							<p class="lede"><?php _e('Sorry, we didn&#8217;t find a match for your search terms.')?></p>
							<p><?php _e('View all issues of')?>
							<a href="<?php echo home_url()?>/magazine">n+1 magazine</a>,
							<?php _e('or our latest')?>
							<a href="<?php echo home_url()?>/online-only/">Online Only</a>
							<?php _e('posts, with material from')?>
							<a href="<?php echo home_url()?>/online-only/paper-monument/">Paper Monument</a>,
							<?php _e('the')?>
							<a href="<?php echo home_url()?>/online-only/film-review/">Film Review</a>,
							<a href="<?php echo home_url()?>/online-only/book-review/">Book Review</a>,
							<a href="<?php echo home_url()?>/online-only/podcast/">Podcast</a>,
							<a href="<?php echo home_url()?>/online-only/city-by-city/">City-by-City</a>,
							<?php _e('and more.')?>
							</p>
							<p><?php _e('If all else fails, please contact us at <a href="mailto:editors@nplusonemag.com">editors@nplusonemag.com</a> and we&#8217;ll try to help.')?></p>
						</div>
					</article>
				</div>
				<?php }?>
			</section><!-- #content -->
		</div><!-- .main-content -->
	</div><!-- .archive-wrapper -->
</div><!-- #main -->
