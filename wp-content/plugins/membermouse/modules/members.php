<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_MembersView();

$showSearch = false;

//only show 'export csv' option if current user is an administrator
global $current_user;

$showCsvExportButton = false;
//$useLegacyExport = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ENABLE_LEGACY_EXPORT,false);
$useLegacyExport = true;

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
	<div id="mm-grid-container" style="width:98%">
		<?php echo $view->generateDataGrid($_POST); ?>
	</div>
</div>

<?php 
if (!$useLegacyExport)
{
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">

<div class="mbtDialog" id="export_status_dialog" style='display:none'>

    <h1>Member Export</h1>

    <div class="mbtBox mbtMale">
        <i class="fas fa-male fa-10x"></i>
    </div>

    <div class="mbtBox">
        <div class="mbtBox" id="mbtArrowAnim">
            <div class="mbtArrowSliding">
                <div class="mbtArrow"></div>
            </div> 
            <div class="mbtArrowSliding mbtDelay1">
                <div class="mbtArrow"></div>
            </div>

            <div class="mbtArrowSliding mbtDelay2">
                <div class="mbtArrow"></div>
            </div>

            <div class="mbtArrowSliding mbtDelay3">
                <div class="mbtArrow"></div>
            </div>
        </div>
    </div>

    <div class="mbtBox mbtFile">
        <i class="fas fa-file-alt fa-10x"></i>
    </div>

    <p>MemberMouse is exporting members ... </p>
    <p><input type='button' name='cancel' value="Cancel" onclick="mmjs.cancelExport(); " /></p>

</div>
<?php } ?>
