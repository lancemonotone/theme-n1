<?php namespace N1_Durable_Goods;
//	Defaults
$instance = wp_parse_args(
	(array) $instance, array(
		'title' 			=> __('The Multi Widget'),
		'subtitle' 			=> __('Regular dispatches from the world at large'),
		'flavor'			=> __('online-only'),
		'number' 			=> '6',
		'ad_after'			=> '',
		'newsletter_after'	=> '',
		'social_after'		=> '',
		'order' 			=> 'DESC',
		'orderby'			=> 'date',
		'infinite'			=> 'n'
	)
);
$title = strip_tags($instance['title']);
$subtitle = strip_tags($instance['subtitle']);
$flavor = strip_tags($instance['flavor']);
$number = intval(strip_tags($instance['number']));
$ad_after = intval(strip_tags($instance['ad_after']));
$newsletter_after = intval(strip_tags($instance['newsletter_after']));
$social_after = intval(strip_tags($instance['social_after']));
$order = strip_tags($instance['order']);
$orderby = strip_tags($instance['orderby']);
$infinite = strip_tags($instance['infinite']);
?>

<?php // text ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Module Title:' ); ?></label><br>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title?>"/>
</p>

<?php // text ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e( 'Module subtitle:' ); ?></label><br>
	<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo $subtitle?>"/>
</p>

<?php // select ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('flavor'); ?>"><?php _e( 'Flavor:' ); ?></label>
	<select class="widefat"  name="<?php echo $this->get_field_name('flavor'); ?>" id="<?php echo $this->get_field_id('flavor'); ?>">
		<option value="home-featured" <?php selected( $instance['flavor'],  'home-featured', true); ?>><?php _e('Home Featured')?></option>
        <option value="featured-default" <?php selected( $instance['flavor'],  'featured-default', true); ?>><?php _e('Featured (default)')?></option>
		<option value="featured-author" <?php selected( $instance['flavor'],  'featured-author', true); ?>><?php _e('Featured (by author)')?></option>
		<option value="online-only" <?php selected( $instance['flavor'],  'online-only', true); ?>><?php _e('Online Only')?></option>
		<option value="issue" <?php selected( $instance['flavor'],  'issue', true); ?>><?php _e('More from this Issue')?></option>
		<option value="author" <?php selected( $instance['flavor'],  'author', true); ?>><?php _e('More by this Author (single post)')?></option>
		<option value="tag" <?php selected( $instance['flavor'],  'tag', true); ?>><?php _e('Related Content (single post)')?></option>
	</select>
</p>

<?php // select ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of posts:' ); ?></label>
	<select class="alignright widefat narrow"  name="<?php echo $this->get_field_name('number'); ?>" id="<?php echo $this->get_field_id('number'); ?>">
		<option value="-1" <?php selected( $instance['number'],  "-1", true); ?>>---</option>
		<?php for($i = 1; $i < 21; $i++){?>
		<option value="<?php echo $i?>" <?php selected( $instance['number'],  $i, true); ?>><?php echo $i?></option>
		<?php } ?>
	</select>
</p>

<?php // select ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('ad_after'); ?>"><?php _e( 'Ad After:' ); ?></label>
	<select class="alignright widefat narrow"  name="<?php echo $this->get_field_name('ad_after'); ?>" id="<?php echo $this->get_field_id('ad_after'); ?>">
		<option value="" <?php selected( $instance['ad_after'],  "", true); ?>>No Ad</option>
		<?php for($i = 1; $i < 6; $i++){?>
		<option value="<?php echo $i?>" <?php selected( $instance['ad_after'],  $i, true); ?>><?php echo $i?></option>
		<?php } ?>
	</select>
</p>

<?php // select ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('newsletter_after'); ?>"><?php _e( 'Newsletter After:' ); ?></label>
	<select class="alignright widefat narrow"  name="<?php echo $this->get_field_name('newsletter_after'); ?>" id="<?php echo $this->get_field_id('newsletter_after'); ?>">
		<option value="" <?php selected( $instance['newsletter_after'],  "", true); ?>>No Newsletter</option>
		<?php for($i = 1; $i < 6; $i++){?>
		<option value="<?php echo $i?>" <?php selected( $instance['newsletter_after'],  $i, true); ?>><?php echo $i?></option>
		<?php } ?>
	</select>
</p>

<?php // select ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('social_after'); ?>"><?php _e( 'Social After:' ); ?></label>
	<select class="alignright widefat narrow"  name="<?php echo $this->get_field_name('social_after'); ?>" id="<?php echo $this->get_field_id('social_after'); ?>">
		<option value="" <?php selected( $instance['social_after'],  "", true); ?>>No Social</option>
		<?php for($i = 1; $i < 6; $i++){?>
		<option value="<?php echo $i?>" <?php selected( $instance['social_after'],  $i, true); ?>><?php echo $i?></option>
		<?php } ?>
	</select>
</p>

<?php // select ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e( 'Order by:' ); ?></label>
	<select class="alignright widefat narrow"  name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
		<option value="menu_order" <?php selected( $instance['orderby'],  'menu_order', true); ?>><?php _e('Menu')?></option>
		<option value="date" <?php selected( $instance['orderby'],  'date', true); ?>><?php _e('Date')?></option>
		<option value="rand" <?php selected( $instance['orderby'],  'rand', true); ?>><?php _e('Random')?></option>
	</select>
</p>

<?php // select ?>
<p class="cf">
	<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e( 'Order:' ); ?></label>
	<select class="alignright widefat narrow"  name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
		<option value="DESC" <?php selected( $instance['order'],  'DESC', true); ?>><?php _e('Descending')?></option>
		<option value="ASC" <?php selected( $instance['order'],  'ASC', true); ?>><?php _e('Ascending')?></option>
	</select>
</p>

<?php // checkbox ?>
<p class="cf">
	<input id="<?php echo $this->get_field_id('infinite'); ?>" name="<?php echo $this->get_field_name('infinite'); ?>" type="checkbox" value="y" <?php checked( 'y', $instance['infinite'] ); ?> />
	<label for="<?php echo $this->get_field_id('infinite'); ?>"><?php _e('Infinite Scroll?') ?></label>
</p>
