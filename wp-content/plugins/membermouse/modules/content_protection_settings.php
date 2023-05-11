<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_allow_overdue_access"]))
{
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ALLOW_OVERDUE_ACCESS, $_POST["mm_allow_overdue_access"]);
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_DRIP_CONTENT_TIME_SETTING, $_POST["mm_drip_content_time_setting"]);
}

$allowOverdueAccess = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ALLOW_OVERDUE_ACCESS);
$dripContentTimeSetting = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_DRIP_CONTENT_TIME_SETTING);
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 

<script>
function updateContentProtectionForm()
{	
	if(jQuery("#mm_allow_overdue_access_cb").is(":checked")) 
	{
		jQuery("#mm_allow_overdue_access").val("1");
	} 
	else 
	{
		jQuery("#mm_allow_overdue_access").val("0");
	}
}
</script>

<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("Content Protection Settings"); ?></p>

	<div style="margin-top:10px;">
		<input id="mm_allow_overdue_access_cb" type="checkbox" <?php echo (($allowOverdueAccess=="1")?"checked":""); ?> onchange="updateContentProtectionForm();" />
		<?php echo _mmt("Allow members with overdue memberships or bundles to access protected content"); ?> <a href="http://support.membermouse.com/support/solutions/articles/9000020507-configure-access-to-content-when-status-is-overdue" target="_blank"><?php echo _mmt("Learn more"); ?></a>
		<input id="mm_allow_overdue_access" name="mm_allow_overdue_access" type="hidden" value="<?php echo $allowOverdueAccess; ?>" />
	</div>
	<div style="margin-top:10px; width:600px;">
	<p><?php echo sprintf(_mmt("Content that is dripped to members becomes available at 12 AM. Based on the setting below this will happen at 12 AM server time or 12 AM local time. Local time is based on the current WordPress timezone setting of %s. You can change this setting on %sWordPress Settings > General > Timezone%s.%sWould you like content to be dripped at 12 AM server time or local time?"),'<code><?php echo MM_OptionUtils::getOption("timezone_string"); ?></code>','<em>','</em>','<br/><br/>'); ?> 
	</p>
		<div>
			<input name="mm_drip_content_time_setting" value='<?php echo MM_ProtectedContentEngine::$TIME_SETTING_SERVER; ?>' type="radio" <?php echo (($dripContentTimeSetting != MM_ProtectedContentEngine::$TIME_SETTING_LOCAL)?"checked":""); ?>  />
			<?php echo _mmt("Use server time where it's currently"); ?> <code><?php echo MM_Utils::getCurrentTime("Y-m-d g:i a"); ?></code>
		</div>
		<div style="margin-top:10px;">
			<input name="mm_drip_content_time_setting"  value='<?php echo MM_ProtectedContentEngine::$TIME_SETTING_LOCAL; ?>' type="radio" <?php echo (($dripContentTimeSetting == MM_ProtectedContentEngine::$TIME_SETTING_LOCAL)?"checked":""); ?>  />
			<?php echo _mmt("Use local time where it's currently"); ?> <code><?php echo MM_Utils::dateToLocal(MM_Utils::getCurrentTime(), "Y-m-d g:i a"); ?></code>
		</div>
		
	</div>
</div>