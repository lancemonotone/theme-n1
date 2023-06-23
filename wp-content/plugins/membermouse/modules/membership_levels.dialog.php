<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
	
	$membership = new MM_MembershipLevel($p->id);
	$product = new MM_Product($membership->getDefaultProduct());
	
	if (!$p->duplicate)
	{
    	if(!$membership->isFree() && count($membership->getProductIds()) > 0 && !$membership->hasSubscribers()) {
    		$productsDisabled = "";
    	}
    	else {
    		$productsDisabled = "disabled='disabled'";
    	}
    	
    	if($membership->hasSubscribers()) {
    		$subTypeDisabled = "disabled='disabled'";
    	} 
    	else {
    		$subTypeDisabled = "";	
    	}
    	
		$archivedInfo = "";
    	if($membership->isDefault() == "0") {
    		$disableForDefault = "";
    	} 
    	else {
    		$disableForDefault = "disabled='disabled'";	
    		$subTypeDisabled = "disabled='disabled'";	
    	}
    
    	$nameModifier = "";
	} 
	else
	{
	   $nameModifier = _mmt("Copy of")." ";
	   $disableForDefault = "";
	   $subTypeDisabled = "";
	}
	
	$archivedInfo = "";
	if($membership->isDefault() == "0") {
		$disableForDefault = ""; 
	} 
	else {
		$disableForDefault = "disabled='disabled'";	
		$subTypeDisabled = "disabled='disabled'";	
	}
	
	$welcomeEmailChecked = $membership->doSendWelcomeEmail()?"checked" : "";
?>
<div id="mm-form-container">
	<table cellspacing="10">
		<tr>
			<td width="160"><?php echo _mmt("Name"); ?>*</td>
			<td><input id="mm-display-name" type="text" style="width:100%;" value='<?php echo $nameModifier.htmlentities($membership->getName(),ENT_QUOTES, 'UTF-8', true); ?>'/></td>
		</tr>
		
		<tr>
			<td>Status<?php echo MM_Utils::getInfoIcon(_mmt("New members cannot sign up for an inactive membership level. If there are currently members on this membership level, changing this to inactive will have no effect on them.")); ?></td>
			<td>
				<div id="mm-status-container">
					<input type="radio" name="status" value="active" onclick="mmjs.processForm()" <?php echo (($membership->getStatus()=="1")?"checked":""); ?> <?php echo $disableForDefault; ?> /> <?php echo _mmt("Active"); ?> &nbsp;
					<input type="radio" name="status" value="inactive" onclick="mmjs.processForm()" <?php echo (($membership->getStatus()=="0")?"checked":""); ?> <?php echo $disableForDefault; ?> /> <?php echo _mmt("Inactive"); ?>
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
			<?php 
				$wpRoleDescription = _mmt("Specify the WordPress role you want to assign to members with this membership level. Their WordPress role will be set to the role you specify when they first join or when their membership level is changed to this one. If you don't want MemberMouse to set or change the member's WordPress role then select the '&mdash; Don't set or change role &mdash;' option.");
			?>
			<td><?php echo _mmt("WordPress Role"); ?><?php echo MM_Utils::getInfoIcon($wpRoleDescription); ?></td>
			<td>
				<select name='mm_wp_role' id='mm_wp_role'>
				<?php echo MM_HtmlUtils::getWordPressRoles($membership->getWPRole()); ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td><?php echo _mmt("Membership Type"); ?></td>
			<td>
				<div id="mm-subscription-container">
					<input type="radio" name="subscription-type" value="free" onclick="mmjs.processForm()" <?php echo ($membership->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> <?php echo _mmt("Free"); ?> &nbsp;
					<input type="radio" name="subscription-type" value="paid" onclick="mmjs.processForm()" <?php echo (!$membership->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> <?php echo _mmt("Paid"); ?>
				</div>
				
				<input id="mm-has-associations" type="hidden" value="<?php echo $membership->hasSubscribers() ? "yes" : "no"; ?>" />
				<input id="mm-subscription-type" type="hidden" />
				
				<div id="mm-free-membership-settings" style="margin-top:5px; <?php if(!$membership->isFree()){ echo "display:none;"; } ?>">
					<span style="font-size:11px;">
					Free Membership Description <em>(optional)</em><?php echo MM_Utils::getInfoIcon(_mmt("Enter a description below for this membership level. It will be displayed in checkout forms when the [MM_Form_Data name='productDescription'] SmartTag is used.")); ?>
					</span>
					<textarea id="mm-description" name='description' style="width:100%; font-size: 11px"><?php echo $membership->getDescription(); ?></textarea>
				</div>
				
				<div id="mm-paid-membership-settings" style="margin-top:5px; <?php if($membership->isFree()){ echo "display:none;"; } ?>">
					<?php 
					    $idToUse = ($p->duplicate) ? "" : $membership->getId();
						$productsList = MM_HtmlUtils::getMembershipProducts($idToUse, $membership->getProductIds(), null, false);

						if(!empty($productsList))
						{
					?>
					<span style="font-size:11px;">
					<?php echo _mmt("Products"); ?><?php echo MM_Utils::getInfoIcon(_mmt("Paid membership levels can have multiple products associated with them which allows you to offer different pricing for the same membership. Select one or more products below to associate with this membership level.")); ?>
					</span>
					<select id="mm-products[]"  multiple  style='width: 100%;' size='6' onchange='mmjs.filterRegistrationProducts()'>
						<?php echo $productsList; ?>
					</select>
					<br/>
				
					<span style="font-size:11px">
					<?php echo _mmt("Select Multiple Products"); ?>: PC <code>ctrl + click</code> 
					Mac <code><img width="9" height="9" src="//km.support.apple.com/library/APPLE/APPLECARE_ALLGEOS/HT1343/ks_command.gif" alt="Command key icon" data-hires="true">
(Command key) + click</code>
					</span>
					
					<br/><br/>
					<span style="font-size:11px;">
					<?php echo _mmt("Default Product"); ?><?php echo MM_Utils::getInfoIcon(_mmt("Customers can purchase any of the products above to sign up for this membership level. It's also possible for customers to sign up for the membership directly in which case they'll end up purchasing the default product. You can specify the default product below.")); ?> 
					</span>
					<br/>
						<select id='mm-default-product-id'  >
							<?php 
								if($product->isValid() && $product->getId()>0)
								{ 
									$products = $membership->getProductIds();
									$productsArr = array();
									
									if(is_array($products))
									{
										foreach($products as $pid)
										{
											$prod = new MM_Product($pid);
											$productsArr[$pid] = $prod->getName();
										}
										
										$selections = MM_HtmlUtils::generateSelectionsList($productsArr, $product->getId());
										
										if(empty($selections))
										{
											echo "<option value=''>"._mmt("Select a product")."</option>";
										}
										else
										{
											echo $selections;
										}	
									}
									else
									{
										echo "<option value=''>"._mmt("Select a product")."</option>";
									}
									?>
									<?php 
								} 
								else
								{
									echo "<option value=''>"._mmt("Select a product")."</option>";
								}
							?>
						</select>
					<?php } else { ?>
					<input type="hidden" id="mm-products[]" name="mm-products-list" />
					<em><?php echo _mmt("No products available"); ?>.</em>
					<div style="font-size:11px; margin-top:10px;"><?php echo _mmt("Each product can only be associated with one membership level or bundle so once a product
					has been associated with an access type, it's no longer available for assignment"); ?>. <?php echo _mmt("You must"); ?> 
					<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_PRODUCTS)."&autoload=new"; ?>"><?php echo _mmt("create a new product");?></a> 
					<?php echo _mmt("in order to associate it with this membership level"); ?>.</div>
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
			<?php $expiryDescription = _mmt("Please Note: If you have your product set up to be a recurring subscription, there is not a need to set your membership level to expire. With a recurring subscription, if a payment is missed, MemberMouse will handle canceling the membership automatically. If you have a recurring subscription product and a membership level that is set to expire, you could create a scenario where a member continues to get billed, but they can no longer login to their account to cancel the subscription because of the expiry settings."); ?>
			<td><?php echo _mmt("Expiry Settings").MM_Utils::getInfoIcon($expiryDescription); ?></td>
			<td>
				<div id="mm-subscription-container">
					<input type="checkbox" name="expiry-setting" id="expiry-setting" onclick="mmjs.setToExpire()" <?php echo ($membership->doesExpire() ? "checked":""); ?>  /> <?php echo _mmt("Membership Level Expires");?>
					<input type='hidden' name='expiry_chk' id='expiry_chk' value='<?php echo ($membership->doesExpire() ? "1":"0"); ?>' />
				</div>
				
				<div style="margin-top:5px; display: none;" id='expires_div' >
					<?php echo _mmt("Expires After");?> <input type='text' id='expire_amount' name='expire_amount' value='<?php echo $membership->getExpireAmount(); ?>' style='width: 50px' /> 
					<select name='expire_period' id='expire_period'>
					<option value='days' <?php echo (($membership->getExpirePeriod()=="days")?"selected":""); ?>><?php echo _mmt("Days");?></option>
					<option value='weeks' <?php echo (($membership->getExpirePeriod()=="weeks")?"selected":""); ?>><?php echo _mmt("Weeks");?></option>
					<option value='months' <?php echo (($membership->getExpirePeriod()=="months")?"selected":""); ?>><?php echo _mmt("Months");?></option>
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
			<td><?php echo _mmt("Welcome Email");?></td>
			<td>
				<div>
					<input type='checkbox' id='mm-welcome-email-enabled-field' <?php echo $welcomeEmailChecked; ?> onchange="mmjs.welcomeEmailChanged()" /> <?php echo _mmt("Send welcome email to new members");?>
					<input type='hidden' id='mm-welcome-email-enabled' value='<?php echo ($membership->doSendWelcomeEmail() ? "1":"0"); ?>' />
				</div>
				<div style='clear:both;'>&nbsp;</div>
				<div  id='mm-welcome-email-row'>
					<div>
						<?php echo _mmt("From");?>
						<select id="mm-email-from" class="medium-text">
						<?php echo MM_HtmlUtils::getEmployees($membership->getEmailFromId()); ?>
						</select>
						<?php 
							$employeesUrl = MM_ModuleUtils::getUrl(MM_MODULE_GENERAL_SETTINGS, MM_MODULE_EMPLOYEES);
						?>
						<a href="<?php echo $employeesUrl ?>" style="font-size:10px" target="_blank"><?php echo _mmt("add employees");?></a>
					</div>
				
					<div style="margin-top:5px">
						<?php echo _mmt("Subject");?>*
						<input id="mm-email-subject" type="text" style="width:368px; font-family:courier; font-size: 11px;" value="<?php echo $membership->getEmailSubject(); ?>"/>
					</div>
					
					<div style="margin-top:5px">
						<?php echo _mmt("Body");?>* <?php echo MM_SmartTagLibraryView::smartTagLibraryButtons("mm-email-body"); ?>
						<?php 
							$validSmartTags = _mmt("Only the following SmartTags can be used here").":\n";
							$validSmartTags .= "[MM_Access_Decision] ("._mmt("you must provide an ID").")\n";
							$validSmartTags .= "[MM_Content_Data] ("._mmt("you must provide an ID").")\n";
							$validSmartTags .= "[MM_Content_Link] ("._mmt("you must provide an ID").")\n";
							$validSmartTags .= "[MM_CorePage_Link]\n";
							$validSmartTags .= "[MM_CustomField_Data]\n";
							$validSmartTags .= "[MM_Employee_Data]\n";
							$validSmartTags .= "[MM_Member_Data]\n";
							$validSmartTags .= "[MM_Member_Decision]\n";
							$validSmartTags .= "[MM_Member_Link]\n";
							$validSmartTags .= "[MM_Purchase_Link]";
						?>
						<span style="font-size:11px; color:#666666; margin-left: 5px;"><em><?php echo _mmt("Note: Only certain SmartTags can be used here");?></em></span>
						<?php echo MM_Utils::getInfoIcon($validSmartTags, ""); ?>
					</div>
					
					<div style="margin-top:5px">
						<textarea id='mm-email-body' style="width:100%; height:140px; font-family:courier; font-size: 11px;"><?php echo htmlentities($membership->getEmailBody(),ENT_QUOTES, 'UTF-8', true); ?></textarea>
					</div>
				</div>
				<input id='id' type='hidden' value='<?php if (!$p->duplicate && ($membership->getId() != 0)) { echo $membership->getId(); } ?>' />
			</td>
		</tr>
		
		<?php if(MM_MemberMouseService::hasPermission(MM_MemberMouseService::$FEATURE_BUNDLES)) { ?>
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td>Bundles<?php echo MM_Utils::getInfoIcon(_mmt("Select the bundles that members with this membership level should automatically get access to.")); ?></td>
			<td>
				<select id="mm-bundles[]" size="5" multiple="multiple" style="width:100%">
				<?php echo MM_HtmlUtils::getBundles($membership->getBundles()); ?>
				</select>
				
				<span style="font-size:11px">
					<?php echo _mmt("Select Multiple Bundles"); ?>: PC <code>ctrl + click</code> 
					Mac <code><img width="9" height="9" src="//km.support.apple.com/library/APPLE/APPLECARE_ALLGEOS/HT1343/ks_command.gif" alt="Command key icon" data-hires="true">
(Command key) + click</code>
				</span>
			</td>
		</tr>
		<?php } ?>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td><?php echo _mmt("Protected Categories"); ?><?php echo MM_Utils::getInfoIcon(_mmt("Select the WordPress categories that should be automatically protected by this membership level.")); ?></td>
			<td>
				<select id="mm-categories[]" size="5" multiple="multiple" style="width:100%">
				<?php echo MM_HtmlUtils::getWordPressCategories($membership->getCategoryIds()); ?>
				</select>
				
				<span style="font-size:11px">
					<?php echo _mmt("Select Multiple Categories"); ?>: PC <code>ctrl + click</code> 
					Mac <code><img width="9" height="9" src="//km.support.apple.com/library/APPLE/APPLECARE_ALLGEOS/HT1343/ks_command.gif" alt="Command key icon" data-hires="true">
(Command key) + click</code>
				</span>
			</td>
		</tr>
	</table>
	
	<input id='mm-is-default' type='hidden' value='<?php echo ($p->duplicate) ? false : $membership->isDefault(); ?>' />
	
	<script type='text/javascript'>
	mmjs.welcomeEmailChanged();
	mmjs.setToExpire();
	</script>
</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.save();" class="mm-ui-button blue"><?php echo _mmt("Save Membership Level");?></a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button"><?php echo _mmt("Cancel");?></a>
</div>
</div>