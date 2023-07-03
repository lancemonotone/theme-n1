<?php if ( N1_Magazine::Instance()->is_paywalled() && function_exists( 'adrotate_group' ) ) {
	echo adrotate_group( 3 );
} ?>

<div id="main" class="main-magazine-home main magazine-home wrapper cf">
	<div id="magazine-archive" class="als-container">
		<div class="list_carousel responsive">
			<div id="issue-slider">
				<?php
				$issues = N1_Magazine::Instance()->issues;
				foreach ($issues as $issue){
					$issue_art = get_field('issue_art', $issue->ID);?>
					<div class="als-item issue">
						<a href="<?php echo home_url()?>/magazine/<?php echo $issue->post_name?>">
							<figure class="issue thumb">
								<img src="<?php echo $issue_art['sizes']['issue-art']?>" alt="<?php _e('Art for')?> <?php echo $issue->post_title?>">
							</figure>
							<ol class="meta">
								<li class="number"><?php echo $issue->post_title?></li>
								<li class="title"><?php echo get_field('issue_name', $issue->ID)?></li>
								<li class="pubdate"><?php echo get_field('issue_date', $issue->ID)?></li>
							</ol>
						</a>
					</div>
				<?php } ?>
			</div>
			<div class="clearfix"></div>
			<a id="next3" class="als-next next" href="#"><img title="next" alt="next" src="<?php echo get_stylesheet_directory_uri()?>/assets/build/images/r-black.png"></a>
			<a id="prev3" class="als-prev prev" href="#"><img title="previous" alt="prev" src="<?php echo get_stylesheet_directory_uri()?>/assets/build/images/l-black.png"></a>
		</div>
	</div> <!-- als-container end -->

	<section class="main magazine-home content" id="content">
		<?php get_template_part( 'sidebars/sidebar', 'landing_magazine' ); ?>
	</section><!-- /#content -->
</div>
