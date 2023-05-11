<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_ActivityLogView();
$module = MM_ModuleUtils::getModule();
$isMemberDetailsArea = ($module == MM_MODULE_MEMBER_DETAILS_ACTIVITY_LOG) ? true : false;

if($isMemberDetailsArea)
{
	// include activity log JS
	$activityLogJS = plugins_url("../resources/js/admin/mm-".MM_MODULE_ACTIVITY_LOG.".js", __FILE__);
	wp_enqueue_script(basename($activityLogJS), $activityLogJS, array('membermouse-global'), MemberMouse::getPluginVersion());
	
	if(isset($_REQUEST[MM_Session::$PARAM_USER_ID]))
	{
		$_REQUEST["member_id"] = $_REQUEST[MM_Session::$PARAM_USER_ID];
	}
}

$eventsList = array_merge(array(""=>"All"), MM_ActivityLog::getEventsList());

$eventTypeValue = "";
if(!empty($_REQUEST["event_type"]))
{
	$eventTypeValue = $_REQUEST["event_type"];
}

$eventOptions = MM_HtmlUtils::generateSelectionsList($eventsList, $eventTypeValue);

$memberIdValue = "";
if(!empty($_REQUEST["member_id"]))
{
	$memberIdValue = $_REQUEST["member_id"];
}

$fromDateValue = "";
if(!empty($_REQUEST["from_date"]))
{
	$fromDateValue = $_REQUEST["from_date"];
}

$toDateValue = "";
if(!empty($_REQUEST["to_date"]))
{
	$toDateValue = $_REQUEST["to_date"];
}
?>
<script type='text/javascript'>
jQuery(document).ready(function()
{
	jQuery("#from_date").datepicker({
		dateFormat: "mm/dd/yy"
	});
	jQuery("#to_date").datepicker({
		dateFormat: "mm/dd/yy"
	});

	mmjs.setIsMemberDetailsArea(<?php echo ($isMemberDetailsArea) ? "true" : "false"; ?>);
});

function viewInfo(eventId)
{
	jQuery("#mm-view-info-" + eventId).show();
	jQuery("#mm-view-info-" + eventId).dialog({autoOpen: true, width: "650", height: "450", modal: true});
}
</script>

<div class="mm-wrap">

<form method="post">
<div id="mm-form-container">
	<table style="width:600px;">
		<tr>
			<!-- LEFT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td><?php echo _mmt("From"); ?></td>
					<td>
						<input id="from_date" name="from_date" type="text" value="<?php echo $fromDateValue; ?>" style="width: 152px" placeholder="mm/dd/yyyy"  />
						<a onClick="jQuery('#from_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a> 
					</td>
				</tr>
				<tr>
					<td><?php echo _mmt("To"); ?></td>
					<td>
						<input id="to_date" name="to_date" type="text" value="<?php echo $toDateValue; ?>" style="width: 152px" placeholder="mm/dd/yyyy"   />
						<a onClick="jQuery('#to_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
					</td>
				</tr>
			</table>
			</td>
			
			<!-- RIGHT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td><?php echo _mmt("Event Type"); ?></td>
					<td>
						<select id='event_type' name='event_type'><?php echo $eventOptions; ?></select>
					</td>
				</tr>
				<?php if(!$isMemberDetailsArea) { ?>
				<tr>
					<td><?php echo _mmt("Member ID"); ?></td>
					<td><input id="member_id" name="member_id" type="text" value="<?php echo $memberIdValue; ?>" /></td>
				</tr>
				<?php } else { ?>
					<input id="member_id" name="member_id" type="hidden" value="<?php echo $memberIdValue; ?>" />
				<?php } ?>
			</table>
			</td>
		</tr>
	</table>
	
	<input type="button" class="mm-ui-button blue" value="<?php echo _mmt("Show Events"); ?>" onclick="mmjs.resetAndSearch();">
	<input type="button" class="mm-ui-button" value="<?php echo _mmt("Reset Form"); ?>" onclick="mmjs.resetForm();">
</div>
</form>

<div style="width: 99%; margin-top: 10px; margin-bottom: 10px;" class="mm-divider"></div> 
	
<div id="mm-grid-container" style="width:99%">
	<?php echo $view->generateDataGrid($_REQUEST); ?>
</div>				

</div>