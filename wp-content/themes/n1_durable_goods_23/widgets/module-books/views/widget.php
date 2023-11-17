<?php
$books_module_id = $instance['books_module'];

// Get latest or specific module as set in widget admin.
if($instance['always_latest'] === '1'){ 
	$the_args = array( 'order'=> 'DESC' ); 
} else { 
	$the_args = array( 'p' => $books_module_id ); 
}

// Merge query args and get_posts.
$books_modules = get_posts(
	array_merge(
		$the_args,
		array(
			'post_type' 		=> 'module_books',
			'post_status' 		=> 'publish',
			'posts_per_page'	=> -1
		)
	)
);

$books_module = reset($books_modules);
$title = get_field('books_module_title', $books_module->ID);
$subtitle = get_field('books_module_subtitle', $books_module->ID);
$copy = get_field('books_module_copy', $books_module->ID);
$books = get_field('books_module_books', $books_module->ID);
?>

<section class="featured books">
	<h2 class="featured books category"><span class="module-hed"><?php echo $title?></span></h2>
	<p class="featured books dek"><?php echo $subtitle?></p>	
	<h3 class="featured books title"><?php echo $copy?></h3>
	
	<?php foreach($books as $b){?>
		<div class="featured books module">
			<figure class="featured books module cover">
				<a href="<?php echo $b['books_module_books_shopify']?>" title="<?php echo $b['books_module_books_title']?>">
					<img alt="thumbnail" src="<?php echo $b['books_module_books_cover']['sizes']['books-module']?>" alt="<?php $b['books_module_books_title']?>" class="featured books module cover">
				</a>
			</figure>
			<ul class="featured books module author meta">
				<li class="featured books module title"><?php echo $b['books_module_books_title']?></li>
				<li class="featured books module author"><?php echo $b['books_module_books_author']?></li>
				<li class="featured books module description"><?php echo $b['books_module_books_promo']?></li>
			</ul>
		</div>
	<?php } ?>

	<div class="featured books jump"><a href="<?php echo get_field('options_shopify_link','options')?>" title="<?php _e('Visit the Bookstore')?>" class="jump"><?php _e('Visit the Bookstore')?></a></div>
</section>