<?php namespace N1_Durable_Goods; ?>

<?php if ( N1_Magazine::is_paywalled() && function_exists( 'adrotate_group' ) ) {
	echo adrotate_group( 3 );
} ?>

<div id="main" class="main issue wrapper cf">
<div class="main issue content">
	<?php get_template_part( 'sidebars/sidebar', 'single_magazine' ); ?>
<?php while ( have_posts() ) { the_post();
	$cat = reset(wp_get_post_terms($post->ID, 'category'));
	$issue_obj = N1_Magazine::get_issue_by_slug($issue);
?>
	<section id="content" class="content-post issue-content<?php echo N1_Magazine::is_paywalled($post->ID) ? ' unlogged' : ''?>"> <!-- includes POST and PREV-NEXT -->
		<article id="post-<?php the_ID(); ?>" <?php post_class('post issue-content-post'); ?>>
			<div class="post-header issue-content-post-header">
				<p class="post-category issue-content-post-header-category"><?php echo $cat->name?></p>
				<p class="post-author issue-content-post-header-author"><?php echo N1_Magazine::get_authors($post->ID)?></p>
				<h1 class="post-title issue-content-post-header-title"><?php the_title()?></h1>
				<?php if($article_subhead = get_field('article_subhead', $post->ID)){?>
				<p class="post-dek issue-content-post-header-dek"><?php echo $article_subhead?></p>
				<?php }?>
			</div><!-- .post-header -->

			<div class="post-meta issue-content-post-meta">
				<section class="post-meta-pubinfo issue-content-post-meta-pubinfo">
					<p class="post-meta-entry issue-content-post-meta-pubinfo-entry">
						<span class="category post-meta-hed runin"><?php _e('Published in')?></span>
						<a href="<?php echo N1_Magazine::get_context_issue_url()?>" title="<?php echo $issue_obj->post_title?>">
							<?php echo $issue_obj->post_title?>: <?php echo get_field('issue_name', $issue_obj->ID)?>
						</a>
					</p>
					<p class="post-meta-entry issue-content-post-meta-pubinfo-entry">
						<span class="category post-meta-hed runin"><?php _e('Publication date')?></span>
						<?php echo get_field('issue_date', $issue_obj->ID)?>
					</p>
				</section> <!-- .post-meta-pubinfo -->

				<?php N1_Magazine::print_post_tags( $post->ID, true );?>
				<?php N1_Magazine::print_social($post->ID);?>

			</div><!-- .post-meta -->

			<?php
			$img_id = get_post_thumbnail_id( $post->ID );
			$img_meta = wp_prepare_attachment_for_js($img_id);
			$img = wp_get_attachment_image_src( $img_id, 'content-full' );
			if($img){?>
				<figure class="post-hero issue-content-post-hero">
					<img class="issue-content-post-hero-image" src="<?php echo $img[0]?>" alt="<?php echo $img_meta['alt']?>" />
					<figcaption class="issue-content-post-hero-caption"><?php echo $img_meta['caption']?></figcaption>
				</figure>
			<?php } ?>
			<?php if($reviewed_items = get_field('reviewed_items', $post->ID)){?>
				<div class="reviews">
					<?php echo wpautop(html_entity_decode($reviewed_items))?>
				</div>
			<?php } ?>
			<?php if($article_headnote = get_field('article_headnote', $post->ID)){?>
				<div class="headnote">
					<?php echo wpautop(html_entity_decode($article_headnote))?>
				</div>
			<?php } ?>

			<div class="post-body issue-content-post-body">
				<?php
				if(N1_Magazine::is_paywalled($post->ID)){
					$the_content = apply_filters('the_content', get_field('article_long_excerpt', $post->ID));
					echo Utility::insert_advertisement($the_content, 2, 2);

				} else { // no paywall...show complete article.

						$the_content = '<div class="post-wrapper">' . apply_filters('the_content', get_the_content()) . '</div>';

						/*if($appendix = get_field('article_appendix', $post->ID)){

							$app = '<div class="appendix">'. $appendix .'</div>';

						}*/

						echo $the_content/*.$app*/;
					} ?>
				<?php if($article_appendix = get_field('article_appendix', $post->ID)){?>
				<div class="appendix">
					<p><?php echo html_entity_decode($article_appendix)?></p>
				</div>
				<?php } ?>
			</div><!-- .post-body -->
		</article><!-- #post -->
		<?php if(N1_Magazine::is_paywalled($post->ID)){?>

			<div class="roadblock">
				<!-- subscribe -->
				<section class="subscribe roadblock">
					<h3 class="subscribe roadblock"><?php _e('Unlock sixteen years of n+1.')?></h3>
					<p class="subscribe roadblock subhed"><?php _e('It only takes 5 minutes to subscribe.')?></p>
					<p class="subscribe roadblock subscribe prompt"><?php echo get_field('options_subscribe_prompt','options')?></p>
					<div class="module subscribe prompt wrapper">
					<a href="<?php echo home_url()?>/subscribe" class="module subscribe prompt button"><?php _e('Subscribe')?></a>
					</div>
					<?php if(!is_user_logged_in()){?>
					<div class="subscribe roadblock signin">
						<p class="subscribe roadblock action"><?php echo get_field('options_subscribe_action','options')?></p>
						<form action="<?php echo home_url()?>/wp-login.php?redirect_to=<?php echo urlencode(site_url( $_SERVER['REQUEST_URI'] ))?>" method="POST" class="subscribe roadblock">
							<fieldset class="subscribe roadblock username">
								<label for="username" class="subscribe roadblock username">Email</label>
								<input class="subscribe roadblock text username form-text" type="text" id="log" name="log" placeholder="email">
							</fieldset>

							<fieldset class="subscribe roadblock password">
								<label for="password" class="subscribe roadblock password">Password</label>
								<input class="form-text subscribe roadblock text password" type="password" id="pwd" name="pwd" placeholder="password">
							</fieldset>

							<fieldset class="form-actions subscribe roadblock submit">
								<input type="submit" value="<?php _e('Sign In')?>" class="subscribe roadblock submit button">
							</fieldset>
						</form>
						<a href="<?php echo home_url()?>/forgot-password/"><?php _e('Forgot Password')?></a>
					</div>
					<?php } ?>
				</section>

			</div><!-- .roadblock -->

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
<?php } ?>
	<?php // right sidebar content (currently blank) ?>
	<section class="sidebar"></section>
	</div><!-- /.main.content -->
</div><!-- /#main -->
<?php get_template_part( 'sidebars/sidebar', 'single_magazine_bottom' ); ?>
