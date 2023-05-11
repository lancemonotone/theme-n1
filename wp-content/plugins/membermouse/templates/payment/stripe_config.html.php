<script>
function stripeTestModeChangeHandler()
{
	if(jQuery("#stripe_test_mode").is(":checked"))
	{
		jQuery("#stripe-test-account-info").show();
		jQuery(".stripe-test").show();
		jQuery(".stripe-live").hide();
	}
	else
	{
		jQuery(".stripe-test").hide();
		jQuery(".stripe-live").show();
		jQuery("#stripe-test-account-info").hide();

	}
}

function stripeElementsChangeHandler()
{
	if (jQuery("#stripe_elements_enabled").is(":checked"))
	{
		if (jQuery("#stripe_js_enabled").is(":checked"))
		{
			jQuery("#stripe_js_enabled").removeAttr("checked"); 
		} 
		jQuery(".stripe-js-block").show();
	}
	else
	{ 
		if (!jQuery("#stripe_js_enabled").is(":checked"))
		{
			jQuery(".stripe-js-block").hide();
		}
	}
}

function stripeJSChangeHandler()
{
	if (jQuery("#stripe_js_enabled").is(":checked"))
	{
		jQuery(".stripe-js-block").show();
		jQuery("#stripe_elements_enabled").removeAttr("checked"); 
	}
	else
	{
		jQuery(".stripe-js-block").hide();
	}
}

jQuery(function() {
	stripeTestModeChangeHandler();
	stripeJSChangeHandler();
	stripeElementsChangeHandler();
});

function showStripeTestCardNumbers()
{
	var str = "";

	str += "You can use the following test credit card numbers when testing payments.\n";
	str += "The expiration date must be set to the present date or later:\n\n";
	str += "- Visa: 4242424242424242\n";
	str += "- Visa: 4012888888881881\n";
	str += "- MasterCard: 5555555555554444\n";
	str += "- MasterCard: 5105105105105100\n";
	str += "- American Express: 378282246310005\n";
	str += "- American Express: 371449635398431\n";
	str += "- Discover: 6011111111111117\n";
	str += "- Discover: 6011000990139424\n";
	str += "- Diners Club: 30569309025904\n";
	str += "- Diners Club: 38520000023237\n";
	str += "- JCB: 3530111333300000\n";
	str += "- JCB: 3566002020360505\n\n";
	str += "Regulatory Test Card (SCA)\n";
	str += "- Visa: 4000002500003155\n";
	alert(str);
}
</script>

<div style="padding:10px;">
<img src='https://membermouse.com/assets/plugin_images/logos/stripe.png' />

<div style="margin-top:5px; margin-bottom:10px;">
<a href='http://support.membermouse.com/support/solutions/articles/9000020360-configuring-stripe' target='_blank'>Need help configuring Stripe?</a>
</div>

<div style="margin-bottom:10px;">
	<input type='checkbox' value='true' <?php echo (($p->inTestMode()==true)?"checked":""); ?> id='stripe_test_mode' name='payment_service[stripe][test_mode]' onClick="stripeTestModeChangeHandler()" />
	Enable Test Mode
</div>

<div style="margin-bottom:10px;">
	<input type='checkbox' value='true' <?php echo (($p->isStripeElementsEnabled()==true)?"checked":""); ?> id='stripe_elements_enabled' name='payment_service[stripe][stripe_elements_enabled]' onClick="stripeElementsChangeHandler()" />
	<i class="fa fa-star"></i> Enable Stripe Elements (<em><a href="https://www.pcisecuritystandards.org/pci_security/completing_self_assessment" target="_blank"><?php echo _mmt("Highest PCI Compliance"); ?></a>, <a href="https://support.membermouse.com/support/solutions/articles/9000194939-strong-customer-authentication-sca-compliance" target="_blank"><?php echo _mmt("SCA-Ready"); ?></a></em>)
</div>

<div style="margin-bottom:10px;">
	<input type='checkbox' value='true' <?php echo (($p->isStripeJSEnabled()==true)?"checked":""); ?> id='stripe_js_enabled' name='payment_service[stripe][stripe_js_enabled]' onClick="stripeJSChangeHandler()" />
	Enable Stripe.js (<em><a href="https://www.pcisecuritystandards.org/pci_security/completing_self_assessment" target="_blank"><?php echo _mmt("Lower PCI Compliance"); ?></a>, <a href="https://support.membermouse.com/support/solutions/articles/9000194939-strong-customer-authentication-sca-compliance" target="_blank"><?php echo _mmt("Not SCA-Ready"); ?></a></em>)
</div>

<div id="stripe-test-account-info" style="margin-bottom:10px; margin-left:10px; <?php echo (($p->inTestMode()==true)?"":"display:none;"); ?>">
	<div style="margin-bottom:5px;">
		<?php echo MM_Utils::getIcon('external-link', 'blue', '1.2em', '2px', '', "margin-right:3px;"); ?> 
		<a href="https://manage.stripe.com/account/apikeys" target="_blank">Set up or retrieve your Stripe API Keys</a>
	</div>
	<div style="margin-bottom:5px;">
		<?php echo MM_Utils::getIcon('external-link', 'blue', '1.2em', '2px', '', "margin-right:3px;"); ?> 
		<a href="https://manage.stripe.com/dashboard" target="_blank">Log Into your Stripe dashboard</a>
	</div>
	<div style="margin-bottom:5px;">
		<?php echo MM_Utils::getIcon('credit-card', 'blue', '1.3em', '1px', "Test Credit Card Numbers", "margin-right:3px;"); ?>
		<a href="javascript:showStripeTestCardNumbers()">Test Credit Card Numbers</a>
	</div>
	<div>
		<?php echo MM_Utils::getIcon('flask', 'blue', '1.3em', '1px', 'Setup Test Data', "margin-right:3px;"); ?>
		<a href="<?php echo MM_ModuleUtils::getUrl(MM_ModuleUtils::getPage(), MM_MODULE_TEST_DATA); ?>" target="_blank">Configure Test Data</a>
	</div>
</div>

<div style="margin-bottom:10px;">
	<span class="stripe-test" id="stripe-test-api-key-label">Test Secret Key</span>
	
	<p class="stripe-test" style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getTestAPIKey(); ?>' id='stripe_test_api_key' name='payment_service[stripe][test_api_key]' style='width: 275px;' />
	</p>
	
	<span class="stripe-live" id="stripe-live-api-key-label">Live Secret Key</span>
	
	<p class="stripe-live" style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getLiveAPIKey(); ?>' id='stripe_live_api_key' name='payment_service[stripe][live_api_key]' style='width: 275px;' />
	</p>
	
	<div class="stripe-js-block" <?php echo (($p->isStripeJSEnabled()==true)?"":"display:none;"); ?>>
		<span class="stripe-test" id="stripe-test-publishable-key-label">Test Publishable Key</span>
		
		<p class="stripe-test" style="margin-left:10px; font-family:courier; font-size:11px;">
			<input type='text' value='<?php echo $p->getTestPublishableKey(); ?>' id='stripe_test_publishable_key' name='payment_service[stripe][test_publishable_key]' style='width: 275px;' />
		</p>
		
		<span class="stripe-live" id="stripe-live-publishable-key-label">Live Publishable Key</span>
		
		<p class="stripe-live" style="margin-left:10px; font-family:courier; font-size:11px;">
			<input type='text' value='<?php echo $p->getLivePublishableKey(); ?>' id='stripe_live_publishable_key' name='payment_service[stripe][live_publishable_key]' style='width: 275px;' />
		</p>
	</div>
</div>

</div>
