<script>
function testSetTestProcessorOptionsDisplayState()
{
	var shouldShowOverrideDetails = jQuery("#test_test_processor_override_mode").is(':checked');
	var shouldShowTKDetails = jQuery("#test_test_processor_use_key_mode").is(':checked');
	var shouldShowPKDetails = jQuery("#test_production_processor_use_key_mode").is(':checked');
	jQuery("#test_test_processor_always_override_details").toggle(shouldShowOverrideDetails);
	jQuery("#test_test_processor_use_key_details").toggle(shouldShowTKDetails);
	jQuery("#test_test_processor_use_production_key_details").toggle(shouldShowPKDetails);
}

function generateRandomKeyForSelector()
{
	var keyLength = 12; //key length
	var randomAlphaNum = Array(keyLength+1).join((Math.random().toString(36)+'00000000000000000').slice(2, 18)).slice(0, keyLength);
	return randomAlphaNum;
}


jQuery(document).ready(function() {
	jQuery(".test_test_processor_mode_radio").change(testSetTestProcessorOptionsDisplayState);
	testSetTestProcessorOptionsDisplayState(); //configure initial state
	jQuery("#test_test_processor_override_key_generate").click(function(e) {
		e.preventDefault();
		var newKey = generateRandomKeyForSelector();
		jQuery("#test_test_processor_override_key").val(newKey);
		jQuery("#test_test_processor_override_key_example_key").html(newKey);
	});
	jQuery("#test_production_processor_override_key_generate").click(function(e) {
		e.preventDefault();
		var newKey = generateRandomKeyForSelector();
		jQuery("#test_production_processor_override_key").val(newKey);
		jQuery("#test_production_processor_override_key_example_key").html(newKey);
	});
	jQuery("#test_test_processor_override_key").change(function() {
		var newVal = jQuery("#test_test_processor_override_key").val();
		jQuery("#test_test_processor_override_key_example_key").html(newVal);
	});
	jQuery("#test_production_processor_override_key").change(function() {
		var newVal = jQuery("#test_production_processor_override_key").val();
		jQuery("#test_production_processor_override_key_example_key").html(newVal);
	});
});
</script>
<div style="padding:10px;">
	<div style="margin-top:5px; margin-bottom:10px;">
		<a href='http://support.membermouse.com/support/solutions/articles/9000020288-using-the-test-payment-service' target='_blank'>Need help configuring the Test Payment Service?</a>
	</div>
	
	<div id="test-account-info" style="margin-bottom:10px; margin-left:10px; ">
		<div>
			<?php echo MM_Utils::getIcon('flask', 'blue', '1.3em', '1px', 'Setup Test Data', "margin-right:3px;"); ?>
			<a href="<?php echo MM_ModuleUtils::getUrl(MM_ModuleUtils::getPage(), MM_MODULE_TEST_DATA); ?>" target="_blank">Configure Test Data</a>
		</div>
	</div>
	
	<div style="margin-bottom:10px;">
		<p><strong><em>Always On</em></strong></p>
		<input type='radio' value='<?php echo MM_TestPaymentService::$MODE_ALWAYS_OVERRIDE; ?>' <?php echo ($p->getMode() === MM_TestPaymentService::$MODE_ALWAYS_OVERRIDE)?"checked":""; ?> id='test_test_processor_override_mode' class='test_test_processor_mode_radio' name='payment_service[test][test_processor_mode]' />
		Use test payment service as the onsite payment method<br>
		<div id="test_test_processor_always_override_details" <?php echo ($p->getMode() === MM_TestPaymentService::$MODE_ALWAYS_OVERRIDE)?"style='display:none;'":"margin-bottom:10px;"; ?>>
			<p style="font-size:11px; margin-left:10px; padding-right:20px;">
					<?php echo MM_Utils::getInfoIcon("", ""); ?>
					With this option selected, the onsite payment method is overriden, and the test payment service is used.
			</p>
		</div>
			
		<p><strong><em>On Demand Testing</em></strong></p>
		<input type='radio' value='<?php echo MM_TestPaymentService::$MODE_OVERRIDE_WITH_KEY; ?>' <?php echo ($p->getMode() === MM_TestPaymentService::$MODE_OVERRIDE_WITH_KEY)?"checked":""; ?> id='test_test_processor_use_key_mode' class='test_test_processor_mode_radio' name='payment_service[test][test_processor_mode]' />
		Use test payment service as the onsite payment method when override key is used during checkout<br>
		<div id="test_test_processor_use_key_details" <?php echo ($p->getMode() === MM_TestPaymentService::$MODE_OVERRIDE_WITH_KEY)?"style='display:none;'":"margin-bottom:10px;"; ?>>		
			<p style="font-size:11px; margin-left:10px; padding-right:20px;">
				<?php echo MM_Utils::getInfoIcon("", ""); ?>
				When this option is selected, a special parameter can be added to checkout URLs to force the test payment service to override the selected onsite payment method. This allows for test orders to be 
				created on production sites without disrupting normal operations. The parameter name is <code>pso</code> and its value has to match the value entered below: 
			</p>
			<p style="margin-left:10px; font-size:11px;">
				<em>Override Key</em> <br/>
				
				<span style="font-family:courier;">
					<input type='text' value='<?php echo $p->getTestProcessorOverrideKey(); ?>' id='test_test_processor_override_key' name='payment_service[test][test_processor_override_key]' style='width: 175px;' />
					<input type='button' class="mm-ui-button blue" value='Generate' id='test_test_processor_override_key_generate'>
				</span>
			</p>
			<p style="font-size:11px; margin-left:10px; padding-right:20px;">
				<em>Example On Demand Test Link</em> <br/>
				
				<span style="font-family:courier; font-size:11px;">
					<?php echo MM_TestPaymentService::getExampleCheckoutLink();?>&<span style='font-weight: bold;'>pso=<span id='test_test_processor_override_key_example_key'><?php echo ($p->getTestProcessorOverrideKey() !== "")?$p->getTestProcessorOverrideKey():"yourkey"; ?></span></span>
				</span>
			</p>
		</div>
		
		<!-- 
		<input type='radio' value='<?php echo MM_TestPaymentService::$MODE_PRODUCTION_OVERRIDE_WITH_KEY; ?>' <?php echo ($p->getMode() === MM_TestPaymentService::$MODE_PRODUCTION_OVERRIDE_WITH_KEY)?"checked":""; ?> id='test_production_processor_use_key_mode' class='test_test_processor_mode_radio' name='payment_service[test][test_processor_mode]' disabled />
		[<strong>Coming soon</strong>] <em><span style='color:#666;'>Use test mode of onsite/offsite payment method when override key is used during checkout</span></em><br>
		<div id="test_test_processor_use_production_key_details" <?php echo ($p->getMode() === MM_TestPaymentService::$MODE_PRODUCTION_OVERRIDE_WITH_KEY)?"style='display:none;'":"margin-bottom:10px;"; ?>>		
			
			<p style="font-size:11px; margin-left:10px; padding-right:20px;">
				<?php echo MM_Utils::getInfoIcon("", ""); ?>
				When this option is selected, a special parameter can be added to checkout URLs to force the order to be processed using the test mode of the selected onsite/offsite method (if test mode is supported). This allows for test orders to be 
				created and processed through a particular payment method on production sites without disrupting normal operations. The parameter name is <code>pso</code> and its value has to match the value entered below: 
			</p>
			<p style="margin-left:10px; font-size:11px;">
				<em>Override Key</em> <br/>
				
				<span style="font-family:courier;">
					<input type='text' value='<?php echo $p->getProductionProcessorOverrideKey(); ?>' id='test_production_processor_override_key' name='payment_service[test][production_processor_override_key]' style='width: 175px;' />
					<input type='button' class="mm-ui-button blue" value='Generate' id='test_production_processor_override_key_generate'>
				</span>
			</p>
			<p style="font-size:11px; margin-left:10px; padding-right:20px;">
				<em>Example On Demand Test Link</em> <br/>
				
				<span style="font-family:courier; font-size:11px;">
					<?php echo MM_TestPaymentService::getExampleCheckoutLink();?>&<span style='font-weight: bold;'>pso=<span id='test_production_processor_override_key_example_key'><?php echo ($p->getProductionProcessorOverrideKey() !== "")?$p->getProductionProcessorOverrideKey():"yourkey"; ?></span></span>
				</span>
			</p>
		</div>
		 -->
	</div>
</div>