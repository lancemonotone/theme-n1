<script>
function authNetCIMTestModeChangeHandler()
{
	if(jQuery("#authorizenetcim_use_test_gateway").is(":checked"))
	{
		jQuery("#auth-net-cim-test-account-info").show();
		jQuery(".authorizenetcim-test").show();
		jQuery(".authorizenetcim-live").hide()
	}
	else
	{
		jQuery("#auth-net-cim-test-account-info").hide();
		jQuery(".authorizenetcim-test").hide();
		jQuery(".authorizenetcim-live").show();
	}
}

function authNetCIMAcceptJSChangeHandler()
{
	if (jQuery("#authorizenetcim_accept_js_enabled").is(":checked"))
	{
		jQuery(".authorizenetcim-accept-js-block").show();
	}
	else
	{
		jQuery(".authorizenetcim-accept-js-block").hide();
	}
}

function showAuthNetCIMTestCardNumbers()
{
	var str = "";

	str += "You can use the following test credit card numbers when testing payments.\n";
	str += "The expiration date must be set to the present date or later:\n\n";
	str += "- American Express: 370000000000002\n";
	str += "- Discover: 6011000000000012\n";
	str += "- Visa: 4007000000027\n";
	str += "- Second Visa: 4012888818888\n";
	str += "- JCB: 3088000000000017\n";
	str += "- Diners Club/Carte Blanche: 38000000000006";

	alert(str);
}

jQuery(function() {
	authNetCIMTestModeChangeHandler();
	authNetCIMAcceptJSChangeHandler();
	jQuery("#authorizenetcim_accept_js_enabled").click(function() {
    	if (jQuery("#authorizenetcim_accept_js_enabled").is(":checked")) {
			jQuery("#authorizenetcim_accept_js_warning").show("fast");
    	}
    	else
    	{
    		jQuery("#authorizenetcim_accept_js_warning").hide("fast");
    	}
	});
	//first-run
	if (jQuery("#authorizenetcim_accept_js_enabled").is(":checked")) {
			jQuery("#authorizenetcim_accept_js_warning").show("fast");
    }
});

</script>

<div style="padding:10px;">
<img src='https://membermouse.com/assets/plugin_images/logos/authorizenet-cim.png' />

<div style="margin-top:5px; margin-bottom:10px;">
<a href='http://support.membermouse.com/support/solutions/articles/9000020404-configuring-authorize-net-cim' target='_blank'>Need help configuring Authorize.net CIM?</a>
</div>

<div style="margin-bottom:10px;">
	<input type='checkbox' value='true' <?php echo (($p->inTestMode()==true)?"checked":""); ?> id='authorizenetcim_use_test_gateway' name='payment_service[authorizenetcim][test_mode]' onclick="authNetCIMTestModeChangeHandler()" />
	Enable Test Mode
</div>

<div style="margin-bottom:10px;">
	<input type='checkbox' value='true' <?php echo (($p->isAcceptJSEnabled()==true)?"checked":""); ?> id='authorizenetcim_accept_js_enabled' name='payment_service[authorizenetcim][accept_js_enabled]' onClick="authNetCIMAcceptJSChangeHandler()" />
	<i class="fa fa-star"></i> Enable Accept.js (<em><a href="https://www.pcisecuritystandards.org/pci_security/completing_self_assessment" target="_blank"><?php echo _mmt("Highest PCI Compliance"); ?></a>, <a href="https://support.membermouse.com/support/solutions/articles/9000194939-strong-customer-authentication-sca-compliance" target="_blank"><?php echo _mmt("Not SCA-Ready"); ?></a></em>)
	<?php if (!$p->isAcceptJSReady()) { ?> 
	<br/>
	<span id="authorizenetcim_accept_js_warning" style="display:none"><br/><i class="fa fa-exclamation-circle" aria-hidden="true" style="color:red"></i> Accept.js requires a non-localhost domain, and the checkout and my account pages must be secured with a valid SSL certificate to function properly</span>
	<?php } ?>
</div>

<div id="auth-net-cim-test-account-info" style="margin-bottom:10px; margin-left:10px; <?php echo (($p->inTestMode()==true)?"":"display:none;"); ?>">
	<div style="margin-bottom:5px;">
		<?php echo MM_Utils::getIcon('external-link', 'blue', '1.2em', '2px', '', "margin-right:3px;"); ?>
		<a href="https://developer.authorize.net/testaccount/" target="_blank">Sign Up for a Test Gateway Account</a>
	</div>
	<div style="margin-bottom:5px;">
		<?php echo MM_Utils::getIcon('external-link', 'blue', '1.2em', '2px', '', "margin-right:3px;"); ?>
		<a href="https://sandbox.authorize.net/" target="_blank">Log Into Test Merchant Interface</a>
	</div>
	<div style="margin-bottom:5px;">
		<?php echo MM_Utils::getIcon('credit-card', 'blue', '1.3em', '1px', "Test Credit Card Numbers", "margin-right:3px;"); ?>
		<a href="javascript:showAuthNetCIMTestCardNumbers()">Test Credit Card Numbers</a>
	</div>
	<div>
		<?php echo MM_Utils::getIcon('flask', 'blue', '1.3em', '1px', 'Setup Test Data', "margin-right:3px;"); ?>
		<a href="<?php echo MM_ModuleUtils::getUrl(MM_ModuleUtils::getPage(), MM_MODULE_TEST_DATA); ?>" target="_blank">Configure Test Data</a>
	</div>
</div>

<div style="margin-bottom:10px;">
	<span class="authorizenetcim-test" id="authorizenetcim-test-login-label">Test API Login ID</span>
	
	<p class="authorizenetcim-test" style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getTestAPILogin(); ?>' id='authorizenetcim_test_api_login' name='payment_service[authorizenetcim][test_api_login]' style='width: 275px;' />
	</p>
	
	<span class="authorizenetcim-live"  id="authorizenetcim-live-login-label">API Login ID</span>
	
	<p class="authorizenetcim-live" style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getLiveAPILogin(); ?>' id='authorizenetcim_live_api_login' name='payment_service[authorizenetcim][live_api_login]' style='width: 275px;' />
	</p>
</div>

<div style="margin-bottom:10px;">
	<span class="authorizenetcim-test" id="authorizenetcim-test-transaction-key-label">Test Transaction Key</span>
	
	<p class="authorizenetcim-test"  style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getTestTransactionKey(); ?>' id='authorizenetcim_test_transaction_key' name='payment_service[authorizenetcim][test_transaction_key]' style='width: 275px;' />
	</p>
	
	<span class="authorizenetcim-live" id="authorizenetcim-live-transaction-key-label">Transaction Key</span>
	
	<p class="authorizenetcim-live" style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getLiveTransactionKey(); ?>' id='authorizenetcim_live_transaction_key' name='payment_service[authorizenetcim][live_transaction_key]' style='width: 275px;' />
	</p>
</div>

<div class="authorizenetcim-accept-js-block" <?php echo (($p->isAcceptJSEnabled()==true)?"":"display:none;"); ?>>
	<span class="authorizenetcim-test" id="authorizenetcim-test-public-client-key-label">Test Public Client Key</span>
	
	<p class="authorizenetcim-test" style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getTestPublicClientKey(); ?>' id='authorizenetcim_test_public_client_key' name='payment_service[authorizenetcim][test_public_client_key]' style='width: 275px;' />
	</p>
	
	<span class="authorizenetcim-live" id="authorizenetcim-live-public-client-key-label">Public Client Key</span>
	
	<p class="authorizenetcim-live" style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getLivePublicClientKey(); ?>' id='authorizenetcim_live_public_client_key' name='payment_service[authorizenetcim][live_public_client_key]' style='width: 275px;' />
	</p>
</div>

<div class="authorizenetcim-live" style="margin-bottom:10px;" id='authorizenetcim_live_validation_mode'>
	<input type='checkbox' value='true' <?php echo (($p->getLiveValidationMode()==true)?"checked":""); ?> name='payment_service[authorizenetcim][live_validation_mode]' />
	Perform credit card authorization before saving data
  	<p style="font-size:11px; margin-left:0px; padding-right:20px;margin-top:10px;">
	  	<?php echo MM_Utils::getInfoIcon("", ""); ?>
		When activating credit card authorizations, please note that Authorize.net CIM requires the Billing Address and the Billing Zip Code to be provided in order for the authorization to go through successfully.
	</p>
</div>

	

</div>
