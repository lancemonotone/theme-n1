<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_ManageTransactionsView();
$view->setupEnhancedDatagrid();

//translations
$mmehdts = $view->getDefaultGridTranslations();

//images
$mmehdgi = $view->getDefaultGridImages("transactions");
$mmehdgi += [ 'type-test'      => MM_Utils::getIcon('flask', 'grey', '1.3em', '2px', _mmt("Test Transaction"), "padding-left:4px;"),
              'type-payment'   => MM_Utils::getIcon('money', 'green', '1.4em', '2px', _mmt("Initial Payment or One-Time Payment")),
              'type-recurring' => MM_Utils::getIcon('refresh', 'green', '1.4em', '2px',  _mmt("Recurring Payment", "padding-left:2px;")),
              'type-refund'    => MM_Utils::getIcon('money', 'red', '1.4em', '2px',  _mmt("Refund"))
            ];

//set up a transaction type mapping to pass to the javascript. This allows the javascript to pickup any future changed on the serverside mappings
$transactionTypeMap = (object)['payment' => MM_TransactionLog::$TRANSACTION_TYPE_PAYMENT,
                               'recurring_payment' => MM_TransactionLog::$TRANSACTION_TYPE_RECURRING_PAYMENT,
                               'charge'            => MM_TransactionLog::$TRANSACTION_TYPE_CHARGE,
                               'recurring_charge'  => MM_TransactionLog::$TRANSACTION_TYPE_RECURRING_CHARGE,
                               'refund'            => MM_TransactionLog::$TRANSACTION_TYPE_REFUND,
                               'chargeback'        => MM_TransactionLog::$TRANSACTION_TYPE_CHARGEBACK,
];


//get the initial data from the view, get a unique id for the grid
$dataset = json_encode($view->search());
$gridId = uniqid();

$showSearch = false;

//only show 'export csv' option if current user is an administrator
global $current_user;

$showCsvExportButton = false;
if (isset($current_user) && isset($current_user->ID))
{
	$employee = MM_Employee::findByUserId($current_user->ID);
	if($employee->isValid() && ($employee->getRoleId() == MM_Role::$ROLE_ADMINISTRATOR || $employee->doAllowExport()))
	{
		$showCsvExportButton = true;
		$exportDataUrl = MM_MODULES_URL."/export_data.php";
		echo "<script type='text/javascript'>export_transactions_url = '{$exportDataUrl}';</script>\n";
	}
	
	echo "<input type='hidden' id='mm-admin-id' value='{$current_user->ID}' />";
	
	// determine if this user's preference is to have the advanced search open
	$showSearchOptionName = MM_OptionUtils::$OPTION_KEY_SHOW_TRANSACTIONS_SEARCH."-".$current_user->ID;
	$showSearchOptionValue = MM_OptionUtils::getOption($showSearchOptionName);
	
	if($showSearchOptionValue == "1")
	{
		$showSearch = true;
	}
}
?>
<div class="mm-wrap">
	<div style="margin-top:20px;" class="mm-button-container">			
		<a id="mm-show-search-btn" onclick="mmjs.showSearch()" class="mm-ui-button blue" <?php echo ($showSearch) ? "style=\"display:none;\"" : ""; ?>><?php echo MM_Utils::getIcon('search-plus'); ?> <?php echo _mmt("Advanced Search");?></a>
		<a id="mm-hide-search-btn" onclick="mmjs.hideSearch()" class="mm-ui-button" <?php echo ($showSearch) ? "" : "style=\"display:none;\""; ?>><?php echo MM_Utils::getIcon('search-minus'); ?> <?php echo _mmt("Advanced Search");?></a>
		
		<?php 
			if($showCsvExportButton) { 
		?>
		<a class="mm-ui-button" onclick="mmjs.csvExport(0);" style="margin-left:15px;"><?php echo MM_Utils::getIcon('download'); ?> Export</a>
		<?php } ?>
	</div>
	
	<div style="width: 98%; margin-top: 10px; margin-bottom: 0px;" class="mm-divider"></div> 
	
	<div id="mm-advanced-search" <?php echo ($showSearch) ? "" : "style=\"display:none;\""; ?>>
		<div id="mm-advanced-search-container" style="width:98%">
		<?php echo $view->generateSearchForm($_POST); ?>
		</div>
		<div style="width: 98%; margin-top: 0px; margin-bottom: 10px;" class="mm-divider"></div> 
	</div>
	
	<div id='mm_manage_transactions_csv'></div>
	<div id="gridHolder" data-grid-id="<?php echo $gridId; ?>" style="width:98%">
	<!-- Container for datagrid -->
	</div>
	
	<?php if ($dataset != null) { ?>
	<script type="application/json" id="<?php echo "transactions-{$gridId}-data"; ?>">
	<?php echo $dataset; ?>
	</script>
	<?php } ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	let mmehdgi = <?php echo json_encode($mmehdgi); ?>; //set up images
    let mmehdts = <?php echo json_encode($mmehdts); ?>; //set up translations
    mmjs.orderLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_TRANSACTION_HISTORY)."&user_id="; ?>";
    mmjs.memberLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_GENERAL)."&user_id="; ?>";
    let ttm = <?php echo json_encode ($transactionTypeMap); ?>; //set up transaction type mapping
    mmjs.renderGrid(mmehdgi,mmehdts,ttm);
});
</script>