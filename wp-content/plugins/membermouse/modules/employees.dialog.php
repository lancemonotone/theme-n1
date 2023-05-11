<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$employee = new MM_Employee($p->id);
$disableRole = "";
global $current_user;

// employees can't edit their own role
if($current_user->ID == $employee->getUserId())
{
	$disableRole = "disabled='disabled'";
}
?>
<div id="mm-form-container">
	<table cellspacing="10">
		<tr>
			<td colspan="2">
			<span style="font-size:11px;">
			<?php echo _mmt("Employee accounts are used to grant access to additional members of your team. Once the account has been created, the employee can login with the email address and password associated with the account."); ?> 
			</span>
			</td>
		</tr>
		<tr>
			<td><?php echo _mmt("Role"); ?></td>
			<td>
				<select id='mm-role-id' <?php echo $disableRole; ?> onchange="mmjs.toggleAccessRestrictions();">
				<?php 
				echo MM_HtmlUtils::generateSelectionsList(MM_Role::getRoleList(), $employee->getRoleId());
				?>
				</select>
				<div id='mm-role-desc-admin' style="font-size:11px; margin-top:4px; display:none;"><?php echo sprintf(_mmt("Employees with the %sAdministrator%s role have the same permissions as a standard WordPress administrator and have access to all MemberMouse configuration modules, member management pages and the reporting suite."),"<code>","</code>"); ?></div>
			<div id='mm-role-desc-support' style="font-size:11px; margin-top:4px; display:none;"><?php echo sprintf(_mmt("Employees with the %sSupport%s role will only be able to access the MemberMouse member management pages."),"<code>","</code>"); ?></div>
			<div id='mm-role-desc-sales' style="font-size:11px; margin-top:4px; display:none;">
			<?php echo sprintf(_mmt("Employees with the %sSales%s role will only be able to access the MemberMouse member management pages."),"<code>","</code>"); ?></div>
			<div id='mm-role-desc-product-mgr' style="font-size:11px; margin-top:4px; display:none;">
			<?php echo sprintf(_mmt("Employees with the %sProduct Manager%s role will only be able to access the MemberMouse product settings and member management pages.."),"<code>","</code>"); ?></div>
			<div id='mm-role-desc-analyst' style="font-size:11px; margin-top:4px; display:none;">
			<?php echo sprintf(_mmt("Employees with the %sAnalyst%s role will only be able to access the MemberMouse reporting suite.."),"<code>","</code>"); ?>
			</div>
			</td>
		</tr>
		<tr>
			<td><?php echo _mmt("Display Name"); ?>* </td>
			<td><input id="mm-display-name" type="text" class="medium-text" value='<?php echo $employee->getDisplayName(); ?>'/></td>
		</tr>
		<tr>
			<td><?php echo _mmt("Email"); ?>*</td>
			<td><input id="mm-email" type="text" class="medium-text" value='<?php echo $employee->getEmail(); ?>' <?php echo ($employee->isValid()) ? "disabled":""; ?>/></td>
		</tr>
		<?php if(!$employee->isValid()) { ?>
		<tr>
			<td><?php echo _mmt("Password"); ?>*</td>
			<td><input id="mm-password" type="password" value='' /></td>
		</tr>
		<?php } ?>
		<tr>
			<td><?php echo _mmt("Real Name"); ?></td>
			<td>
				<input id="mm-first-name" type="text" value='<?php echo $employee->getFirstName(); ?>'  />
				<input id="mm-last-name" type="text" value='<?php echo $employee->getLastName(); ?>'  />
			</td>
		</tr>
		<tr>
			<td><?php echo _mmt("Phone"); ?></td>
			<td><input id="mm-phone" type="text" value='<?php echo $employee->getPhone(); ?>' /></td>
		</tr>
		<tr id="mm-additional-permissions-row" style="display:none;">
			<td><?php echo _mmt("Additional Permissions"); ?></td>
			<td>
				<input id="mm-allow-export" type="checkbox" <?php echo ($employee->doAllowExport()) ? "checked":""; ?> onchange="mmjs.changeAllowExport();" /> <?php echo _mmt("Allow this employee to export data"); ?>
				<input id="mm-allow-export-val" type="hidden" value="<?php echo ($employee->doAllowExport()) ? "1":"0"; ?>" />
			</td>
		</tr>
		
		<?php 
		if(MM_MemberMouseService::hasPermission(MM_MemberMouseService::$FEATURE_EMPLOYEE_ACCOUNTS)) 
		{	
			$selectedMemberships = array();
			$restrictions = $employee->getAccessRescrictions(MM_Employee::$ACCESS_TYPE_MEMBERSHIP);
			
			foreach($restrictions as $key=>$value)
			{
				$selectedMemberships[$value] = $value;
			}
		?>
		<tr id="mm-access-restriction-row" style="display:none;">
			<td><?php echo _mmt("Access Restrictions"); ?></td>
			<td>
			<?php 
				$accessDesc = "By selecting one or more membership levels below you're indicating that the employee should only be able to manage members with one of those membership levels. When no membership levels are selected this indicates that the employee should be able to manage all members.";
			?>
			<?php echo _mmt("Allowed Membership Levels"); ?><?php echo MM_Utils::getInfoIcon($accessDesc); ?>
			<select id="mm-memberships[]" size="6" multiple="multiple" style="margin-top:5px; width:400px;">
			<?php echo MM_HtmlUtils::getMemberships($selectedMemberships); ?>
			</select>
			
			<p style="margin-top:5px; font-size:11px">
					<?php echo _mmt("Select Multiple Membership Levels"); ?>: PC <code>ctrl + click</code> 
					Mac <code><img width="9" height="9" src="//km.support.apple.com/library/APPLE/APPLECARE_ALLGEOS/HT1343/ks_command.gif" alt="Command key icon" data-hires="true">
(Command key) + click</code>
			</p>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<input id='id' type='hidden' value='<?php echo $employee->getId(); ?>' />
	<input id='mm-is-default' type='hidden' value='<?php echo $employee->isDefault(); ?>' />
</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.save();" class="mm-ui-button blue"><?php echo _mmt("Save Employee"); ?></a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button"><?php echo _mmt("Cancel"); ?></a>
</div>
</div>

<script>mmjs.toggleAccessRestrictions();</script>