<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

//load csv parsing library
wp_enqueue_script("membermouse-papaparse", plugins_url(MM_PLUGIN_NAME."/lib/papaparse/papaparse.min.js"), array(), MemberMouse::getPluginVersion());
wp_enqueue_script("membermouse-blockUI", plugins_url(MM_PLUGIN_NAME."/resources/js/common/jquery.blockUI.js"), array(), MemberMouse::getPluginVersion(), true);
?>

<style>
.mm-import-wizard-step {
	font: 21px/1.3 'PT Sans','Myriad Pro',Myriad,Arial,Helvetica,sans-serif;
    margin-bottom: 20px;
    color: #004D66;
    margin-top: 20px;
}
.mm-import-wizard-notice {
    color: #F90;
	font: 16px/0.4em 'PT Sans','Myriad Pro',Myriad,Arial,Helvetica,sans-serif;
    margin-bottom: 20px;
    margin-top: 20px;
}
#mm-form-container td {
    font-size: 14px;
    vertical-align: middle;
}

.mm-import-complete-table {
	margin: 0 auto;
}
</style>

<div class="mm-wrap" style="font-size:14px;">

	<form onSubmit="javascript: return false;">
		<p class="mm-import-wizard-step"><?php echo _mmt('Step 1: Download Import Template',MM_LANGUAGE_DOMAIN); ?></p>

		<p style="margin-left:12px;">
			<a class="mm-ui-button" onclick="mmjs.downloadTemplate('<?php echo MM_MODULES_URL; ?>','<?php echo MM_EXPORT_FILE_MEMBERS_IMPORT_TEMPLATE; ?>');"><?php echo MM_Utils::getIcon('download', '', '1.3em', '2px'); ?> <?php echo _mmt('Download Import Template'); ?></a>
			<a class="mm-ui-button" onclick="stl_js.showIdLookup('');"><?php echo MM_Utils::getIcon('search', '', '1.3em', '2px'); ?> <?php echo _mmt('Lookup IDs'); ?></a>
			<a class="mm-ui-button" href="http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements" target="_blank"><?php echo MM_Utils::getIcon('globe', '', '1.3em', '2px'); ?> <?php echo _mmt('ISO Country Codes'); ?></a>
		</p>
		
		<p style="margin-left:12px; margin-top:20px;">
			<?php echo _mmt('Member Status IDs:',MM_LANGUAGE_DOMAIN); ?>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$ACTIVE); ?> 1
			</span>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$CANCELED); ?> 2
			</span>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$LOCKED); ?> 3
			</span>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$PAUSED); ?> 4
			</span>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$OVERDUE); ?> 5
			</span>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$PENDING_ACTIVATION); ?> 6
			</span>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$ERROR); ?> 7
			</span>
			<span style="margin-left:10px; font-size:14px;">
				<?php echo MM_Status::getImage(MM_Status::$EXPIRED); ?> 8
			</span>
		</p>
		
		<p class="mm-import-wizard-step" style="margin-bottom:10px;"><?php echo _mmt('Step 2: Upload Import File',MM_LANGUAGE_DOMAIN); ?></p>
		
		<div id="mm-upload-import-file-form">
			<table cellspacing="12">
			<tr>
				<td width="120px;">
					<input id="mm-import-file-from-computer-radio" type="radio" checked value="computer" name="import-file-location">
					<?php echo _mmt('From Computer',MM_LANGUAGE_DOMAIN); ?>
				</td>
				<td>
					<div id="mm-file-upload-container">
						<input id="fileToUpload" name="fileToUpload" type="file" size="30" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<input id="mm-import-file-from-url-radio" type="radio" value="url" name="import-file-location">
					<?php echo _mmt('From URL', MM_LANGUAGE_DOMAIN); ?>
				</td>
				<td>
					<span style="font-family: courier; font-size: 12px;">
						<input type="text" id="mm-import-file-from-url-source" style="width:430px;" />
					</span>
				</td>
			</tr>
			</table>
		</div>
		
		<p class="mm-import-wizard-step" style="margin-bottom:10px;"><?php echo _mmt('Step 3: Configure Import Settings'); ?></p>
		
		<table cellspacing="12">
			<tr>
				<td width="140"><?php echo _mmt('Import members as'); ?></td>
				<td>
					<select id="mm-membership-selector" name="mm-membership-selector">
						<?php echo MM_HtmlUtils::getMemberships(null, true); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type='checkbox' id='mm-send-welcome-email' name='mm-send-welcome-email' /> <?php echo _mmt('Send welcome email to new members'); ?>
				</td>
			</tr>
		</table>
		
		<p class="mm-import-wizard-step" style="margin-top:10px;"><?php echo _mmt('Step 4: Import Members'); ?></p>
		
		<p style="margin-left:12px;">
			<input type="hidden" id="mm-import-file-source" name="mm-import-file-source" />
			<input type="hidden" id="mm-import-file-from-computer" name="mm-import-file-from-computer" />
			<input type="hidden" id="mm-import-file-from-url" name="mm-import-file-from-url" />
			<input type='button' class="mm-ui-button green" value='<?php echo _mmt('Import Members'); ?>' onClick="return mmjs.validateForm();"/>
		</p>
	</form>
</div>
<div class="mbtDialog" id="import_status_dialog" style='display:none'>
    <h3><i class="fa fa-download" style="top:1px;"></i>  Import Members</h3>
    <p>Your members are being imported. Please wait ... </p>
    <div class="importProgress">
    	<label for="importProgress"><?php echo _mmt("Import Progress"); ?>:</label>
		<progress id="importProgress" value="0" max="100">0</progress>
		<div id="importProgressText"></div>
		<p><input type='button' name='cancel' value="Cancel" onclick="mmjs.closeImportStatusDialog('<?php echo _mmt("Import Cancelled"); ?>');" class="mm-ui-button" /></p>
	</div> 	
	<div class="importComplete" style="display:none">
		<table class="mm-import-complete-table">
			<tr>
				<td><?php echo _mmt("Members Imported"); ?>:</td>
				<td id="importSuccessCount"></td>
			</tr>
			<tr>
				<td><?php echo _mmt("Errors"); ?>:</td>
				<td id="importErrorCount"></td>
			</tr>
		</table>
		<p><input type='button' name='closeStatusWindow' value="Close" onclick="mmjs.closeImportStatusDialog();" class="mm-ui-button" /></p>
	</div>
</div>