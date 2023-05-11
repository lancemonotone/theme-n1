<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_forget_member_settings_saved"]))
{
    $mmForgetMemberEmailAddress = (isset($_POST['mm_forget_member_email_address']) && ($_POST['mm_forget_member_email_address'] == "true"));
    $mmForgetMemberAddress = (isset($_POST['mm_forget_member_address']) && ($_POST['mm_forget_member_address'] == "true"));
    $mmForgetMemberAddressCountry = (isset($_POST['mm_forget_member_address_country']) && ($_POST['mm_forget_member_address_country'] == "true"));
    $mmForgetMemberOrderAddress = (isset($_POST['mm_forget_member_order_address']) && ($_POST['mm_forget_member_order_address'] == "true"));
    $mmForgetMemberOrderCountry = (isset($_POST['mm_forget_member_order_country']) && ($_POST['mm_forget_member_order_country'] == "true"));
    $mmForgetMemberActivityLog = (isset($_POST['mm_forget_member_activity_log']) && ($_POST['mm_forget_member_activity_log'] == "true"));
    $mmForgetMemberCustomFields = (isset($_POST['mm_forget_member_custom_fields']) && ($_POST['mm_forget_member_custom_fields'] == "true"));
    
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_EMAIL_ADDRESS, $mmForgetMemberEmailAddress);
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ADDRESS, $mmForgetMemberAddress);
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ADDRESS_COUNTRY, $mmForgetMemberAddressCountry);
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ORDER_ADDRESS, $mmForgetMemberOrderAddress);
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ORDER_COUNTRY, $mmForgetMemberOrderCountry);
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ACTIVITY_LOG, $mmForgetMemberActivityLog);
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_CUSTOM_FIELDS, $mmForgetMemberCustomFields);
    
}
else
{
    $mmForgetMemberEmailAddress = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_EMAIL_ADDRESS);
    $mmForgetMemberAddress = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ADDRESS);
    $mmForgetMemberAddressCountry = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ADDRESS_COUNTRY);
    $mmForgetMemberOrderAddress = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ORDER_ADDRESS);
    $mmForgetMemberOrderCountry = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ORDER_COUNTRY);
    $mmForgetMemberActivityLog = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_ACTIVITY_LOG);
    $mmForgetMemberCustomFields = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGET_MEMBER_CUSTOM_FIELDS);
}
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 
<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("Forget Member Settings"); ?> <span style="font-size:12px;"><a href="http://support.membermouse.com/solution/articles/9000147131--forget-this-member-feature-anonymize-a-member" target="_blank"><?php echo _mmt("Learn more"); ?></a></span></p>

	<input id="mm_forget_member_settings_saved" name="mm_forget_member_settings_saved" type="hidden" value="true" />
   	<div style="margin-top:10px;">
		<strong><?php echo _mmt("Anonymize the following information when forgetting a member"); ?>:</strong>
	</div>
	
	<div style="margin-top:10px; margin-left:0px;">
		<u>User Information</u> 
	</div>
	
	<div style="margin-top:10px; margin-left:20px;">
		<input id="mm_forget_member_email_address" name="mm_forget_member_email_address" type="checkbox" value="true" <?php echo (($mmForgetMemberEmailAddress=="1")?"checked":""); ?> />
		<?php echo _mmt("Email Address"); ?>
	</div>
	
	<div style="margin-top:10px; margin-left:20px;">
		<input id="mm_forget_member_address" name="mm_forget_member_address" type="checkbox" value="true" <?php echo (($mmForgetMemberAddress=="1")?"checked":""); ?> />
		<?php echo sprintf(_mmt("First Name, Last Name, Billing %s shipping address information (except country)"),"&amp;"); ?>
	</div>
	
	<div style="margin-top:10px; margin-left:20px;">
		<input id="mm_forget_member_address_country" name="mm_forget_member_address_country" type="checkbox" value="true" <?php echo (($mmForgetMemberAddressCountry=="1")?"checked":""); ?> />
		<?php echo sprintf(_mmt("Billing %s shipping country"),"&amp;"); ?>
	</div>
	
	<div style="margin-top:10px; margin-left:20px;">
		<input id="mm_forget_member_activity_log" name="mm_forget_member_activity_log" type="checkbox" value="true" <?php echo (($mmForgetMemberActivityLog=="1")?"checked":""); ?> />
		<?php echo _mmt("Activity Log"); ?>
	</div>
	
	<div style="margin-top:10px; margin-left:20px;">
		<input id="mm_forget_member_custom_fields" name="mm_forget_member_custom_fields" type="checkbox" value="true" <?php echo (($mmForgetMemberCustomFields=="1")?"checked":""); ?> />
		<?php echo _mmt("Custom Fields"); ?>
	</div>
	 
	<div style="margin-top:10px; margin-left:0px;">
		<u>Order Information</u>
	</div>
	
	<div style="margin-top:10px; margin-left:20px;">
		<input id="mm_forget_member_order_address" name="mm_forget_member_order_address" type="checkbox" value="true" <?php echo (($mmForgetMemberOrderAddress=="1")?"checked":""); ?> />
		<?php echo sprintf(_mmt("Billing %s shipping address information stored with previous orders (except country)"),"&amp;"); ?>
	</div>
	
	<div style="margin-top:10px; margin-left:20px;">
		<input id="mm_forget_member_order_country" name="mm_forget_member_order_country" type="checkbox" value="true" <?php echo (($mmForgetMemberOrderCountry=="1")?"checked":""); ?> />
		<?php echo sprintf(_mmt("Billing %s shipping country stored with previous orders"),"&amp;"); ?>
	</div>
</div>