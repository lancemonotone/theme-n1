<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_MembershipLevelsView();
$view->setupEnhancedDatagrid();

//translations
$mmehdts = $view->getDefaultGridTranslations();
$mmehdts += [
    //TODO: translate all grid strings, relocate to shared location, make this one module specific
    'Get purchase links' => _mmt("Get purchase links"),
    'Name / Subscribers' => _mmt("Name") . "/" . _mmt("Subscribers")
];

//images
$defaultDescription = _mmt("Any free membership level can be marked as the default membership level. ".
    "The default membership level is used when a customer&rsquo;s first purchase is for a bundle. ".
    "In this scenario, a new account will be created for the customer with the default membership level and the ".
    "bundle will be applied to their account.");
$mmehdgi = $view->getDefaultGridImages("membership level");
$mmehdgi += [
    //module specific
    'default-flag'     => MM_Utils::getDefaultFlag(_mmt("Default Membership Level")."\n\n{$defaultDescription}", "", true, 'margin-right:5px;'),
    'set-default-flag' => MM_Utils::getDefaultFlag(_mmt("Set as Default Membership Level")."\n\n{$defaultDescription}", "", false, 'margin-right:5px;'),
    'type-free'        => MM_Utils::getIcon('dollar', 'red', '1.3em', '2px', _mmt('Free Membership Level')),
    'type-paid'        => MM_Utils::getIcon('dollar', 'green', '1.3em', '2px', _mmt('Paid Membership Level')),
    'subscribers'      => MM_Utils::getIcon('users', 'blue', '1.2em', '1px', '', 'margin-right:2px; margin-left:25px;'),
    'no-subscribers'   => MM_Utils::getIcon('users', 'grey', '1.2em', '1px', '', 'margin-right:2px; margin-left:25px;'),
    'membership'       => MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_MEMBERSHIP),
    'bundle'           => MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_BUNDLE),
    'purchase-link'    => MM_Utils::getIcon('money', '', '1.3em', '1px', '', 'margin-right:0px;'),
    'shopping-cart'    => MM_Utils::getIcon('shopping-cart', 'blue', '1.3em', '1px', _mmt('Products'), 'margin-right:5px;')
];

//get the initial data from the view, get a unique id for the grid
$dataset = json_encode($view->search());
$gridId = uniqid(); 

global $current_user;
$totalHidden = MM_MembershipLevel::getTotalHidden();
$totalHiddenStr = " <span id='mm-total-hidden'>{$totalHidden}</span> ";
$showHidden = (MM_OptionUtils::getOption(MM_MembershipLevelsView::$SEARCH_OPTION_SHOW_HIDDEN."-".$current_user->ID) == "1") ? true : false;
?>
<div class="mm-wrap">	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-member-types-dialog')" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> <?php echo _mmt("Create Membership Level"); ?></a>
		<span style="font-size:11px;">
			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
			<span id='mm-show-hide-controls-container' style='<?php echo ($totalHidden>0) ? "" : "display:none"; ?>'>
				<input type='checkbox' id='mm-show-hidden-membership-levels' <?php echo ($showHidden ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeSearchOptions();" /> <?php echo _mmt("Show").$totalHiddenStr._mmt("hidden membership levels"); ?>
			</span>
		</span> 
	</div>
	
	<div class="clear"></div>
	<div id="gridHolder" data-grid-id="<?php echo $gridId; ?>" style="width:98%">
	</div>
	
	<?php if ($dataset != null) { ?>
	<script type="application/json" id="<?php echo "membership_levels-{$gridId}-data"; ?>">
    <?php echo $dataset; ?>
	</script>
	<?php } ?>
</div>
 
<div id="dialog-confirm"></div>

<script type="text/javascript">
jQuery(document).ready(function() {
	let mmehdgi = <?php echo json_encode($mmehdgi); ?>; //set up images
    let mmehdts = <?php echo json_encode($mmehdts); ?>; //set up translations
    mmjs.manageMembersLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_BROWSE_MEMBERS).'&membershipId='; ?>";
    mmjs.productsLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_PRODUCTS)."&autoload="; ?>";
    mmjs.bundlesLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_BUNDLES)."&autoload="; ?>";
    mmjs.renderGrid(mmehdgi,mmehdts);
	mmjs.bindEventListeners();
    <?php if(isset($_REQUEST["autoload"])) 
          { 
            if($_REQUEST["autoload"] == "new")
        	{
        		 echo 'mmjs.create(\'mm-member-types-dialog\')';
        	}
        	else
        	{
        		echo 'mmjs.edit(\'mm-member-types-dialog\', \''.$_REQUEST["autoload"].'\');';
        	}
          }
    ?>
});
</script>