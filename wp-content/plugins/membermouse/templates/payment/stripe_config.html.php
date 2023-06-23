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

function stripeConnectClickHandler()
{

	jQuery('.mm-stripe-connect-new').on('click', function(e) {
		e.preventDefault();

		var stripe_connect_button = jQuery(this);

		var form_data = jQuery('#mm-payment-settings-form').serialize();

    var href = stripe_connect_button.data('url');
    var nonce = stripe_connect_button.data('nonce');

    jQuery.ajax({
				type: "POST",
			    url: MemberMouseGlobal.ajaxurl,
			    data: {
			    	'form_data' : form_data,
			    	'action' : 'mm_stripe_connect_save_settings',
			    	'security' : nonce
			    },
			    dataType: 'json',
			    cache: false
			}).done(function(response) {
				window.location = href;
			}).fail(function(result) {
				console.log("AJAX call to server to mm_stripe_connect_save_settings failed.");
			});

     return false;

	});
}

function stripeDisconnectClickHandler() {
	jQuery('body').on('click', '.mm_stripe_disconnect_button', function(e) {
    var proceed = confirm( jQuery(this).data('disconnect-msg') );
    if ( false === proceed ) {
      e.preventDefault();
    }
  });
}

jQuery(function() {
	stripeTestModeChangeHandler();
	stripeJSChangeHandler();
	stripeElementsChangeHandler();
	stripeConnectClickHandler();
	stripeDisconnectClickHandler();
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

<style>
.mm-payment-option-prompt {
  background: rgba(139, 139, 150, 0.1);
  box-shadow: 0 0 1px #bbb;
  align-items: center;
  justify-content: center;
  padding: 1em;
  color: #0a2540;
  width: 600px;
  text-align: center;
  font-size: 15px;
}
.mm-payment-option-prompt.connected .stripe-btn {
  border-radius: 7px;
  background: #00528c;
  color: white;
  font-weight: bold;
  padding: 0px 10px;
}
</style>

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

<?php
$style_keys = '';
$show_keys = false;
if ( ! isset( $_GET['display-keys'] ) && ! isset( $_COOKIE['mm_stripe_display_keys'] ) && ! defined( 'MM_DISABLE_STRIPE_CONNECT' ) ) {
  $style_keys = ' display:none;';
} else {
  $show_keys = true;
}

$account_email = MM_OptionUtils::getOption( MM_OptionUtils::$OPTION_KEY_AUTH_ACCOUNT_EMAIL );
$secret = MM_OptionUtils::getOption( MM_OptionUtils::$OPTION_KEY_AUTH_ACCOUNT_SECRET );
$site_uuid = MM_OptionUtils::getOption( MM_OptionUtils::$OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );
$service_account_name = MM_OptionUtils::getOption( 'mm_stripe_service_account_name' );
$id = MM_StripeConnect::get_method_id();

// If we're authenticated then let's present a stripe url otherwise an authenticator url
if( $account_email && $secret && $site_uuid ) {
  $stripe_connect_url = MM_StripeConnect::get_stripe_connect_url();
}
else {
  $stripe_connect_url = MM_AuthenticatorService::get_auth_connect_url( true, MM_StripeConnect::get_method_id() );
}
?>
<?php if ( 'connected' === MM_StripeConnect::stripe_connect_status() ) : ?>
<div style="margin-bottom:10px;">
  <?php
    $refresh_url = add_query_arg( array( 'action' => 'mm_stripe_connect_refresh', 'method-id' => MM_StripeConnect::get_method_id(), '_wpnonce' => wp_create_nonce('stripe-refresh') ), admin_url('admin-ajax.php') );
    $disconnect_url = add_query_arg( array( 'action' => 'mm_stripe_connect_disconnect', 'method-id' => $id, '_wpnonce' => wp_create_nonce('stripe-disconnect') ), admin_url('admin-ajax.php') );
    $disconnect_confirm_msg = __( 'Disconnecting from this Stripe Account will block webhooks from being processed, and prevent MemberMouse payments associated with it from working.', 'membermouse' );
  ?>
  <div id="stripe-connected-actions" class="mm-payment-option-prompt connected">
    <?php if ( empty( $service_account_name ) ): ?>
      <?php _e( 'Connected to Stripe', 'membermouse' ); ?>
    <?php else: ?>
      <?php printf( __( 'Connected to: %1$s %2$s %3$s', 'membermouse' ), '<strong>', $service_account_name, '</strong>' ); ?>
    <?php endif; ?>
    &nbsp;
    <span style="<?php echo $style_keys; ?>">
    <a href="<?php echo $refresh_url; ?>"
       class="stripe-btn  mm_stripe_refresh_button button-secondary"><?php _e( 'Refresh Stripe Credentials', 'membermouse' ); ?></a></span>
    <a href="<?php echo $disconnect_url; ?>" class=" stripe-btn  mm_stripe_disconnect_button button-secondary"
       data-disconnect-msg="<?php echo $disconnect_confirm_msg; ?>">
      <?php _e( 'Disconnect', 'membermouse' ); ?>
    </a>
  </div>
</div>
<?php endif; ?>

<div style="margin-bottom:10px;<?php echo $style_keys; ?>">
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

<?php 
if ( 'disconnected' === MM_StripeConnect::stripe_connect_status() ) : ?>
<div class="mm-payment-option-prompt">
  <h4><strong><?php _e( 'Re-Connect to Stripe', 'membermouse' ); ?></strong></h4>
  <p><?php _e( 'This Payment Method has been disconnected so it may stop working for new and recurring payments at any time. To prevent this, re-connect your Stripe account by clicking the "Connect with Stripe" button below.', 'membermouse' ); ?></p>
  <p>
    <a href="#" 
				data-url="<?php echo $stripe_connect_url; ?>" 
				data-id="<?php echo $id; ?>" 
				data-nonce="<?php echo wp_create_nonce( "new-stripe-connect" ); ?>" 
				class="mm-stripe-connect-new">
	        <img src="<?php echo MM_Utils::getImageUrl('stripe-connect'); ?>" width="200" alt="<?php esc_attr_e( '"Connect with Stripe" button', 'membermouse' ); ?>">
      </a>
  </p>
</div>
<?php elseif ( 'connected' !== MM_StripeConnect::stripe_connect_status() ) : ?>
<div style="margin-bottom:10px;">
		<p style="margin-left:10px;">
			<a href="#" 
				data-url="<?php echo $stripe_connect_url; ?>" 
				data-id="<?php echo $id; ?>" 
				data-nonce="<?php echo wp_create_nonce( "new-stripe-connect" ); ?>" 
				class="mm-stripe-connect-new">
	        <img src="<?php echo MM_Utils::getImageUrl('stripe-connect'); ?>" width="200" alt="<?php esc_attr_e( '"Connect with Stripe" button', 'membermouse' ); ?>">
	      </a>
		</p>
</div>
<?php endif; ?>

</div>
