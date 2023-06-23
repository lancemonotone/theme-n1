<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_show_preview_settings_bar"]))
{
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SHOW_PREVIEW_BAR, $_POST["mm_show_preview_settings_bar"]);
}

$showPreviewBar = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SHOW_PREVIEW_BAR);
$showPreviewBarDesc = "When this is checked, content protection will be enabled for the administrator while viewing the site. A preview settings bar will be displayed at the bottom of all pages. You'll use this bar to specify which membership level, bundles and days as member values you want to be applied while previewing the site.";
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 

<script>
function updatePreviewSettingsBarForm()
{	
	if(jQuery("#mm_show_preview_settings_bar_cb").is(":checked")) 
	{
		jQuery("#mm_show_preview_settings_bar").val("1");
	} 
	else 
	{
		jQuery("#mm_show_preview_settings_bar").val("0");
	}
}
</script>

<div class="mm-wrap">
	<a name="preview-settings-bar-options"></a>
    <p class="mm-header-text"><?php echo _mmt("Site Preview Options"); ?> <span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020295-show-hide-the-preview-settings-bar" target="_blank"><?php echo _mmt("Learn More"); ?></a></span></p>
    
	<div style="margin-top:10px;">
		<input id="mm_show_preview_settings_bar_cb" type="checkbox" <?php echo (($showPreviewBar=="1")?"checked":""); ?> onchange="updatePreviewSettingsBarForm();" />
		<?php echo _mmt("Enable Site Preview"); ?>
		<input id="mm_show_preview_settings_bar" name="mm_show_preview_settings_bar" type="hidden" value="<?php echo $showPreviewBar; ?>" />
		<p style="margin-left:10px; width:500px;"><?php echo _mmt($showPreviewBarDesc); ?></p>
	</div>
</div>