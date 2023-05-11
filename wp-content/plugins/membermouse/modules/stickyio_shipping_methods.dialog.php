<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
	$stickyioShippingMethod = new MM_StickyioShippingMethod($p->id);
	$stickyioService = MM_PaymentServiceFactory::getPaymentService(MM_PaymentService::$STICKYIO_SERVICE_TOKEN);
?>
<div id="mm-form-container">
	<table cellspacing="10">
		<tr>
			<td width="200">MemberMouse Shipping Method</td>
			<td>
				<select name='mm_option_key' id='mm_option_key'>
				<?php 
					$unmappedShippingMethods = MM_StickyioShippingMethod::getUnmappedShippingMethods($stickyioShippingMethod->getMMOptionKey());
					
					echo MM_HtmlUtils::generateSelectionsList($unmappedShippingMethods, $stickyioShippingMethod->getMMOptionKey()); 
				?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 98%; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td width="150">Sticky.io Shipping Method</td>
			<td>
				<select name='stickyio_shipping_method_id_selector' id='stickyio_shipping_method_id_selector'>
				<?php echo MM_HtmlUtils::generateSelectionsList($stickyioService->getShippingMethods(), $stickyioShippingMethod->getStickyioShippingMethodId()); ?>
				</select>
				<a href="javascript:mmjs.getStickyioShippingDescription('');" title="View Sticky.io Shipping Method Info"><?php echo MM_Utils::getIcon("info-circle", "blue", "1.3em", "2px;"); ?></a>
			</td>
		</tr>
	</table>
	
	<input id='id' type='hidden' value='<?php if($stickyioShippingMethod->getId() != 0) { echo $stickyioShippingMethod->getId(); } ?>' />
	<input id='stickyio_shipping_method_name' name='stickyio_shipping_method_name' type='hidden' value='<?php echo $stickyioShippingMethod->getStickyioShippingMethodName(); ?>' />
	<input id='stickyio_shipping_method_id' name='stickyio_shipping_method_id' type='hidden' value='<?php echo $stickyioShippingMethod->getStickyioShippingMethodId(); ?>' />
</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.save();" class="mm-ui-button blue">Save Shipping Method Mapping</a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button">Cancel</a>
</div>
</div>