<?php 
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$selectedProducts = isset($_REQUEST['mm-product-ids'])?$_REQUEST['mm-product-ids']:"";
$selectedCoupons = isset($_REQUEST['mm-coupon-ids'])?$_REQUEST['mm-coupon-ids']:"";

?>
<div id="mm-form-container" style="background-color: #EAF2FA; padding-top:2px; padding-left:8px; padding-bottom:8px;">
	<form id="manage-transactions-search-form" action="javascript:void(0);">
    	<table>
    		<tr>
    			<!-- LEFT COLUMN -->
    			<td valign="top" style="width:380px;">
    			<table cellspacing="5">
    				<tr>
    					<td><?php echo _mmt("From"); ?></td>
    					<td>
    						<input id="mm-from-date" type="date" size="25" placeholder="mm/dd/yyyy" /> 
    						<a role="button" class="datepicker-calendar" style="display:none" onClick="jQuery('#mm-from-date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
    					</td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("To"); ?></td>
    					<td>
    						<input id="mm-to-date" type="date" size="25" placeholder="mm/dd/yyyy" />
    						<a role="button" class="datepicker-calendar" style="display:none" onClick="jQuery('#mm-to-date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
    					</td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("Order #"); ?></td>
    					<td><input id="mm-order-number" type="text" size="25" /></td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("Member ID"); ?></td>
    					<td><input id="mm-member-id" type="text" size="25" /></td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("First Name"); ?></td>
    					<td><input id="mm-first-name" type="text" size="25" /></td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("Last Name"); ?></td>
    					<td><input id="mm-last-name" type="text" size="25" /></td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("Email"); ?></td>
    					<td><input id="mm-email" type="text" size="25" /></td>
    				</tr>
    			</table>
    			</td>
    			
    			<!-- CENTER COLUMN -->
    			<td valign="top">
    			<table cellspacing="5" style="width:450px">
    				<tr>
    					<td><?php echo _mmt("Transaction Types"); ?></td>
    					<td>
    						<select id="mm-transaction-types[]" multiple="multiple" style="width:300px;" size="3">
    							<option value="<?php echo MM_TransactionLog::$TRANSACTION_TYPE_PAYMENT; ?>"><?php echo _mmt("Initial Payments");?></option>
    							<option value="<?php echo MM_TransactionLog::$TRANSACTION_TYPE_RECURRING_PAYMENT; ?>"><?php echo _mmt("Recurring Payments");?></option>
    							<option value="<?php echo MM_TransactionLog::$TRANSACTION_TYPE_REFUND; ?>"><?php echo _mmt("Refunds");?></option>
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("Products"); ?></td>
    					<td>
    						<select id="mm-product-ids[]" multiple="multiple" style="width:300px;" size="8">
    						<?php echo MM_HtmlUtils::getProducts($selectedProducts); ?>
    						</select>
    					</td>
    				</tr>
    			</table>
    			</td>
    			
    			<!-- RIGHT COLUMN -->
    			<td valign="top">
    			<table cellspacing="5">
    				<?php
    					$availableCoupons = MM_Coupon::getAllCodes(true);
    					
    					if(count($availableCoupons) > 0)
    					{
    				?>
    				<tr>
    					<td><?php echo _mmt("Coupons"); ?></td>
    					<td>
    						<select id="mm-coupon-ids[]" multiple="multiple" style="width:200px;" size="5">
    						<?php echo MM_HtmlUtils::getNonFreeCouponCodes($selectedCoupons); ?>
    						</select>
    						<table style="width:280px;"><tr>
    							<td><?php echo MM_Utils::getInfoIcon(); ?></td>
    							<td style="font-size:11px; line-height:14px;"><em><?php echo _mmt("Free coupons are not included in this list because there is no transaction associated with them. To see them,"); ?> <a href='?page=<?php echo MM_MODULE_LOGS; ?>&module=<?php echo MM_MODULE_COUPON_LOG; ?>'><?php echo _mmt("use the coupon log"); ?></a>.</em></td>
    						</tr></table>
    					</td>
    				</tr>
    				<?php } ?>
    				
    				<tr>
    					<td><?php echo _mmt("Affiliate ID"); ?></td>
    					<td><input id="mm-affiliate-id" type="text" size="25" /></td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("Sub-Affiliate ID"); ?></td>
    					<td><input id="mm-sub-affiliate-id" type="text" size="25" /></td>
    				</tr>
    				<tr>
    					<td><?php echo _mmt("Service Provider"); ?></td>
    					<td><input id="mm-payment-service" type="text" size="25" /></td>
    				</tr>
    				<!--  column 3 placeholder -->
    			</table>
    			</td>
    		</tr>
    	</table>
    	
    	<input type="button" class="mm-ui-button blue" value="<?php echo _mmt("Search"); ?>" onclick="mmjs.processSearchForm();">
    	<input type="button" class="mm-ui-button" value="<?php echo _mmt("Reset Form"); ?>" onclick="mmjs.resetForm();">
	</form>
</div>

<script type='text/javascript'>
	jQuery(document).ready(function(){
	    var isDateSupported = () => {
			var input = document.createElement('input');
			input.setAttribute('type', 'date');
			input.setAttribute('value', 'a');
			return (input.value !== 'a'); //native date controls discard invalid date values, so we can use this to test for support
		};
		
		if (!isDateSupported())
		{
			//fallback to jQueryUI
    		jQuery("#mm-from-date").datepicker({
    				dateFormat: "mm/dd/yy"
    		});
    		jQuery("#mm-to-date").datepicker({
    				dateFormat: "mm/dd/yy"
    		});
		}
		else if (!window.chrome)
		{
			//the chrome native control has a calendar icon inside of it, so only show the mm calendar icon if the browser is not chrome
			jQuery(".datepicker-calendar").show();
		}
		
		jQuery("#mm-form-container :input").keypress(function(e) {
	        if(e.which == 13) {
	            jQuery(this).blur();
	            mmjs.processSearchForm();
	        }
	    });
				
	});
</script>