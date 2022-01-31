<?php 
$context_issue = N1_Magazine::Instance()->context_issue;
$hero_img = get_field('issue_toc_image', $context_issue->ID);
?>
<section class="magazine issue-landing hero" id="hero">
	<figure id="magazine-issue-landing-hero" class="hero" style="background-image:url(<?php echo $hero_img['sizes']['hero-module']?>);"></figure>
	<div class="hero-wrapper">
		<div class="hero-cell">
			<div class="hero category"><span class="module-hed"><?php echo $context_issue->post_title?></span></div>
			<h2 class="hero title"><?php echo get_field('issue_name', $context_issue->ID)?></h2>
			<p class="hero dek"><?php echo get_field('issue_dek', $context_issue->ID)?></p>
		</div><!-- .hero-cell -->
	</div><!-- .hero-wrapper -->
</section>
<div id="main" class="main cf">
	<div class="content-wrapper">
		<?php get_template_part( 'sidebars/sidebar', 'toc' )?>
	</div><!-- .content-wrapper -->
</div><!-- #main -->