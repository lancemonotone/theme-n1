<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_enable_wp_autop"]))
{
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ENABLE_WP_AUTOP, $_POST["mm_enable_wp_autop"]);
}

$enableWPAutoP = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ENABLE_WP_AUTOP);
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 

<script>
function updateWPContentForm()
{	
	if(jQuery("#mm_enable_wp_autop_cb").is(":checked")) 
	{
		jQuery("#mm_enable_wp_autop").val("0");
	} 
	else 
	{
		jQuery("#mm_enable_wp_autop").val("1");
	}
}
</script>

<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("WordPress Content Options");?></p>
    
	<div style="margin-top:10px;">
		<input id="mm_enable_wp_autop_cb" type="checkbox" <?php echo (($enableWPAutoP=="1")?"":"checked"); ?> onchange="updateWPContentForm();" />
		<?php echo _mmt("Disable WordPress auto-paragraph functionality on MemberMouse core pages");?>
		<input id="mm_enable_wp_autop" name="mm_enable_wp_autop" type="hidden" value="<?php echo $enableWPAutoP; ?>" />
		
		<span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020534-disable-wordpress-auto-paragraph-wpautop-functionality-from-core-pages" target="_blank"><?php echo _mmt("Learn more");?></a></span>
	</div>
</div>