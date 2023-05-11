<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
if(isset($_POST["mm_cleanup_interval"]))
{
	if(isset($_POST["mm_enable_activity_log_cleanup"]) && $_POST["mm_enable_activity_log_cleanup"] == "on") {
		if(intval($_POST["mm_cleanup_interval"]) < 1)
		{
		?>
			<script>
			alert("The activity log cleanup interval must be greater than 0");
			</script>
		<?php
		}
		else
		{
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ACTIVITY_LOG_CLEANUP_ENABLED, "1");
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ACTIVITY_LOG_CLEANUP_INTERVAL, $_POST["mm_cleanup_interval"]);
		}
	}
	else 
	{
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ACTIVITY_LOG_CLEANUP_ENABLED, "0");
	}
}

$enabled = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ACTIVITY_LOG_CLEANUP_ENABLED);
$cleanupInterval = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ACTIVITY_LOG_CLEANUP_INTERVAL);

if($cleanupInterval === false || $cleanupInterval === "")
{
	$cleanupInterval = MM_OptionUtils::$DEFAULT_ACTIVITY_LOG_CLEANUP_INTERVAL;
}
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 
<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("Activity Log Settings"); ?> <span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020487-configure-activity-log-cleanup" target="_blank"><?php echo _mmt("Learn more"); ?></a></span></p>
   
	<div style="margin-top:10px;">
		<input onchange="mmjs.showActivityLogForm()" id="mm-cb-enable-activity-log-cleanup" name="mm_enable_activity_log_cleanup" type="checkbox"  <?php echo (($enabled=="0")?"":"checked"); ?>  />
		<?php echo _mmt("Enable activity log cleanup"); ?>
			
		<input id="mm-enable-activity-log-cleanup" type="hidden" />
	</div>
	<div id="mm-activity-log-cleanup" style="margin-top:5px; display:none; padding-top:5px;">
		<?php echo sprintf(_mmt("Delete activity log entries older than %s days"),'<input id="mm-cleanup-interval" name="mm_cleanup_interval" type="text" size="5" value="'.$cleanupInterval.'" /> '); ?>		
	</div>
</div>

<script type='text/javascript'>
mmjs.showActivityLogForm();
</script>