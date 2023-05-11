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
$showPreviewBarDesc = "When this is checked MemberMouse will display the preview settings bar when you're viewing your site as an administrator.";
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
    <p class="mm-header-text"><?php echo _mmt("Preview Settings Bar Options"); ?> <span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020295-show-hide-the-preview-settings-bar" target="_blank"><?php echo _mmt("Learn More"); ?></a></span></p>
    
	<div style="margin-top:10px;">
		<input id="mm_show_preview_settings_bar_cb" type="checkbox" <?php echo (($showPreviewBar=="1")?"checked":""); ?> onchange="updatePreviewSettingsBarForm();" />
		<?php echo _mmt("Show Preview Settings Bar"); ?><?php echo MM_Utils::getInfoIcon($showPreviewBarDesc); ?>
		<input id="mm_show_preview_settings_bar" name="mm_show_preview_settings_bar" type="hidden" value="<?php echo $showPreviewBar; ?>" />
	</div>
</div>