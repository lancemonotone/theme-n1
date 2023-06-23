<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
	$bundle = new MM_Bundle($p->id);
	
	$subTypeDisabled = "";	

	if (!$p->duplicate && $bundle->hasSubscribers()) 
    {
    	$subTypeDisabled = "disabled='disabled'";	
	}
	
	$provider = MM_EmailServiceProviderFactory::getActiveProvider();
	$provider_token = strtolower($provider->getToken());
	$nameModifier = $p->duplicate ? (_mmt("Copy of")." ") : "";
?>
<script>
function unlockBundle()
{
	jQuery("#expiry-setting").prop('disabled', false);
}
</script>

<style>
.noticeMessage {
	background-color: #FFFFE0;
    border-color: #E6DB55;
    border-style: solid;
    border-width: 1px;
	padding: 5px 10px;
	border-radius: 3px;
	font-size: 12px;
	color: #333;
}
</style>

<div id="mm-form-container">
	<?php 
	if($bundle->hasSubscribers() && !$p->duplicate)
	{
		echo "<p class='noticeMessage'>";
		echo _mmt("This bundle is being used by members so some properties have been locked to avoid accidental editing. ");
		echo sprintf(_mmt("Some locked properties can be edited, %s click here to unlock them %s. Note "),"<a href=\"javascript:unlockBundle()\">","</a>");
		echo _mmt("that any changes made to locked properties will be applied to all members who currently have this bundle.");
		echo "</p>";
	}
	?>
	<table cellspacing="10">
		<tr>
			<td width="150"><?php echo _mmt("Name"); ?>*</td>
			<td><input id="mm-display-name" type="text" class="long-text" value="<?php echo $nameModifier.htmlentities($bundle->getName(), ENT_QUOTES, "UTF-8"); ?>"/></td>
		</tr>
		
		<tr>
			<td>Status</td>
			<td>
				<div id="mm-status-container">
					<input type="radio" name="status" value="active" onclick="mmjs.processForm()" <?php echo (($bundle->getStatus()=="1")?"checked":""); ?>  /> <?php echo _mmt("Active"); ?>  &nbsp;
					<input type="radio" name="status" value="inactive" onclick="mmjs.processForm()" <?php echo (($bundle->getStatus()=="0")?"checked":""); ?>  /> <?php echo _mmt("Inactive"); ?>
				</div>
				<input id="mm-status" type="hidden" />
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td><?php echo _mmt("Bundle Type"); ?></td>
			<td>
				<div id="mm-subscription-container">
					<input type="radio" id="subscription-type-free" name="subscription-type" value="free" onclick="mmjs.processForm()" <?php echo ($bundle->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> <?php echo _mmt("Free"); ?> &nbsp;
					<input type="radio" id="subscription-type-paid"  name="subscription-type" value="paid" onclick="mmjs.processForm()" <?php echo (!$bundle->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> <?php echo _mmt("Paid"); ?>
				</div>
				
				<input id="mm-has-associations" type="hidden" value="<?php echo $bundle->hasSubscribers() ? "yes" : "no"; ?>" />
				<input id="mm-subscription-type" type="hidden" />
				
				<div id="mm-paid-bundle-settings" style="margin-top:5px; <?php if($bundle->isFree()) { echo "display:none;"; } ?>">
					<?php 
					    $idToUse = ($p->duplicate) ? "" : $bundle->getId();
					    $productsList = MM_HtmlUtils::getBundleProducts($idToUse, $bundle->getAssociatedProducts(),null, !$bundle->isValid()); //only show archived products when editing
					    
						if(!empty($productsList))
						{
					?>
					<span style="font-size:11px;">
					Products
					<?php echo MM_Utils::getInfoIcon(_mmt("Paid bundles can have multiple products associated with them which allows you to offer different pricing for the same bundle Select one or more products below to associate with this bundle."), ""); ?> 
					</span>
					
					<select id="mm-products[]" name="mm-products-list"  multiple="multiple" style='width: 95%;' size='6'>
						<?php echo $productsList; ?>
					</select>
					
					<br/>
				
					<span style="font-size:11px">
					<?php echo _mmt("Select Multiple Products"); ?>: PC <code>ctrl + click</code> 
					Mac <code><img width="9" height="9" src="//km.support.apple.com/library/APPLE/APPLECARE_ALLGEOS/HT1343/ks_command.gif" alt="Command key icon" data-hires="true">
(Command key) + click</code>
					</span>
					<?php } else { ?>
					<input type="hidden" id="mm-products[]" name="mm-products-list" />
					<em><?php echo _mmt("No products available"); ?>.</em>
					<div style="font-size:11px; margin-top:10px;"><?php echo _mmt("Each product can only be associated with one membership level or bundle so once a product has been associated with an access type, it's no longer available for assignment"); ?>. <?php echo sprintf(_mmt("You must %s create a new product %s in order to associate it with this bundle"),'<a href="'.MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_PRODUCTS).'&autoload=new">','</a>'); ?>.</div>
					<?php } ?>
				</div>
			</td>
		</tr>	
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>	
		
		<tr>
			<?php $dfltMbrshpDesc = _mmt("If this bundle is the first thing a customer buys, the default membership level specified here will be applied to their account. If an existing member purchases this bundle, their membership level will remain unchanged. If you select 'use system default', the default system membership level will be used as defined on the Membership Levels screen. Only free membership levels can be used as the default membership level."); ?>
			<td nowrap>Default Membership<?php echo MM_Utils::getInfoIcon($dfltMbrshpDesc); ?></td>
			<td>
				<select id="mm-dflt-membership-selector">
					<?php 
						$dfltMembershipId = $crntSelection = $bundle->getDfltMembershipId();
						
						if(intval($dfltMembershipId) == 0)
						{
							$crntSelection = null;
						}
						echo MM_HtmlUtils::getMemberships($crntSelection, false, MM_MembershipLevel::$SUB_TYPE_FREE); ?>
					<option value="0" <?php echo ($dfltMembershipId == 0) ? "selected":"";?>>&mdash; use system default &mdash;</option>
				</select>
			</td>
		</tr>
		
		<tr <?php echo ($provider_token == "default")?"style='display:none;'":""; ?>>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr <?php echo (!$p->duplicate && ($provider_token == "default"))?"style='display:none;'":""; ?>>
			<?php $shortNameDesc = _mmt("Short names are passed to your email provider allowing you to segment your list based on which bundles a particular member has access to. You can name the short names anything you want. The best practice is to name it based on your bundle display name so that when you're looking to segment your list on your email provider you can readily associate the short names with the bundle."); ?>
			<td nowrap>Short Name*<?php echo MM_Utils::getInfoIcon($shortNameDesc); ?></td>
			<td><input id="mm-short-name" type="text" maxlength="10" class="long-text" value="<?php echo htmlentities($bundle->getShortName(), ENT_QUOTES, "UTF-8"); ?>" <?php if ($bundle->hasSubscribers() && !empty($bundle->getShortName)) { echo "readonly='readonly'"; } ?> />
				<span id="mm-short-name-unique-status" style="display:none;"><?php echo _mmt("Short Name is Unique"); ?></span>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>	
		
		<tr>
			<?php $expiryDescription = _mmt("Please Note: If you have your product set up to be a recurring subscription, there is not a need to set your bundle to expire. With a recurring subscription, if a payment is missed, MemberMouse will handle canceling the bundle automatically."); ?>
			<td><?php echo _mmt("Expiry Settings").MM_Utils::getInfoIcon($expiryDescription); ?></td>
			<td>
				<div id="mm-subscription-container">
					<input type='hidden' name='should-expire' id='should-expire' value='<?php echo (($bundle->doesExpire())?"1":"0"); ?>' />
					<input type="checkbox" name="expiry-setting" id="expiry-setting" value="1" onclick="mmjs.setToExpire()" <?php echo ($bundle->doesExpire() ? "checked":""); ?> <?php echo ($subTypeDisabled) ? "disabled" : ""; ?> /> Bundle Expires
				</div>
				
				<div style="margin-top:5px; display: none;" id='expires_div' >
					Expires After <input type='text' id='expire_amount' name='expire_amount' value='<?php echo $bundle->getExpireAmount(); ?>' style='width: 50px' /> 
					<select name='expire_period' id='expire_period'>
					<option value='days' <?php echo (($bundle->getExpirePeriod()=="days")?"selected":""); ?>><?php echo _mmt("Days"); ?></option>
					<option value='weeks' <?php echo (($bundle->getExpirePeriod()=="weeks")?"selected":""); ?>><?php echo _mmt("Weeks"); ?></option>
					<option value='months' <?php echo (($bundle->getExpirePeriod()=="months")?"selected":""); ?>><?php echo _mmt("Months"); ?></option>
					</select>
				</div>
				
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td><?php echo _mmt("Protected Categories"); ?><?php echo MM_Utils::getInfoIcon(_mmt("Select the WordPress categories that should be automatically protected by this bundle.")); ?></td>
			<td>
				<select id="mm-categories[]" size="5" multiple="multiple" style="width:95%">
				<?php echo MM_HtmlUtils::getWordPressCategories($bundle->getCategories()); ?>
				</select>
				
				<span style="font-size:11px">
					<?php echo _mmt("Select Multiple Categories"); ?>: PC <code>ctrl + click</code> 
					Mac <code><img width="9" height="9" src="//km.support.apple.com/library/APPLE/APPLECARE_ALLGEOS/HT1343/ks_command.gif" alt="Command key icon" data-hires="true">
(Command key) + click</code>
				</span>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>	
		
		<tr>
			<td>Description</td>
			<td>
				<textarea id="mm-description" name='description' class="long-text" style="font-size:11px;"><?php echo $bundle->getDescription(); ?></textarea>
			</td>
		</tr>
	</table>
	
	<input id='id' type='hidden' value='<?php if (!$p->duplicate && ($bundle->getId() != 0)) { echo $bundle->getId(); } ?>' />
	<input id='autogen_shortname' type='hidden' value='<?php echo (($provider_token == "default")&&($bundle->getShortName() == ""))?"true":"false";?>' />
</div>

<script type='text/javascript'>
<?php if($bundle->doesExpire()) { ?>
mmjs.setToExpire();
<?php } ?>
mmjs.processForm();
</script>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.save();" class="mm-ui-button blue"><?php echo _mmt("Save Bundle"); ?></a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button"><?php echo _mmt("Cancel"); ?></a>
</div>
</div>