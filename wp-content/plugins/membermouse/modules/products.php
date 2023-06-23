<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
$view = new MM_ProductView();
$view->setupEnhancedDatagrid();

//this variable will make translations available to the grid js
$mmehdts = $view->getDefaultGridTranslations();
$mmehdts += [
    //TODO: translate all module specific strings and add here
        'Get purchase links' => _mmt("Get purchase links")
    ];

//this variable will make the following named images/html available to the grid js
$mmehdgi = $view->getDefaultGridImages("product");
$mmehdgi += [
    //module specific
    'trial'         => MM_Utils::getIcon('clock-o', 'beige', '1.3em', '0px', 'Has Trial', 'margin-right:5px;'),
    'payment-plan'  => MM_Utils::getIcon('calendar-o', 'beige', '1.3em', '0px', 'Payment Plan', 'margin-right:5px;'),
    'subscription'  => MM_Utils::getIcon('refresh', 'beige', '1.3em', '0px', 'Subscription', 'margin-right:5px;'),
    'shippable'     => MM_Utils::getIcon('truck', 'beige', '1.3em', '0px', 'Requires Shipping', 'margin-right:5px;'),
    'sku'           => MM_Utils::getIcon('barcode', 'beige', '1.3em', '0px', "[SKU PLACEHOLDER]", 'margin-right:5px;'), //TODO: add sku to title
    'membership'    => MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_MEMBERSHIP),
    'bundle'        => MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_BUNDLE),
    'purchase-link' => MM_Utils::getIcon('money', '', '1.3em', '1px', '', 'margin-right:0px;')
];

//get the initial data from the view, get a unique id for the grid
$dataset = json_encode($view->search());
$gridId = uniqid(); 

//TODO: if total changes, dump the cache

global $current_user;
$totalHidden = MM_Product::getTotalHidden();
$totalHiddenStr = " <span id='mm-total-hidden'>{$totalHidden}</span> ";
$showHidden = (MM_OptionUtils::getOption(MM_ProductView::$SEARCH_OPTION_SHOW_HIDDEN."-".$current_user->ID) == "1") ? true : false;
?>

<div class="mm-wrap">
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-products-dialog', 580, 600)" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> <?php echo _mmt("Create Product"); ?></a>
		<span style="font-size:11px;">
			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
			<span id='mm-show-hide-controls-container' style='<?php echo ($totalHidden>0) ? "" : "display:none"; ?>'>
				<input type='checkbox' id='mm-show-hidden-products' <?php echo ($showHidden ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeSearchOptions();" /> <?php echo _mmt("Show").$totalHiddenStr._mmt("hidden products"); ?>
			</span>
		</span>
	</div>
	
	<div class="clear"></div>
	<div id="gridHolder" data-grid-id="<?php echo $gridId; ?>" style="width:98%">
	<!-- Container for datagrid -->
	</div>
	
	<?php if ($dataset != null) { ?>
	<script type="application/json" id="<?php echo "product-{$gridId}-data"; ?>">
	<?php echo $dataset; ?>
	</script>
	<?php } ?>	
</div>
 
<div id="dialog-confirm"></div>

<script type="text/javascript">
jQuery(document).ready(function() {
	let mmehdgi = <?php echo json_encode($mmehdgi); ?>; //set up images
    let mmehdts = <?php echo json_encode($mmehdts); ?>; //set up translations
    mmjs.membershipLevelLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_MEMBERSHIP_LEVELS)."&autoload="; ?>";
    mmjs.bundleLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_BUNDLES)."&autoload=";?>";
	mmjs.renderGrid(mmehdgi,mmehdts);
	mmjs.bindEventListeners();
	<?php if(isset($_REQUEST["autoload"])) 
	      {
	          if($_REQUEST["autoload"] == "new")
	          {
	              echo 'mmjs.create(\'mm-products-dialog\', 580, 600);';
	          }
	          else
	          {
	              echo 'mmjs.edit(\'mm-products-dialog\', \''.$_REQUEST["autoload"].'\', 580, 600);';
	          }
	      }
	 ?>
});
</script>