<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_CouponLogView();

$couponCode = "";
if(!empty($_REQUEST["coupon_code"]))
{
    $couponCode = $_REQUEST["coupon_code"];
}

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
});
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
					<td><?php echo _mmt("From"); ?></td>
					<td>
						<input id="from_date" name="from_date" type="text" value="<?php echo $fromDateValue; ?>" style="width: 152px" placeholder="mm/dd/yyyy"  />
						<a onClick="jQuery('#from_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a> 
					</td>
				</tr>
				<tr>
					<td><?php echo _mmt("To"); ?></td>
					<td>
						<input id="to_date" name="to_date" type="text" value="<?php echo $toDateValue; ?>" style="width: 152px" placeholder="mm/dd/yyyy"  />
						<a onClick="jQuery('#to_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
					</td>
				</tr>
			</table>
			</td>
			
			<!-- RIGHT COLUMN -->
			<td valign="top">
			<table cellspacing="5" style="width:300px">
				<tr>
					<td><?php echo _mmt("Member ID"); ?></td>
					<td><input id="member_id" name="member_id" type="text" style="width:100px" value="<?php echo $memberIdValue; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo _mmt("Coupon Code"); ?></td>
					<td>
						<select id='coupon_code' name='coupon_code'>
							<?php
							global $wpdb;
							
							$sql = "SELECT id, coupon_code FROM ".MM_TABLE_COUPONS." WHERE is_gift = '0';";
							$results = $wpdb->get_results($sql);
							$couponCodes = array();
							$couponCodes[""] = "Show All";
							foreach ($results as $row)
							{
							    $couponCodes[$row->coupon_code] = strtoupper($row->coupon_code);
							}
							echo MM_HtmlUtils::generateSelectionsList($couponCodes, $couponCode);
							?>
						</select>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
	
	<input type="button" class="mm-ui-button blue" value="Show Activity" onclick="mmjs.resetAndSearch();">
	<input type="button" class="mm-ui-button" value="Reset Form" onclick="mmjs.resetForm();">
</div>
</form>

<div style="width: 99%; margin-top: 10px; margin-bottom: 10px;" class="mm-divider"></div> 
	
<div id="mm-grid-container" style="width:99%">
	<?php echo $view->generateDataGrid($_REQUEST); ?>
</div>				

</div>