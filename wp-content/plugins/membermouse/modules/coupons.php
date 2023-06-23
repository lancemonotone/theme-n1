<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_CouponView();
$view->setupEnhancedDatagrid();

//translations
$mmehdts = $view->getDefaultGridTranslations();

//images
$mmehdgi = $view->getDefaultGridImages("coupon");

//get the initial data from the view, get a unique id for the grid
$dataset = json_encode($view->search());
$gridId = uniqid(); 

global $current_user;
$couponsSupported = MM_PaymentServiceFactory::couponsSupported();
$showHiddenCoupons = (MM_OptionUtils::getOption(MM_CouponView::$SEARCH_OPTION_SHOW_HIDDEN."-".$current_user->ID) == "1") ? true : false;
$showExpiredCoupons = (MM_OptionUtils::getOption(MM_CouponView::$SEARCH_OPTION_SHOW_EXPIRED."-".$current_user->ID) == "1") ? true : false;
$totalHidden = MM_Coupon::getTotalHidden();
$totalHiddenStr = " <span id='mm-total-hidden'>{$totalHidden}</span> ";
?>
<div class="mm-wrap">
	<?php if(MM_Response::isError($couponsSupported)) { ?>
		<div class="error"><p><?php echo $couponsSupported->message; ?></p></div>
	<?php } ?>
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-coupons-dialog', 620, 645)" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> <?php echo _mmt("Create Coupon"); ?></a>
		
		<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_LOGS, MM_MODULE_COUPON_LOG); ?>" class='mm-ui-button'>
        	<i class="fa fa-ticket"></i> <?php echo _mmt("View Coupon Log"); ?>
        </a>
		
		<span style="font-size:11px;">
			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
			<span id='mm-show-hide-controls-container' style='<?php echo ($totalHidden>0) ? "" : "display:none"; ?>'>
			<input type='checkbox' id='mm-show-hidden-coupons' <?php echo ($showHiddenCoupons ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeCouponSearchOptions();" /> <?php echo _mmt("Show").$totalHiddenStr._mmt("hidden coupons"); ?>
			</span>
			<input type='checkbox' id='mm-show-expired-coupons' <?php echo ($showExpiredCoupons ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeCouponSearchOptions();" /> <?php echo _mmt("Show expired coupons"); ?>
		</span>
	</div>
	<div class="clear"></div>
	
	<div id="gridHolder" data-grid-id="<?php echo $gridId; ?>" style="width:98%">
	<!-- Container for datagrid -->
	</div>
	
	<?php if ($dataset != null) { ?>
	<!-- Initial data for datagrid -->
	<script type="application/json" id="<?php echo "coupons-{$gridId}-data"; ?>">
	<?php echo $dataset; ?>
	</script>
	<?php } ?>
</div>
<div id="dialog-confirm"></div>

<script type="text/javascript">
jQuery(document).ready(function() {
	let mmehdgi = <?php echo json_encode($mmehdgi); ?>; //set up images
    let mmehdts = <?php echo json_encode($mmehdts); ?>; //set up translations
    mmjs.couponLogLink = mmjs.manageMembersLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_LOGS, MM_MODULE_COUPON_LOG).'&coupon_code='; ?>";
    mmjs.productsLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_PRODUCTS)."&autoload="; ?>";
    mmjs.renderGrid(mmehdgi,mmehdts);
	mmjs.bindEventListeners();
});
</script>