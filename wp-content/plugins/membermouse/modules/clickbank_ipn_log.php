<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_ClickBankIPNLogView();

$memberIdValue = "";
if(!empty($_REQUEST["member_id"]))
{
	$memberIdValue = $_REQUEST["member_id"];
}

$receiptValue = "";
if(!empty($_REQUEST["ctransreceipt"]))
{
	$receiptValue = $_REQUEST["ctransreceipt"];
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
					<td>From</td>
					<td>
						<input id="from_date" name="from_date" type="text" value="<?php echo $fromDateValue; ?>" style="width: 152px" />
						<a onClick="jQuery('#from_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a> 
					</td>
				</tr>
				<tr>
					<td>To</td>
					<td>
						<input id="to_date" name="to_date" type="text" value="<?php echo $toDateValue; ?>" style="width: 152px"  />
						<a onClick="jQuery('#to_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
					</td>
				</tr>
			</table>
			</td>
			
			<!-- RIGHT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td>Member ID</td>
					<td><input id="member_id" name="member_id" type="text" value="<?php echo $memberIdValue; ?>" /></td>
				</tr>
				<tr>
					<td>Receipt ID</td>
					<td><input id="ctransreceipt" name="ctransreceipt" type="text" value="<?php echo $receiptValue; ?>" /></td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
	
	<input type="button" class="mm-ui-button blue" value="Show IPNs" onclick="mmjs.resetAndSearch();">
	<input type="button" class="mm-ui-button" value="Reset Form" onclick="mmjs.resetForm();">
</div>
</form>

<div style="width: 99%; margin-top: 10px; margin-bottom: 10px;" class="mm-divider"></div> 
	
<div id="mm-grid-container" style="width:99%">
	<?php echo $view->generateDataGrid($_REQUEST); ?>
</div>				

</div>