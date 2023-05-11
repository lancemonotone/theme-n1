<?php 
$productsExist = false;
if (isset($p->clickbank_products) && is_array($p->clickbank_products) && (count($p->clickbank_products) > 0)) { 
	$productsExist = true;
	$membermouseProducts = array();
	$membermouseProducts['none'] = "(none)";
	if (isset($p->membermouse_products) && is_array($p->membermouse_products))
	{
		foreach ($p->membermouse_products as $productId=>$productObj)
		{
			$productPrice = number_format($productObj->price,2,'.','');
			$membermouseProducts[$productId] = "[ID: {$productObj->id}] {$productObj->name} (\${$productPrice})";
		}
	}
}

$skuName = "@sku";
?> 
<style>
.loadmore
{
    text-decoration:underline; 
    font-size: 11px;
    color: #0073aa;
    transition-property: border,background,color;
    transition-duration: .05s;
    transition-timing-function: ease-in-out;
    cursor:pointer;
}
</style>
<p> 
<?php   echo count($p->clickbank_products); ?> of <?php echo $p->clickbank_total_records; ?> products showing 
 
</p>
<div id="mm-clickbank-configure-products-container">
    <?php if ($productsExist) { ?>
	<table id="mm-clickbank-configure-products-table" class="widefat">
		<thead>
		<tr>
			<th>ClickBank Product</th>
			<th>&nbsp;</th>
			<th>MemberMouse Product</th>
		</tr>
		</thead> 
		<?php foreach ($p->clickbank_products as $cbp) { ?>
		<tr>
			<td width="200">
				ID:  <?php echo $cbp->$skuName; ?></br>
				Title: <?php echo $cbp->title; ?>
			</td>
			<td>&nbsp;</td>
			<td>
				<select name='clickbank_product_mapping[<?php echo $cbp->$skuName; ?>]'>
					<?php echo MM_HtmlUtils::generateSelectionsList($membermouseProducts,$cbp->mapped?$cbp->membermouse_product_id:null); ?>
				</select>
			</td>
		</tr>
		<?php } ?>
	</table>
	<?php if($p->clickbank_next_page>0){?>
			<a class="loadmore" onclick="loadMoreProducts(<?php echo $p->clickbank_next_page; ?>);">Load <?php echo $p->clickbank_total_records_diff; ?> More Products</a>
	<?php } ?>
	<?php if($p->clickbank_prev_page>0){?>
			<a class="loadmore" onclick="loadMoreProducts(<?php echo $p->clickbank_prev_page; ?>);">Load <?php echo $p->clickbank_total_records_max; ?> previous products</a>
	<?php } ?>
	<?php } else { ?>
		<!-- There were no clickbank products retrieved -->
		There are no products configured in ClickBank.
	<?php } ?>
</div>
	
<div class="mm-dialog-footer-container">
	<div class="mm-dialog-button-container">
		<a onClick="saveClickbankProductMappings()" class="mm-ui-button blue">Save Product Mappings</a>
		<a onClick="jQuery('#clickbank-configure-products-dialog').dialog('close');" class="mm-ui-button">Cancel</a>
	</div>
</div>