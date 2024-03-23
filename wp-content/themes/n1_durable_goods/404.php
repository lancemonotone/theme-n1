<?php
/**
 *
 */
get_header();?>
<div id="main" class="cf">
	<section id="content">
			<article id="post-118" class="post-118 page type-page status-publish hentry">
			<div class="entry-header">
				<h1 class="entry-title"><?php _e('Something Happened')?></h1>
			</div>

			<div class="entry-content">
				<div class="mm">
					<p class="lede"><?php _e('What the Heller?')?></p>
					<p><?php _e('We recently redesigned the site. So much has changed.')?></p>
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
					<p><?php _e('Or try a search term in the search area above.')?></p>
					<p><?php _e('If all else fails, please contact us at <a href="mailto:editors@nplusonemag.com">editors@nplusonemag.com</a> and we&#8217;ll try to help.')?></p>
				</div>
			</div><!-- .entry-content -->
		</article><!-- #post -->
	</section> <!-- #content -->
</div><!-- #main -->
<?php get_template_part( 'sidebars/sidebar', 'page' ); ?>
<?php get_footer()?>
