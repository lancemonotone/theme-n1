<?php
/**
 * Current issue table of contents. 
 * Module title (issue name and number)
 * Module subtitle (“Table of Contents”)
 * Issue ToC: Output automatically all issue contents in category order, with category headings. Article title and author only.
 * Featured articles. Display up to two featured articles from the issue.
 * Displayed just like other featured articles (e.g., as pullquote, or with or without image).
 */
 
//	Defaults
$instance = wp_parse_args( 
	(array) $instance, array( 
		'type'		=> 'article'
	) 
);
$type = strip_tags($instance['type']);
?>

<?php // radio ?>
<p>
	<label for="<?php echo $this->get_field_id('type')?>"><?php _e('Flavor?') ?></label><br>
	<input id="<?php echo $this->get_field_id('type')?>-0" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="article" <?php checked( 'article', $type ); ?> /> <?php _e('Article')?><br>
	<input id="<?php echo $this->get_field_id('type')?>-1" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="home" <?php checked( 'home', $type ); ?> /> <?php _e('Home Page')?><br>
	<input id="<?php echo $this->get_field_id('type')?>-2" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="landing-magazine" <?php checked( 'landing-magazine', $type ); ?> /> <?php _e('Magazine Landing Page')?><br>
	<input id="<?php echo $this->get_field_id('type')?>-3" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="landing-issue" <?php checked( 'landing-issue', $type ); ?> /> <?php _e('Issue Landing Page')?><br>
</p>