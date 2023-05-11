<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_CaptchaIPNLogView();

$score = "";
if(!empty($_REQUEST["score_val"]))
{
	$score = $_REQUEST["score_val"];
}

$scoreOp = "";
if(!empty($_REQUEST["score_operation"]))
{
    $scoreOp = $_REQUEST["score_operation"];
}

$passFail = "";
if(!empty($_REQUEST["pass_fail"]))
{
	$passFail = $_REQUEST["pass_fail"];
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

$scoreValuesList = [];
$scoreValuesList[""] = "";
$scoreValuesList["0.0"] = "0.0";
$scoreValuesList["0.1"] = "0.1";
$scoreValuesList["0.2"] = "0.2";
$scoreValuesList["0.3"] = "0.3";
$scoreValuesList["0.4"] = "0.4";
$scoreValuesList["0.5"] = "0.5";
$scoreValuesList["0.6"] = "0.6";
$scoreValuesList["0.7"] = "0.7";
$scoreValuesList["0.8"] = "0.8";
$scoreValuesList["0.9"] = "0.9";
$scoreValuesList["1.0"] = "1.0";

$scoreValues = MM_HtmlUtils::generateSelectionsList($scoreValuesList, $score);

$scoreOperationsList = [];
$scoreOperationsList["="] = "Equals";
$scoreOperationsList[">"] = "Is Greater Than";
$scoreOperationsList["<"] = "Is Less Than";

$scoreOperations = MM_HtmlUtils::generateSelectionsList($scoreOperationsList, $scoreOp);

$passFailList = [];
$passFailList[""] = "Show All";
$passFailList["pass"] = "Pass Only";
$passFailList["fail"] = "Fail Only";

$passFailOptions = MM_HtmlUtils::generateSelectionsList($passFailList, $passFail);
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
	<table style="width:700px;">
		<tr>
			<!-- LEFT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td>From</td>
					<td>
						<input id="from_date" name="from_date" type="text" value="<?php echo $fromDateValue; ?>" style="width: 152px" placeholder="mm/dd/yyyy"  />
						<a onClick="jQuery('#from_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a> 
					</td>
				</tr>
				<tr>
					<td>To</td>
					<td>
						<input id="to_date" name="to_date" type="text" value="<?php echo $toDateValue; ?>" style="width: 152px" placeholder="mm/dd/yyyy"  />
						<a onClick="jQuery('#to_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
					</td>
				</tr>
			</table>
			</td>
			
			<!-- RIGHT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td>Score</td>
					<td>
						<select id='score_operation' name='score_operation'><?php echo $scoreOperations; ?></select>
						<select id='score_val' name='score_val'><?php echo $scoreValues; ?></select>
					</td>
				</tr>
				<tr>
					<td>Pass/Fail</td>
					<td><select id='pass_fail' name='pass_fail'><?php echo $passFailOptions; ?></select></td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
	
	<input type="button" class="mm-ui-button blue" value="Show Entries" onclick="mmjs.resetAndSearch();">
	<input type="button" class="mm-ui-button" value="Reset Form" onclick="mmjs.resetForm();">
</div>
</form>

<div style="width: 99%; margin-top: 10px; margin-bottom: 10px;" class="mm-divider"></div> 
	
<div id="mm-grid-container" style="width:99%">
	<?php echo $view->generateDataGrid($_REQUEST); ?>
</div>				

</div>