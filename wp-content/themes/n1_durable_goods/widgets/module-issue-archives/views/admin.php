<?php namespace N1_Durable_Goods;
/**
 * This area of the homepage shows the latest four posts from the Paper Monument
 * category in Online Only. Display post title (with click-through link) and
 * author only. In addition, admins will need the ability to edit:
 * Module title (e.g. �Paper Monument�; text input)
 * Module subtitle (�Contemporary writing on art�; text input)
 */

//	Defaults
$instance = wp_parse_args(
	(array) $instance, array(
		'title' => __('The Magazine'),
		'subtitle' => __('Ten years of n+1')
	)
);
$title = strip_tags($instance['title']);
$subtitle = strip_tags($instance['subtitle']);
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Module Title:' ); ?></label><br>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title?>"/>
</p>
<p>
<label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e( 'Module Subtitle:' ); ?></label><br>
<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo $subtitle?>"/>
</p>
