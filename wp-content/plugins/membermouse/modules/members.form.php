<?php 
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
	// get default selections
	$selectedMembership = "";
	$selectedBundle = "";
	
	if(isset($_REQUEST["membershipId"]))
	{ 
		$selectedMembership = $_REQUEST["membershipId"];
		$_REQUEST["mm_memberships"] = array($selectedMembership);
	}
	
	if(isset($_REQUEST["bundleId"]))
	{ 
		$selectedBundle = $_REQUEST["bundleId"];
		$_REQUEST["mm_bundles"] = array($selectedBundle);
	}
?>
<div id="mm-form-container" style="background-color: #EAF2FA; padding-top:2px; padding-left:8px; padding-bottom:8px;">
	<table>
		<tr>
			<!-- LEFT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td>Search By</td>
					<td>
						<select id="mm-member-search-by-date" >
							<option value='user_registered'><?php echo _mmt('Registration Date'); ?></option>
							<option value='status_updated'>Status Changed Date</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>From</td>
					<td>
						<input id="mm-from-date" type="text" style="width: 152px" placeholder="mm/dd/yyyy" /> 
						<a onClick="jQuery('#mm-from-date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
					</td>
				</tr>
				<tr>
					<td>To</td>
					<td>
						<input id="mm-to-date" type="text" style="width: 152px" placeholder="mm/dd/yyyy" />
						<a onClick="jQuery('#mm-to-date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
					</td>
				</tr>
				<tr>
					<td><?php echo _mmt('Member ID'); ?></td>
					<td><input id="mm-member-id" type="text" size="25" /></td>
				</tr>
				<tr>
					<td><?php echo _mmt('First Name'); ?></td>
					<td><input id="mm-first-name" type="text" size="25" /></td>
				</tr>
				<tr>
					<td><?php echo _mmt('Last Name'); ?></td>
					<td><input id="mm-last-name" type="text" size="25" /></td>
				</tr>
				<tr>
					<td><?php echo _mmt('Email'); ?></td>
					<td><input id="mm-email" type="text" size="25" /></td>
				</tr>
			</table>
			</td>
			
			<!-- CENTER COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td><?php echo _mmt('Membership Levels'); ?></td>
					<td>
						<select id="mm-memberships[]" size="6" multiple="multiple" style="width:300px;">
						<?php echo MM_HtmlUtils::getMemberships($selectedMembership); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><?php echo _mmt('Bundles'); ?></td>
					<td>
						<select id="mm-bundles[]" size="6" multiple="multiple" style="width:300px;">
						<?php echo MM_HtmlUtils::getBundles($selectedBundle); ?>
						</select>
					</td>
				</tr>
			</table>
			</td>
			
			<!-- RIGHT COLUMN -->
			<td valign="top">
			<table cellspacing="5">
				<tr>
					<td><?php echo _mmt('Membership Status'); ?></td>
					<td>
						<select id="mm-member-status-types[]" size="5" multiple="multiple" style="width:120px;">
						<?php echo MM_HtmlUtils::getMemberStatusList(); ?>
						</select>
					</td>
				</tr>
				
				<?php if(MM_CustomField::hasCustomFields(false)) { ?>
				<tr>
					<td><?php echo _mmt('Custom Field 1'); ?></td>
					<td>
						<select id="mm-member-custom-field"  onchange="mmjs.changeCustomField('mm-member-custom-field');">
							<option value=''>None</option>
							<?php echo MM_HtmlUtils::getCustomFields(null, false); ?>
						</select>
						<br />
						<input type='text' id='mm-member-custom-field-value' value='' style='width: 200px;display:none' />
					</td>
				</tr>
				<?php if(count(MM_CustomField::getCustomFieldsList(false, false)) > 1) { ?>
				<tr>
					<td><?php echo _mmt('Custom Field 2'); ?></td>
					<td>
						<select id="mm-member-custom-field2"  onchange="mmjs.changeCustomField('mm-member-custom-field2');">
							<option value=''>None</option>
							<?php echo MM_HtmlUtils::getCustomFields(null, false); ?>
						</select>
						<br />
						<input type='text' id='mm-member-custom-field2-value' value='' style='width: 200px;display:none' />
					</td>
				</tr>
				<?php } ?>
				<?php } ?>
				
				<tr>
					<td><?php echo _mmt('Notes'); ?></td>
					<td><textarea id="mm-notes" value=''></textarea></td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
	
	<input type="button" class="mm-ui-button blue" value="<?php echo _mmt('Show Members'); ?>" onclick="mmjs.search(0);">
	<input type="button" class="mm-ui-button" value="<?php echo _mmt('Reset Form'); ?>" onclick="mmjs.resetForm();">
</div>

<script type='text/javascript'>
	jQuery(document).ready(function(){
		jQuery("#mm-from-date").datepicker({
				dateFormat: "mm/dd/yy"
		});
		jQuery("#mm-to-date").datepicker({
				dateFormat: "mm/dd/yy"
		});
		jQuery("#mm-form-container :input").keypress(function(e) {
	        if(e.which == 13) {
	            jQuery(this).blur();
	            mmjs.search(0);
	        }
	    });
				
	});
</script>