<?php namespace N1_Durable_Goods;
$title = $instance['title'];
$subtitle = $instance['subtitle'];
$img_src = $instance['img_src'];
$pm_posts = get_posts(
	array(
		'post_type' 		=> 'article',
		'post_status' 		=> 'publish',
		'posts_per_page'	=> '4',
		'online-only'			=> 'paper-monument'
	)
);
if(count($pm_posts)){
	$pm_img = get_field('options_paper_monument_img', 'options');
?>

<section class="module featured pm" style="background-image:url(<?php echo $pm_img['sizes']['full']?>);">
	<?php echo $title ? '<h2 class="featured pm category"><span class="module-hed">' . $title . '</span></h2>' : ''?>
	<?php echo $subtitle ? '<p class="featured pm dek">' . $subtitle . '</p>' : ''?>

<?php foreach($pm_posts as $pm){
	$authors = N1_Magazine::get_authors($pm->ID, true, false);
	?>
	<div class="featured pm article">
	<a href="<?php echo get_permalink($pm->ID)?>" title="<?php echo $pm->post_title?>">
		<ul class="featured books module author meta">
			<li class="featured pm article title"><?php echo $pm->post_title?></li>
			<li class="featured pm article author"><?php echo $authors?></li>
		</ul>
	</a>
	<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>', $pm->ID ); ?>
	</div>
<?php } ?>
	<div class="featured pm jump"><a href="<?php echo home_url()?>/online-only/paper-monument" class="jump"><?php _e('Visit Paper Monument')?></a></div>
</section>

<?php } ?>
