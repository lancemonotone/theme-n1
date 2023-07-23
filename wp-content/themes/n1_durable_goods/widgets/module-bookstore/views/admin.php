<?php namespace N1_Durable_Goods;
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
        'disable'		=> ''
    )
);
$disable = strip_tags($instance['disable']);
?>
<p>
    <input id="<?php echo $this->get_field_id('disable'); ?>" name="<?php echo $this->get_field_name('disable'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['disable'] ); ?> />
    <label for="<?php echo $this->get_field_id('disable'); ?>"><?php _e('Disable?') ?></label>
</p>
<p>The settings for this module are located in <a href="<?php echo admin_url('admin.php?page=acf-options')?>">Site Settings</a>.</p>
