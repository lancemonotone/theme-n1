<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_MembershipLevelsView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "desc", 10);
$data = $view->getViewData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "membership level";

$rows = array();

foreach($data as $key=>$item)
{	
    $membership = new MM_MembershipLevel($item->id, false);
	
	// Default Flag
	$defaultDescription = "Any free membership level can be marked as the default membership level. The default membership level is used when a customer&rsquo;s first purchase is for a bundle. In this scenario, a new account will be created for the customer with the default membership level and the bundle will be applied to their account.";
    
	if($item->is_default == '1') 
	{
		$defaultFlag = MM_Utils::getDefaultFlag("Default Membership Level\n\n{$defaultDescription}", "", true, 'margin-right:5px;');
	}
	else if($item->status == '1' && $item->is_free == '1' && $item->is_hidden!="1" )
	{
		$defaultFlag = MM_Utils::getDefaultFlag("Set as Default Membership Level\n\n{$defaultDescription}", "onclick='mmjs.setDefault(\"".$item->id."\")'", false, 'margin-right:5px;');
	} 
	else	
	{
		$defaultFlag = "<a style='margin-right:5px;'><img src='".MM_Utils::getImageUrl("clear")."' /></a>";
	}
    	
   	// Product Assocations
   	if($item->is_free != "1")
   	{
   		$products = array();
   		$productIds = array();
   		
   		if(!empty($item->products))
   		{
	   		foreach($item->products as $product)
	   		{
	   			$products[] = "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_PRODUCTS)."&autoload=".$product->id."'>".$product->name."</a>";
	   			$productIds[] = $product->id;
	   		}
   		}
   		
   		$productAssociations = MM_Utils::getIcon('shopping-cart', 'blue', '1.3em', '1px', 'Products', 'margin-right:5px;').join(', ' , $products);
   		$membershipLevel = MM_Utils::getIcon('dollar', 'green', '1.3em', '2px', _mmt('Paid Membership Level'));
   		$purchaseLinks = '<a title="'._mmt('Get purchase links').'" onclick="mmjs.showPurchaseLinks('.$item->id.',\''.htmlentities(addslashes($item->name), ENT_QUOTES, "UTF-8").'\', \''.join(',' , $productIds).'\')" class="mm-ui-button" style="margin:0px;">'.MM_Utils::getIcon('money', '', '1.3em', '1px', '', 'margin-right:0px;').'</a>';
   	}
   	else 
   	{
    	$membershipLevel = MM_Utils::getIcon('dollar', 'red', '1.3em', '2px', _mmt('Free Membership Level'));
    	$productAssociations = MM_NO_DATA;
    	$purchaseLinks = '<a title="'._mmt('Get purchase links').'" onclick="mmjs.showPurchaseLinks('.$item->id.',\''.htmlentities(addslashes($item->name), ENT_QUOTES, "UTF-8").'\', \'\')" class="mm-ui-button" style="margin:0px;">'.MM_Utils::getIcon('money', '', '1.3em', '1px', '', 'margin-right:0px;').'</a>';
   	}
	
    // Name / Subscribers		    
    if(!empty($item->member_count))
    {
   		$item->name .= '<p>'.MM_Utils::getIcon('users', 'blue', '1.2em', '1px', '', 'margin-right:2px; margin-left:25px;').' <a href="'.MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_BROWSE_MEMBERS).'&membershipId='.$item->id.'">'.$item->member_count.' Members</a></p>';
   	}
   	else
   	{
   		$item->name .= '<p>'.MM_Utils::getIcon('users', 'grey', '1.2em', '1px', '', 'margin-right:2px; margin-left:25px;').' <i>'._mmt('No Subscribers').'</i></p>';
   	}
    
    // Bundles   	
    $bundles = array();
    
    if(!empty($item->bundles)) 
    {
	   	foreach($item->bundles as $bundle) 
	   	{
	   		$bundles[] = "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_BUNDLES)."&autoload=".$bundle->id."'>".$bundle->name."</a>";
	   	}
    }
	
    
    if(!empty($bundles))
    {
    	$item->bundles = MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_BUNDLE, '', 'margin-right:5px;');
    	$item->bundles .= join(', ' , $bundles);
    }
    else
    {
    	$item->bundles = MM_NO_DATA;
    }
    
    // Actions
	$hideActionUrl = 'onclick="mmjs.hideMembershipLevel(\''.$item->id.'\',\''.$item->status.'\')"';
	$showActionUrl = 'onclick="mmjs.showMembershipLevel(\''.$item->id.'\',\''.$item->status.'\')"';
    $editActionUrl = 'onclick="mmjs.edit(\'mm-member-types-dialog\', \''.$item->id.'\')"';
    $deleteActionUrl = 'onclick="mmjs.remove(\''.$item->id.'\')"';
	$actions = MM_Utils::getEditIcon(_mmt("Edit Membership Level"), '', $editActionUrl); 
     
	$showHideActions = "";
	if(!$membership->hasAssociations() && intval($item->member_count) <= 0 && $item->is_hidden!="1" )
    {
		$actions .= MM_Utils::getDeleteIcon("Delete Product", 'margin-left:5px;', $deleteActionUrl);
	} 
	else
	{
		$actions .= MM_Utils::getDeleteIcon("This membership level is currently being used and cannot be deleted.", 'margin-left:5px;', '', true); 
	}
	
	if($item->is_default!="1")
	{
		if($item->is_hidden!="1" )
		{
			$showHideMsg = "Hide this membership level from the list view.";
			$showHideActions .= MM_Utils::getHideIcon($showHideMsg, 'margin-left:5px;', $hideActionUrl);
		}
		else  
		{ 
			$showHideMsg = "Show this membership level in the list view.";
			$showHideActions .= MM_Utils::getHideIcon($showHideMsg, 'margin-left:5px;', $showActionUrl, true);
		}
	} 
	
    	
    $rows[] = array
    (
        array('content' => $item->id),
    	array('content' => $defaultFlag." <span title='ID [".$item->id."]'>".$item->name."</span>"),
    	array('content' => $membershipLevel),
    	array('content' => $productAssociations),
    	array('content' => $item->bundles),
    	array('content' => $purchaseLinks),
    	array('content' => MM_Utils::getStatusImage($item->status)),
    	array('content' => $actions.$showHideActions)
    );
}

$headers = array
(	    
    'id'            => array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">'._mmt("ID").'</a>'),
   	'name'			=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">'._mmt('Name / Subscribers').'</a>'),
	'is_free'		=> array('content' => '<a onclick="mmjs.sort(\'is_free\');" href="#">'._mmt('Type').'</a>'),
	'products'		=> array('content' => 'Products', 'attr'=>'style="width:400px;"'),
   	'bundles'		=> array('content' => 'Bundles', 'attr'=>'style="width:200px;"'),
   	'purchaselinks'	=> array('content' => _mmt('Purchase Links')),
   	'status'		=> array('content' => '<a onclick="mmjs.sort(\'status\');" href="#">'._mmt('Status').'</a>'),
	'actions'		=> array('content' => _mmt('Actions'))
);

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No membership levels.</i></p>";
}
global $current_user;
$totalHidden = MM_MembershipLevel::getTotalHidden();
$totalHiddenStr = ($totalHidden>0)?" {$totalHidden} ":" "; 
$showHidden = (MM_OptionUtils::getOption(MM_MembershipLevelsView::$SEARCH_OPTION_SHOW_HIDDEN."-".$current_user->ID) == "1") ? true : false;
?>
<div class="mm-wrap">
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-member-types-dialog')" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> <?php echo _mmt("Create Membership Level"); ?></a>
		<span style="font-size:11px;">
			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
			<?php if($totalHidden>0){ ?>
			<input type='checkbox' id='mm-show-hidden-membership-levels' <?php echo ($showHidden ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeSearchOptions();" /> <?php echo _mmt("Show").$totalHiddenStr._mmt("hidden membership levels"); ?>
			<?php } ?>
		</span> 
	</div>
	
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
		 echo 'mmjs.create(\'mm-member-types-dialog\')';
	}
	else
	{
		echo 'mmjs.edit(\'mm-member-types-dialog\', \''.$_REQUEST["autoload"].'\');';
	}
	?>
});
</script>
<?php } ?>