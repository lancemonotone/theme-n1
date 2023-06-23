<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_MembersView();
$view->setupEnhancedDatagrid();

//translations
$mmehdts = $view->getDefaultGridTranslations();

//images
$mmehdgi = $view->getDefaultGridImages("member");
$mmehdgi += ['active_status'               => MM_Status::getImage(MM_Status::$ACTIVE),
             'canceled_status'             => MM_Status::getImage(MM_Status::$CANCELED),
             'locked_status'               => MM_Status::getImage(MM_Status::$LOCKED),
             'paused_status'               => MM_Status::getImage(MM_Status::$PAUSED),
             'overdue_status'              => MM_Status::getImage(MM_Status::$OVERDUE),
             'error_status'                => MM_Status::getImage(MM_Status::$ERROR),
             'expired_status'              => MM_Status::getImage(MM_Status::$EXPIRED),
             'pending_activation_status'   => MM_Status::getImage(MM_Status::$PENDING_ACTIVATION),
             'pending_cancellation_status' => MM_Status::getImage(MM_Status::$PENDING_CANCELLATION),
             'bundle'                      => MM_Utils::getAccessIcon(MM_OrderItemAccess::$ACCESS_TYPE_BUNDLE),
             'last-login-date'             => MM_Utils::getIcon('calendar-o', 'purple', '1.2em', '2px', _mmt("Last logged in ")."[lldplaceholder]", "margin-right:8px;"),
             'never-logged-in'             => MM_Utils::getIcon('calendar-o', 'purple', '1.2em', '2px', _mmt("Member hasn't logged in yet"), "margin-right:8px;"),
             'login-count'                 => MM_Utils::getIcon('key', 'yellow', '1.2em', '2px', _mmt("Logged in")." [lcplaceholder] "._mmt("times")),
             'pages-accessed'              => MM_Utils::getIcon('file-o', 'turq', '1.2em', '2px', _mmt("Accessed")." [paplaceholder] "._mmt("pages"))
];

//get the initial data from the view, get a unique id for the grid
$initialQuery = null;
if (isset($_GET["membershipId"]) || isset($_GET["bundleId"]))
{
    $queryMeta = (object)["query"=>(object)["conditions"=>[]]];
    if (isset($_GET["membershipId"]))
    {
        $queryMeta->query->conditions[] = (object)["name"=>"mud.membership_level_id", "value"=>htmlspecialchars($_GET["membershipId"], ENT_NOQUOTES, "UTF-8") , "rel"=>"eq"];
    }
    
    if (isset($_GET["bundleId"]))
    {
        $queryMeta->query->bundles = [htmlspecialchars($_GET["bundleId"], ENT_NOQUOTES, "UTF-8")];
    }
    $initialQuery["queryMeta"] = json_encode($queryMeta);
}
$dataset = json_encode($view->search($initialQuery));
$gridId = uniqid(); 

$showSearch = false;

//only show 'export csv' option if current user is an administrator
global $current_user;

$showCsvExportButton = false;
$useLegacyExport = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ENABLE_LEGACY_EXPORT,false); 

if (isset($current_user) && isset($current_user->ID))
{
	$employee = MM_Employee::findByUserId($current_user->ID);
	if($employee->isValid() && ($employee->getRoleId() == MM_Role::$ROLE_ADMINISTRATOR || $employee->doAllowExport()))
	{
		$showCsvExportButton = true;
	}
	
	echo "<input type='hidden' id='mm-admin-id' value='{$current_user->ID}' />";
	
	// determine if this user's preference is to have the advanced search open
	$showSearchOptionName = MM_OptionUtils::$OPTION_KEY_SHOW_MBRS_SEARCH."-".$current_user->ID;
	$showSearchOptionValue = MM_OptionUtils::getOption($showSearchOptionName);
	
	if($showSearchOptionValue == "1")
	{
		$showSearch = true;
	}
}

if (!$useLegacyExport)
{
    wp_enqueue_script("membermouse-dexie", plugins_url(MM_PLUGIN_NAME."/lib/dexie/dexie.min.js"), array(), MemberMouse::getPluginVersion(), true);
    wp_enqueue_script("membermouse-blockUI", plugins_url(MM_PLUGIN_NAME."/resources/js/common/jquery.blockUI.js"), array(), MemberMouse::getPluginVersion(), true);
    wp_enqueue_script("membermouse-batchTransfer", plugins_url(MM_PLUGIN_NAME."/resources/js/admin/mm-batch_transfer.js"), array(), MemberMouse::getPluginVersion(), true);
}
?>
<div class="mm-wrap">
	<?php if(count(MM_MembershipLevel::getMembershipLevelsList()) > 0) { ?>
		<div style="margin-top:20px;" class="mm-button-container">			
			<a id="mm-show-search-btn" onclick="mmjs.showSearch()" class="mm-ui-button blue" <?php echo ($showSearch) ? "style=\"display:none;\"" : ""; ?>><?php echo MM_Utils::getIcon('search-plus'); ?> <?php echo _mmt('Advanced Search'); ?></a>
			<a id="mm-hide-search-btn" onclick="mmjs.hideSearch()" class="mm-ui-button" <?php echo ($showSearch) ? "" : "style=\"display:none;\""; ?>><?php echo MM_Utils::getIcon('search-minus'); ?> <?php echo _mmt('Advanced Search'); ?></a>
			
			<a onclick="mmjs.create('mm-create-member-dialog', 500, 380)" class="mm-ui-button green" style="margin-left:15px;"><?php echo MM_Utils::getIcon('user'); ?> <?php echo _mmt('Create Member'); ?></a>
			
			<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_IMPORT_WIZARD); ?>" class="mm-ui-button" style="margin-left:15px;"><?php echo MM_Utils::getIcon('upload'); ?> <?php echo _mmt('Import Members'); ?></a>
		
			<?php 
				if ($showCsvExportButton) 
				{ 
				    if ($useLegacyExport) 
				    {
			?>
						<a class="mm-ui-button" onclick="mmjs.legacyCsvExport(0);" style="margin-left:15px;"><?php echo MM_Utils::getIcon('download'); ?> <?php echo _mmt('Export Members'); ?></a>
			<?php   }
			        else
			        {
			?> 
						<a class="mm-ui-button" onclick="mmjs.csvExport(0);" style="margin-left:15px;"><?php echo MM_Utils::getIcon('download'); ?> <?php echo _mmt('Export Members'); ?></a>
			<?php          
			        }
			    } ?>
		</div>
	<?php } ?>
	
	<div style="width: 98%; margin-top: 10px; margin-bottom: 0px;" class="mm-divider"></div> 
	
	<div id="mm-advanced-search" <?php echo ($showSearch) ? "" : "style=\"display:none;\""; ?>>
		<div id="mm-advanced-search-container" style="width:98%">
		<?php echo $view->generateSearchForm($_POST); ?>
		</div>
		<div style="width: 98%; margin-top: 0px; margin-bottom: 10px;" class="mm-divider"></div> 
	</div>
	
	<div id='mm_members_csv'></div>
	
	<div id="gridHolder" data-grid-id="<?php echo $gridId; ?>" style="width:98%">
	<!-- Container for datagrid -->
	</div>
	
	<?php if ($dataset != null) { ?>
	<script type="application/json" id="<?php echo "members-{$gridId}-data"; ?>">
	<?php echo $dataset; ?>
	</script>
	<?php } ?>
</div>


<?php 
if (!$useLegacyExport)
{
?>  
<div class="mbtDialog" id="export_status_dialog" style='display:none'>
    <h3><i class="fa fa-download" style="top:1px;"></i>  Export Members</h3>
    <p>Your members are being exported. Please wait ... </p>
    <div>
    	<label for="exportProgress">Export progress:</label>
		<progress id="exportProgress" value="32" max="100">0</progress>
		<div id="exportProgressText"></div>
	</div> 	
    <p><input type='button' name='cancel' value="Cancel" onclick="mmjs.cancelExport();" class="mm-ui-button" /></p>
</div>
<?php } ?>


<script type="text/javascript">
jQuery(document).ready(function() {
    let mmehdgi = <?php echo json_encode($mmehdgi); ?>; //set up images
    let mmehdts = <?php echo json_encode($mmehdts); ?>; //set up translations
    mmjs.memberDetailsLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_GENERAL)."&user_id="; ?>";
    mmjs.bundlesLink = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_BUNDLES)."&autoload="; ?>";
    mmjs.renderGrid(mmehdgi,mmehdts);
    mmjs.bindEventListeners();
});
</script>