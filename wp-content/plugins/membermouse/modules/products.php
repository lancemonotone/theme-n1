<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_ProductView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "desc", 10);
$data = $view->getViewData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "product";

$rows = array();

foreach($data as $key=>$item)
{	
	$product = new MM_Product($item->id);
	
	// Actions
	$editActionUrl = 'onclick="mmjs.edit(\'mm-products-dialog\', \''.$product->getId().'\', 580, 600)"';
   	$deleteActionUrl = 'onclick="mmjs.remove(\''.$product->getId().'\')"';
	$hideActionUrl = 'onclick="mmjs.hideProduct(\''.$product->getId().'\',\''.$product->getStatus().'\')"';
	$showActionUrl = 'onclick="mmjs.showProduct(\''.$product->getId().'\',\''.$product->getStatus().'\')"';
   	$actions = MM_Utils::getEditIcon("Edit Product", '', $editActionUrl); 
	
	$purchaseLinks = '<a title="Get purchase links" onclick="mmjs.showPurchaseLinks('.$product->getId().',\''.htmlentities(addslashes($product->getName()), ENT_QUOTES, "UTF-8").'\')" class="mm-ui-button" style="margin:0px;">'.MM_Utils::getIcon('money', '', '1.3em', '1px', '', 'margin-right:0px;').'</a>';
	
	
	// Associated Access
	$accessGranted = "";
	
	$membership = $product->getAssociatedMembership();
	
	if($membership->isValid())
	{
		$accessGranted .= MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_MEMBERSHIP);
		$accessGranted .= " <a href='".MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_MEMBERSHIP_LEVELS)."&autoload=".$membership->getId()."'>".$membership->getName()."</a>";
	}
	
	if(empty($accessGranted))
	{
		$bundle = $product->getAssociatedBundle();
	
		if($bundle->isValid())
		{
			$accessGranted .= MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_BUNDLE);
			$accessGranted .= " <a href='".MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_BUNDLES)."&autoload=".$bundle->getId()."'>".$bundle->getName()."</a>";
		}
	}
	
	if(empty($accessGranted))
	{
		$accessGranted = MM_NO_DATA;
	}
	
	
	// Attributes
	$attributes = "";
	
	if($product->hasTrial())
	{
		$attributes .= MM_Utils::getIcon('clock-o', 'beige', '1.3em', '0px', 'Has Trial', 'margin-right:5px;');
	}
	else
	{
		$attributes .= "<img title='No Trial' style='margin-right:5px;' src='".MM_Utils::getImageUrl("clear")."' />";
	}
	
	if($product->isRecurring())
	{
		if($product->doLimitPayments())
		{
			$attributes .= MM_Utils::getIcon('calendar-o', 'beige', '1.3em', '0px', 'Payment Plan', 'margin-right:5px;');
		}
		else 
		{
			$attributes .= MM_Utils::getIcon('refresh', 'beige', '1.3em', '0px', 'Subscription', 'margin-right:5px;');
		}
	}
	else
	{
		$attributes .= "<img title='No Recurring' style='margin-right:5px;' src='".MM_Utils::getImageUrl("clear")."' />";
	}
	
	if($product->isShippable())
	{
		$attributes .= MM_Utils::getIcon('truck', 'beige', '1.3em', '0px', 'Requires Shipping', 'margin-right:5px;');
	}
	else
	{
		$attributes .= "<img title='No Shipping Required' style='margin-right:5px;' src='".MM_Utils::getImageUrl("clear")."' />";
	}
	
	if($product->getSku() != "")
	{
		$attributes .= MM_Utils::getIcon('barcode', 'beige', '1.3em', '0px', "SKU [".$product->getSku()."]", 'margin-right:5px;');
	}
	else
	{
		$attributes .= "<img title='No SKU' style='margin-right:5px;' src='".MM_Utils::getImageUrl("clear")."' />";
	}
	 
	$showHideActions = "";
	if(!MM_Product::isBeingUsed($product->getId()) && !MM_Product::hasBeenPurchased($product->getId()))
    {
		$actions .= MM_Utils::getDeleteIcon("Delete Product", 'margin-left:5px;', $deleteActionUrl);
    }
    else 
    {
    	$actions .= MM_Utils::getDeleteIcon("This product is currently being used and cannot be deleted.", 'margin-left:5px;', '', true);
	}	
	if(!$product->isHidden())
	{
		$showHideMsg = "Hide this product from the list view.";
		$showHideActions .= MM_Utils::getHideIcon($showHideMsg, 'margin-left:5px;', $hideActionUrl);
	}
	else if($product->isHidden())
	{ 
		$showHideMsg = "Show this product in the list view.";
		$showHideActions .= MM_Utils::getHideIcon($showHideMsg, 'margin-left:5px;', $showActionUrl, true);
	} 
	
    $rows[] = array
    (
        array('content' => $product->getId()),
    	array('content' => "<span title='ID [".$product->getId()."]'>".$product->getName()."</span>"),
    	array('content' => $product->getBillingDescription()),
    	array('content' => $attributes),
    	array('content' => $accessGranted),
    	array('content' => $purchaseLinks),
    	array('content' => MM_Utils::getStatusImage($product->getStatus())),
    	array('content' => $actions.$showHideActions) 
    );
}

$headers = array
(
        'id'            => array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">'._mmt("ID").'</a>'),
		'name'			=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">'._mmt("Name").'</a>'),
		'billing'		=> array('content' => _mmt('Billing Description')),
		'attributes'	=> array('content' => _mmt('Attributes')),
		'access'		=> array('content' => _mmt('Associated Access')),
		'links'			=> array('content' => _mmt('Purchase Links')),
		'status'		=> array('content' => '<a onclick="mmjs.sort(\'status\');" href="#">'._mmt("Status").'</a>'),
		'actions'		=> array('content' => _mmt('Actions'))
);

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") 
{
	$dgHtml = "<p><i>No products found.</i></p>";
}
global $current_user;

$totalHidden = MM_Product::getTotalHidden();
$totalHiddenStr = ($totalHidden>0)?" {$totalHidden} ":" ";
$showHidden = (MM_OptionUtils::getOption(MM_ProductView::$SEARCH_OPTION_SHOW_HIDDEN."-".$current_user->ID) == "1") ? true : false;
?>
<div class="mm-wrap">
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-products-dialog', 580, 600)" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> <?php echo _mmt("Create Product"); ?></a>
		<span style="font-size:11px;">
			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
			<?php if($totalHidden>0){ ?>
			<input type='checkbox' id='mm-show-hidden-products' <?php echo ($showHidden ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeSearchOptions();" /> <?php echo _mmt("Show").$totalHiddenStr._mmt("hidden products"); ?>
			<?php } ?>
		</span>
	</div>
	
	<div class="clear"></div>
	<div style="width:98%">
	<?php echo $dgHtml; ?>
	</div>
</div>
 
<div id="dialog-confirm"></div>

<?php if(isset($_REQUEST["autoload"])) { ?>
<script type='text/javascript'>
jQuery(document).ready(function() {
	<?php
	if($_REQUEST["autoload"] == "new")
	{
		 echo 'mmjs.create(\'mm-products-dialog\', 580, 600);';
	}
	else
	{
		echo 'mmjs.edit(\'mm-products-dialog\', \''.$_REQUEST["autoload"].'\', 580, 600);';
	}
	?>
});
</script>
<?php } ?>