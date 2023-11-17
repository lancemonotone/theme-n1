<?php
/**
 * This area of the homepage shows the latest four posts from the Paper Monument 
 * category in Online Only. Display post title (with click-through link) and 
 * author only. In addition, admins will need the ability to edit:
 * Module title (e.g. “Paper Monument”; text input)
 * Module subtitle (“Contemporary writing on art”; text input)
 */
 
//	Defaults
$instance = wp_parse_args( 
	(array) $instance, array( 
		'title' 	=> __('Paper Monument'),
		'subtitle' 	=> __('Contemporary writing on art'),
		'img_src'	=> '',
	) 
);
$title = strip_tags($instance['title']);
$subtitle = strip_tags($instance['subtitle']);
$img_src = strip_tags($instance['img_src']); 
?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Module Title:' ); ?></label><br>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title?>"/>
</p>
<p>
	<label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e( 'Module Subtitle:' ); ?></label><br>
	<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo $subtitle?>"/>
</p>

<p>
	<label for="<?php echo $this->get_field_id('img_src'); ?>"><?php _e('Image'); ?>:</label><br>
	<!-- Image Thumbnail -->
	<img class="custom_media_img" src="<?php echo $img_src?>" style="max-height:150px;max-width:100%;margin:10px 0;text-align:center;display:block;" />
	<!-- Upload button and text field -->
	<input class="custom_media_src" id="<?php echo $this->get_field_id('img_src'); ?>" type="text" name="<?php echo $this->get_field_name('img_src'); ?>" value="<?php echo $img_src?>" style="display:none;">
	<a href="javascript:void(0);" class="button custom_media_upload">Upload</a>
	<a href="javascript:void(0);" class="custom_media_delete" <?php echo $img_src ? '' : 'style="display: none;"'?>>Remove?</a>
	
</p>