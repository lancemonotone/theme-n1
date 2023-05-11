<?php 
$api = new MM_Api($p->id);
$statusList = $api->getStatusList();
?>
<div id="mm-form-container">
<input type='hidden' id='mm-id' value='<?php echo $api->getId(); ?>' />
<table cellspacing="10">
<tr>
	<td>
		<?php echo _mmt("Name"); ?>
	</td>
	<td>
		<input type='text' id='mm-name' class="medium-text" value='<?php echo $api->getName(); ?>' />
	</td>
</tr>
<tr>
	<td>
		<?php echo _mmt("Status"); ?>
	</td>
	<td>
		<div id="mm-status-container">
			<input type="radio" id="mm-status-field"   name="mm-status-field"  value="active" onclick="mmjs.processForm()" <?php echo (($api->getStatus()=="1" || $api->getStatus() == "")?"checked":""); ?> onchange="mmjs.setStatusField()"  /> <?php echo _mmt("Active"); ?>
			<input type="radio" id="mm-status-field"   name="mm-status-field" value="inactive" onclick="mmjs.processForm()" <?php echo (($api->getStatus()=="0")?"checked":""); ?> onchange="mmjs.setStatusField()" /> <?php echo _mmt("Inactive"); ?>
			<input type='hidden' name='mm-status' id='mm-status' value='' />
		</div>
	</td>
</tr>
<tr>
	<td>
		<?php echo _mmt("API Key"); ?>
	</td>
	<td>
		<input type='text' id='mm-api-key' value='<?php echo $api->getApiKey(); ?>' /> <a href="#" onclick="mmjs.generateKey('mm-api-key')"><?php echo _mmt("Generate"); ?></a>
	</td>
</tr>
<tr>
	<td>
		<?php echo _mmt("API Password"); ?>
	</td>
	<td>
		<input type='text' id='mm-api-secret' value='<?php echo $api->getApiSecret(); ?>' /> <a href="#" onclick="mmjs.generateKey('mm-api-secret')"><?php echo _mmt("Generate"); ?></a>
	</td>
</tr>

</table>
</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.save();" class="mm-ui-button blue"><?php echo _mmt("Save API Credentials"); ?></a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button"><?php echo _mmt("Cancel"); ?></a>
</div>
</div>