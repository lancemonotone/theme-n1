<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */  

$view = new MM_StickyioProductsView();

$s = new stdClass();
$s->v2 = 1;

$dataGrid = new MM_DataGrid($_REQUEST, "stickyio_campaign_name", "asc", 10, $s);
$data = $view->getViewData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "product mapping";

$rows = array();

foreach($data as $key => $item)
{
	$mmProductName = MM_NO_DATA;
	$mmProductDescription = MM_NO_DATA;
   	$product = new MM_Product($item->membermouse_product_id);
   	
   	if($product->isValid())
   	{
   		$mmProductName = $product->getName();
   		$mmProductDescription = $product->getBillingDescription()." ";
   		
   		if($product->hasTrial())
   		{
   			$mmProductDescription .= MM_Utils::getIcon('clock-o', 'beige', '1.3em', '2px', 'Has Trial', 'margin-right:5px;');
   		}
   		
   		if($product->isRecurring())
   		{
   			if($product->doLimitPayments())
   			{
   				$mmProductDescription .= MM_Utils::getIcon('calendar-o', 'beige', '1.3em', '2px', 'Payment Plan', 'margin-right:5px;');
   			}
   			else
   			{
   				$mmProductDescription .= MM_Utils::getIcon('refresh', 'beige', '1.3em', '2px', 'Subscription', 'margin-right:5px;');
   			}
   		}
   		
   		if($product->isShippable())
   		{
   			$mmProductDescription .= MM_Utils::getIcon('truck', 'beige', '1.3em', '2px', 'Requires Shipping', 'margin-right:5px;');
   		}
   		
   		if($product->getSku() != "")
   		{
   			$mmProductDescription .= MM_Utils::getIcon('barcode', 'beige', '1.3em', '2px', "SKU [".$product->getSku()."]", 'margin-right:5px;');
   		}
   	}

    // Actions 
   	$editActionUrl = 'onclick="mmjs.edit(\'mm-stickyio-products-dialog\', {id:\''.$item->id.'\', v2:\'1\'}, 550, 335)"';
   	$deleteActionUrl = 'onclick="mmjs.remove(\''.$item->id.'\')"';
   	$actions = MM_Utils::getEditIcon("Edit Product Mapping", '', $editActionUrl);
   	$actions .= MM_Utils::getDeleteIcon("Delete Product Mapping", 'margin-left:5px;', $deleteActionUrl);
   	
   	$llOffer = $item->stickyio_offer_name;
   	$llBillingModel = $item->stickyio_billing_model_name;
   	
   	// Lime Light Product
   	$llProduct = "{$item->stickyio_product_name} [{$item->stickyio_product_id}]";
   	$llProduct .= '<a href="javascript:mmjs.getStickyioProductDescription(\''.$item->stickyio_product_id.'\');" style="margin-left: 5px; cursor:pointer;" title="View Lime Light Product Info">'.MM_Utils::getIcon("info-circle", "blue", "1.3em", "2px;").'</a>';
   	
   	$llProductNameAndID = "{$item->stickyio_campaign_name} [{$item->stickyio_campaign_id}]";
   	if(intval($item->stickyio_campaign_id)<=0)
   	{
   		if($item->stickyio_campaign_id==0){
   			$llProductNameAndID = "<i>ALL CAMPAIGNS</i>";
   		}
   		else{
   			$llProductNameAndID = "{$item->stickyio_campaign_name}";
   		}
   	}
   	
	$rows[] = array
    (
        array( 'content' => $llProductNameAndID),
        array( 'content' => $llOffer),
        array( 'content' => $llBillingModel),
    	array( 'content' => $llProduct),
    	array( 'content' => $mmProductName),
    	array( 'content' => $mmProductDescription),
    	array( 'content' => $actions),
    );
}

$headers = array
(
    'stickyio_campaign_name'	=> array('content' => '<a onclick="mmjs.sort(\'stickyio_campaign_name\');" href="#">Campaign</a>'),
    'stickyio_offer' 	=> array('content' => 'Offer'),
    'stickyio_billing_model' 	=> array('content' => 'Billing Model'),
	'stickyio_product_name' 	=> array('content' => '<a onclick="mmjs.sort(\'stickyio_product_name\');" href="#">Product</a>'),
	'membermouse_product_id'	=> array('content' => 'MemberMouse Product'),
	'membermouse_product_desc'	=> array('content' => 'Product Description'),
	'actions'					=> array('content' => 'Actions', 'attr'=>'style="width:50px;"')
);

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") 
{
	$dgHtml = "<p><em>No product mappings.</em></p>";
}
?>
<div class="mm-wrap">
	<?php 
		if(MM_MemberMouseService::hasPermission(MM_MemberMouseService::$PYMT_SERVICE_STICKYIO)) 
		{
			$products = MM_Product::getAll();
			
			if(count($products) > 0)
			{
				$unmappedProducts = MM_StickyioProduct::getUnmappedProducts();
				
				if(count($unmappedProducts) > 0)
				{
	?>
				<div class="mm-button-container">
					<a onclick="mmjs.create('mm-stickyio-products-dialog', 550, 555)" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> Create Product Mapping</a>
				</div>
			
				<div class="clear"></div>
				<?php } ?> 
				<div style="width:95%;">
					<?php echo $dgHtml; ?>
				</div>
	<?php 
			}
			else 
			{
				echo "<p><em>No MemberMouse products created.</em></p>";
			}
		} 
		else 
		{ 
	?>
		<?php echo MM_Utils::getIcon('lock', 'yellow', '1.3em', '2px'); ?>
		This feature is not available on your current plan. To get access, <a href="<?php echo MM_MemberMouseService::getUpgradeUrl(MM_MemberMouseService::$PYMT_SERVICE_LIMELIGHT); ?>" target="_blank">upgrade your plan now</a>.
	<?php } ?>
</div>