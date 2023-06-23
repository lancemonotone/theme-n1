<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_BundlesView();
$view->setupEnhancedDatagrid();

//this variable will make translations available to the grid js
$mmehdts = $view->getDefaultGridTranslations();
$mmehdts += [
    //TODO: translate all module specific strings and add here
    'Get purchase links' => _mmt("Get purchase links")
];

//this variable will make the following named images/html available to the grid js
$mmehdgi = $view->getDefaultGridImages("bundle");
$mmehdgi += [
    //module specific
    'bundle'           => MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_BUNDLE),
    'purchase-link'    => MM_Utils::getIcon('money', '', '1.3em', '1px', '', 'margin-right:0px;'),
    'shopping-cart'    => MM_Utils::getIcon('shopping-cart', 'blue', '1.3em', '1px', _mmt('Products'), 'margin-right:5px;'),
    'subscribers'      => MM_Utils::getIcon('users', 'blue', '1.2em', '1px', '', 'margin-right:2px;'),
    'no-subscribers'   => MM_Utils::getIcon('users', 'grey', '1.2em', '1px', '', 'margin-right:2px;'),
    'type-free'        => MM_Utils::getIcon('dollar', 'red', '1.3em', '2px', _mmt('Free Bundle')),
    'type-paid'        => MM_Utils::getIcon('dollar', 'green', '1.3em', '2px', _mmt('Paid Bundle'))
];

//get the initial data from the view, get a unique id for the grid
$dataset = json_encode($view->search());
$gridId = uniqid(); 

//TODO: if total changes, dump the cache
global $current_user;
$totalHidden = MM_Bundle::getTotalHidden();
$totalHiddenStr = " <span id='mm-total-hidden'>{$totalHidden}</span> ";
$showHidden = (MM_OptionUtils::getOption(MM_BundlesView::$SEARCH_OPTION_SHOW_HIDDEN."-".$current_user->ID) == "1") ? true : false; 
?>

<div class="mm-wrap">
	
	<?php if(MM_MemberMouseService::hasPermission(MM_MemberMouseService::$FEATURE_BUNDLES)) { ?>
		<div class="mm-button-container">
			<a onclick="mmjs.create('mm-bundles-dialog')" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> <?php echo _mmt("Create Bundle");?></a>
		
			<span style="font-size:11px;">
    			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
				<span id='mm-show-hide-controls-container' style='<?php echo ($totalHidden>0) ? "" : "display:none"; ?>'>
					<input type='checkbox' id='mm-show-hidden-bundles' <?php echo ($showHidden ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeSearchOptions();" /> <?php echo _mmt("Show").$totalHiddenStr._mmt("hidden bundles"); ?>
				</span>
    		</span>
		</div>
		
    	<div class="clear"></div>
    	<div id="gridHolder" data-grid-id="<?php echo $gridId; ?>" style="width:98%">
    	<!-- Container for datagrid -->
    	</div>
	
    	<?php if ($dataset != null) { ?>
    	<script type="application/json" id="<?php echo "bundle-{$gridId}-data"; ?>">
	       <?php echo $dataset; ?>
	    </script>
	<?php } ?>	
	<?php } else { ?>
		<?php echo MM_Utils::getIcon('lock', 'yellow', '1.3em', '2px'); ?> 
		<?php echo _mmt("This feature is not available on your current plan");?>. <?php echo sprintf(_mmt("To get access, %s upgrade your plan now %s"), '<a href="'.MM_MemberMouseService::getUpgradeUrl(MM_MemberMouseService::$FEATURE_BUNDLES).'" target="_blank">','</a>'); ?>
	<?php } ?>
</div> 
<div id="dialog-confirm"></div>


<script type='text/javascript'>
jQuery(document).ready(function() {
	let mmehdgi = <?php echo json_encode($mmehdgi); ?>; //set up images
    let mmehdts = <?php echo json_encode($mmehdts); ?>; //set up translations
    mmjs.manageMembersLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_BROWSE_MEMBERS).'&bundleId='; ?>";
    mmjs.productsLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_PRODUCTS)."&autoload="; ?>";
    mmjs.renderGrid(mmehdgi,mmehdts);
	mmjs.bindEventListeners();
	<?php
	if(isset($_REQUEST["autoload"])) 
	{
    	if($_REQUEST["autoload"] == "new")
    	{
    		 echo 'mmjs.create(\'mm-bundles-dialog\');';
    	}
    	else
    	{
    		echo 'mmjs.edit(\'mm-bundles-dialog\', \''.$_REQUEST["autoload"].'\');';
    	}
	}
	?>
});
</script>