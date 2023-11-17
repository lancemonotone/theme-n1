<?php
/**
 * A custom post type or widget-like module featuring promotions from the Bookstore, which is hosted offsite (Shopify). 
 * Admins will need to be able to edit the following fields:
 * Module title (e.g. "The Bookstore"; text input)
 * Module subtitle ("New and notable books from n+1"; text input)
 * Module dek ("Two Reasons to Get Excited About School"; text input)
 * Up to two slots for book promotion, including:
 * Book cover (image upload)
 * Book promotional text (WYSIWYG)
 * Link to Shopify bookstore
 */
 
//	Defaults
$instance = wp_parse_args( 
	(array) $instance, array( 
		'books_module' => '',
		'always_latest' => '1'
	) 
);
$always_latest = strip_tags($instance['always_latest']);
$books_module = strip_tags($instance['books_module']);

// Get Books module posts
$the_args = array(
	'post_type' 		=> 'module_books',
	'post_status' 		=> 'publish',
	'posts_per_page' 	=> -1,
);
$books_modules = get_posts($the_args);
?>
<p>
	<input id="<?php echo $this->get_field_id('always_latest'); ?>" name="<?php echo $this->get_field_name('always_latest'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['always_latest'] ); ?> />
	<label for="<?php echo $this->get_field_id('always_latest'); ?>"><?php _e('Always use latest published?') ?></label>
</p>
<p>
	<label for="<?php echo $this->get_field_id('books_module'); ?>"><?php _e( 'Choose Books Module:' ); ?></label>
	<select class="widefat"  name="<?php echo $this->get_field_name('books_module'); ?>" id="<?php echo $this->get_field_id('books_module'); ?>" class="widefat">
		<?php foreach($books_modules as $bw){?>
			<option value="<?php echo $bw->ID?>" <?php selected( $instance['books_module'],  $bw->ID, true); ?>><?php echo $bw->post_title?></option>
		<?php } ?>
	</select>
</p>