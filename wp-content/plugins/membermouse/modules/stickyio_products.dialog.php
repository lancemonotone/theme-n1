<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
$stickyioProduct = new MM_StickyioProduct($p->id);
$limeLightService = MM_PaymentServiceFactory::getPaymentService(MM_PaymentService::$STICKYIO_SERVICE_TOKEN);
?>

<div id="mm-form-container">
	<table cellspacing="10">
		<tr>
			<td width="150">MemberMouse Product</td>
			<td>
				<select name='mm_product_id' id='mm_product_id' onchange="mmjs.getMMProductDescription();">
				<?php 
					$unmappedProducts = MM_StickyioProduct::getUnmappedProducts();
					
					// add currently selected product
					if($stickyioProduct->isValid())
					{
						$product = new MM_Product($stickyioProduct->getMMProductId());
						
						if($product->isValid())
						{
							$unmappedProducts[$stickyioProduct->getMMProductId()] = $product->getName();
						}
					}
					
					echo MM_HtmlUtils::generateSelectionsList($unmappedProducts, $stickyioProduct->getMMProductId()); 
				?>
				</select>
				
				<div id="mm_product_description" style="margin-top:10px;"></div>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 98%; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td width="150">Campaign</td>
			<td><?php 
						$productId = $stickyioProduct->getStickyioProductId();
						if(empty($productId)) { 
					?>
				<select name='stickyio_campaign_id[]' id='stickyio_campaign_id' onchange='mmjs.getStickyioOffers();' multiple='true' style="height: 200px">
				<?php echo MM_HtmlUtils::generateSelectionsList($limeLightService->getCampaigns(), $stickyioProduct->getStickyioCampaignId()); ?>
				</select>
				<?php }else{ ?>
				
				<select name='stickyio_campaign_id[]' id='stickyio_campaign_id' onchange='mmjs.getStickyioOffers();'  >
				<?php echo MM_HtmlUtils::generateSelectionsList($limeLightService->getCampaigns(), $stickyioProduct->getStickyioCampaignId()); ?>
				</select>
				<?php }?>
			</td>
		</tr> 
		<tr id="offer_row"  style="display:none;" >
			<td width="150">Offer</td>
			<td>
				<div id="stickyio_offer_display_section">
					<?php 
						$offerId = $stickyioProduct->getStickyioOfferId();
						if(!empty($offerId)) { 
					?>
					<span style="font-family:courier;"><?php echo $stickyioProduct->getStickyioOfferName(); ?> [<?php echo $stickyioProduct->getStickyioOfferId(); ?>]</span>
					<?php }  ?>
					<br /> 
					 
				</div>
				<div id="stickyio_select_offer_section" style="display:none;">
					<select name='stickyio_offer_id_selector' id='stickyio_offer_id_selector' onchange='mmjs.getStickyioBillingModelsFromOffer();'  style="display:none;">
					</select>
					 <br />  
				</div>
				
			</td>
		</tr>
		<tr id="billing_model_row" style="display:none;"  >
			<td width="150">Billing Model</td>
			<td>
				<div id="stickyio_billing_model_display_section">
					<?php 
						$offerId = $stickyioProduct->getStickyioBillingModelId();
						if(!empty($offerId)) { 
					?>
					<span style="font-family:courier;"><?php echo $stickyioProduct->getStickyioBillingModelName(); ?> [<?php echo $stickyioProduct->getStickyioBillingModelId(); ?>]</span>
					<?php }   ?>
					<br /> 
					 
				</div>
				
				<div id="stickyio_select_billing_model_section" style="display:none;">
					<select name='stickyio_billing_model_id_selector' id='stickyio_billing_model_id_selector' style="display:none;">
					</select>
					 <br />  
				</div>
				
			</td>
		</tr>
		<tr id="product_row" style="display:none;" >
			<td width="150">Product</td>
			<td>
				<div id="stickyio_product_display_section">
					<?php 
						$productId = $stickyioProduct->getStickyioProductId();
						if(!empty($productId)) { 
					?>
					<span style="font-family:courier;"><?php echo $stickyioProduct->getStickyioProductName(); ?> [<?php echo $stickyioProduct->getStickyioProductId(); ?>]</span>
					<?php } else { ?>
					<a href="javascript:mmjs.getStickyioProducts();">Load Lime Light Products</a>
					<?php } ?>
					<a href="javascript:mmjs.getStickyioProducts();" title="Get Lime Light Products"><?php echo MM_Utils::getIcon("download", "green", "1.4em", "2px;"); ?></a>
					<a href="javascript:mmjs.getStickyioProductDescription('');" title="View Lime Light Product Info"><?php echo MM_Utils::getIcon("info-circle", "blue", "1.3em", "1px;"); ?></a>
					<br />
					<?php if($stickyioProduct->getStickyioProductId()>0){ ?>
						<div style='clear:both; height: 10px;'></div>
					<input type='checkbox' onchange="mmjs.doToggleCampaignSelection();" id='stickyio_map_all_associated_campaigns' name='stickyio_map_all_associated_campaigns' value='1' <?php echo (($stickyioProduct->getStickyioCampaignId()==0)?"checked":"");?> /> Use the product mapping specified above across all Lime Light campaigns. <?php echo MM_Utils::getIcon("info-circle", "blue", "1.3em", "1px;","When this is checked, Lime Light product whatever will be mapped to the MemberMouse product whatever regardless of what Lime Light campaign is specified. This can be overridden for specific campaigns in a separate mapping."); ?>
					<?php } ?>
					 
				</div>
				
				<div id="stickyio_select_product_section" style="display:none;">
					<select name='stickyio_product_id_selector' id='stickyio_product_id_selector' style="display:none;">
					</select>
					<a href="javascript:mmjs.getStickyioProductDescription('');" title="View Lime Light Product Info"><?php echo MM_Utils::getIcon("info-circle", "blue", "1.3em", "1px;"); ?></a>
					<br />
						<div style='clear:both; height: 10px;'></div>
					<input type='checkbox' onchange="mmjs.doToggleCampaignSelection();" id='stickyio_map_all_associated_campaigns' name='stickyio_map_all_associated_campaigns' value='1' /> Use the product mapping specified above across all Lime Light campaigns. <?php echo MM_Utils::getIcon("info-circle", "blue", "1.3em", "1px;","When this is checked, Lime Light product whatever will be mapped to the MemberMouse product whatever regardless of what Lime Light campaign is specified. This can be overridden for specific campaigns in a separate mapping."); ?>
				</div>
				
			</td>
		</tr>
	</table>
	
	<input id='id' type='hidden' value='<?php if($stickyioProduct->getId() != 0) { echo $stickyioProduct->getId(); } ?>' />
	<input id='stickyio_campaign_name' name='stickyio_campaign_name' type='hidden' />
	<input id='stickyio_offer_name' name='stickyio_offer_name' type='hidden' />
	<input id='stickyio_offer_id' name='stickyio_offer_id' type='hidden' value='<?php echo $stickyioProduct->getStickyioOfferId(); ?>' />
	<input id='stickyio_billing_model_name' name='stickyio_billing_model_name' type='hidden' />
	<input id='stickyio_billing_model_id' name='stickyio_billing_model_id' type='hidden' value="<?php echo $stickyioProduct->getStickyioBillingModelId(); ?>" />
	<input id='stickyio_campaign_map_all' name='stickyio_campaign_map_all' type='hidden' value="0" />
	<input id='stickyio_product_name' name='stickyio_product_name' type='hidden' value='<?php echo $stickyioProduct->getStickyioProductName(); ?>' />
	<input id='stickyio_product_id' name='stickyio_product_id' type='hidden' value='<?php echo $stickyioProduct->getStickyioProductId(); ?>' />
</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.save();" class="mm-ui-button blue">Save Product Mapping</a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button">Cancel</a>
</div>
</div>
<script>
jQuery( document ).ready(function() {
	mmjs.getMMProductDescription();
	<?php if($stickyioProduct->getStickyioProductId()>0){?>
	mmjs.doToggleCampaignSelection();
	mmjs.getStickyioOffers();
	<?php }?>
});
</script>