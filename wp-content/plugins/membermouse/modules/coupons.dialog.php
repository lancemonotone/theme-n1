<?php
$prod = new MM_Product();
$selectedProds = array();

$id = 0; 

if(isset($p->id))
{
	$id = $p->id;
}

$coupon = new MM_Coupon($id);
$prods = $coupon->getProducts();

if(!$coupon->isValid())
{
	$coupon->setStartDate(Date("m/d/Y"));
}

$editable = "";

if(MM_Coupon::isBeingUsed($coupon->getId()))
{
	$editable = "disabled='disabled'";
}

$products = MM_HtmlUtils::createCheckboxGroup(MM_Product::getAll(), "mm_products", $prods);
?>
<div id="mm-coupons-container">
	<table cellspacing="10">
		<tr>
			<td width="140"><?php echo _mmt("Name"); ?>*</td>
			<td><input type='hidden' id='id' value='<?php echo $coupon->getId(); ?>' />
				<input id="mm_coupon_name" type="text" value='<?php echo $coupon->getCouponName(); ?>' style='width:300px;' />
			</td>
		</tr>
		<tr>
			<td width="140"><?php echo _mmt("Coupon Code"); ?>*</td>
			<td>
				<input id="mm_coupon_code" <?php echo $editable; ?>  type="text" value='<?php echo strtoupper($coupon->getCouponCode()); ?>' />
			</td>
		</tr>
		<tr>
			<td width="140">Discount*</td>
			<td>
				<input id="mm_coupon_value" <?php echo $editable; ?> type="text" value='<?php echo $coupon->getCouponValue(); ?>' style="width:85px;" />
				<select id="mm_coupon_type" <?php echo $editable; ?> onchange="mmjs.typeChangeHandler();">
					<option value='<?php echo MM_Coupon::$TYPE_PERCENTAGE; ?>' <?php echo (($coupon->getCouponType() == MM_Coupon::$TYPE_PERCENTAGE)?"selected":""); ?>>% <?php echo _mmt("Off"); ?></option>	
					<option value='<?php echo MM_Coupon::$TYPE_DOLLAR; ?>' <?php echo (($coupon->getCouponType() == MM_Coupon::$TYPE_DOLLAR)?"selected":""); ?>>$ <?php echo _mmt("Off"); ?></option>	
					<option value='<?php echo MM_Coupon::$TYPE_FREE; ?>' <?php echo (($coupon->getCouponType() == MM_Coupon::$TYPE_FREE)?"selected":""); ?>><?php echo _mmt("Free"); ?></option>					
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<div style="width: 98%; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		<tr>
			<td width="140" style="vertical-align:top;"><?php echo _mmt("Subscription Options"); ?></td>
			<td>
				<div id="mm_subscription_options_section">
					<p style="margin:0px 0px 5px 0px;"><input id="mm_recurring_setting_first" <?php echo $editable; ?> name="mm_recurring_setting" type="radio" value='first' <?php echo (($coupon->getRecurringBillingSetting()=="first")?"checked":""); ?> /> <?php echo _mmt("Apply discount to the first charge only"); ?></p>
					<p style="margin:10px 0px 0px 0px;"><input id="mm_recurring_setting_all" <?php echo $editable; ?> name="mm_recurring_setting" type="radio" value='all' <?php echo (($coupon->getRecurringBillingSetting()=="all")?"checked":""); ?> /> <?php echo _mmt("Apply discount to all charges"); ?></p>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<div style="width: 98%; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		<tr>
			<td valign="top" width="140"><?php echo _mmt("Restrictions"); ?></td>
			<td>
				<table>
					<tr>
						<td width="140"><?php echo _mmt("Start Date"); ?></td>
						<td>
							<input id="mm_start_date" type="text" value='<?php echo $coupon->getStartDate(true); ?>' style="width:110px;" />
							<a onClick="jQuery('#mm_start_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
						</td>
					</tr>
					<tr>
						<td><?php echo _mmt("End Date"); ?></td>
						<td>
							<input id="mm_end_date" type="text" value='<?php echo $coupon->getEndDate(true); ?>' style="width:110px;" />
							<a onClick="jQuery('#mm_end_date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
						</td>
					</tr>
					<tr>
						<td><?php echo _mmt("Limit Quantity"); ?></td>
						<td>
							<input id="mm_quantity" type="text" style='width: 80px;' value='<?php echo $coupon->getQuantity(); ?>' />
						</td>
					</tr>
					<tr valign='top'>
						<td valign='top'><?php echo _mmt("Valid Products"); ?></td>
						<td valign='top'>
							<div style="overflow: auto; height: 165px; width: 250px; border: 1px solid #ccc;">
							<?php echo $products; ?>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
	
	<script type='text/javascript'>
		jQuery(document).ready(function(){
			jQuery("#mm_start_date").datepicker({
				dateFormat: "mm/dd/yy"
			});
			jQuery("#mm_end_date").datepicker({
				dateFormat: "mm/dd/yy"
			});
			mmjs.typeChangeHandler();
		});
	</script>
	
<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.createCoupon();" class="mm-ui-button blue"><?php echo _mmt("Save Coupon"); ?></a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button"><?php echo _mmt("Cancel"); ?></a>
</div>
</div>