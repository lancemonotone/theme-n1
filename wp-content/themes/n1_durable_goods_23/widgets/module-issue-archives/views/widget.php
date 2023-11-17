<?php
$title = strip_tags($instance['title']);
$subtitle = strip_tags($instance['subtitle']);
$issues = get_posts(
	array(
		'post_type' 		=> 'toc_desc',
		'post_status' 		=> 'publish',
		'posts_per_page'	=> '-1',
		'order'				=> 'DESC'
	)
);
?>

<section class="magazine-archive">
<div class="floatwrapper">
	<h2 class="magazine-archive title"><span class="section-hed"><?php _e($title)?></span></h2>
	<p class="magazine-archive dek"><?php _e($subtitle)?></p>

<?php foreach($issues as $issue){
	$img = get_field('issue_art', $issue->ID);
	?>
	<div class="magazine-archive issue">
		<a href="<?php echo home_url()?>/magazine/<?php echo $issue->post_name?>"  title="Link to <?php echo $issue->post_title?>">
			<figure class="issue thumb">
				<img src="<?php echo $img['sizes']['issue-archives-module']?>">
			</figure>
				<ol class="magazine-archive issue meta">
					<li class="magazine-archive issue number"><?php echo $issue->post_title?></li>
					<li class="magazine-archive issue title"><?php echo get_field('issue_name', $issue->ID)?></li>
					<li class="magazine-archive issue pubdate"><?php echo get_field('issue_date', $issue->ID)?></li>
				</ol>
		</a>	
	</div><!-- .magazine-archive issue -->
<?php } ?>
</div><!-- .floatwrapper -->
</section>