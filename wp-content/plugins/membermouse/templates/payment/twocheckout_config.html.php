<script language="Javascript">
  
  	function _2COTestModeChangeHandler(object)
	{
		switch(jQuery(object).is(':checked'))
    	{
      	case true:
        		jQuery('.live').hide();
        		jQuery('.sandbox').show();
        		jQuery('#two-checkout-test-account-info').show();
        		break;  
      	case false:
      	default:
        		jQuery('.live').show();
        		jQuery('.sandbox').hide();
        		jQuery('#two-checkout-test-account-info').hide();
      		break;
    	}
	}
  
  	function show2COTestCardNumbers()
	{
	  	var str = "";
	  	str += "You can use the following test credit card numbers when testing payments.\n";
	  	str += "The expiration date must be set to the present date or later:\n\n";
	  	str += "|| For a Successful Transaction:\n";
	  	str += "   Card #: 4000000000000002 OR 4222222222222220\n";
	  	str += "   CVV Code: 123\n\n";
	  	str += "|| For a Declined Transaction:\n";
	  	str += "   Card #: 4333433343334333\n";
	  	str += "   CVV Code: 123\n";
		/*
	  	str += "To test fraud wait and failed status, use the buyer information below:\n";
	  	str += "- Name: Joe Flagster\n";
	  	str += "- Street Address: 123 Main Street\n";
	  	str += "- City: Townsville\n";
	  	str += "- State: Ohio\n";
	  	str += "- Zip: 43206\n";
	  	str += "- Country: USA\n";
		*/
	  	alert(str);
  	}
  
	jQuery(document).ready(function(){
   	_2COTestModeChangeHandler(jQuery("#twocheckout_test_mode"));
  	});
	
</script>
<div style="padding:10px;">
	<img src='https://membermouse.com/assets/plugin_images/logos/2checkout.png' />
	
  	<div style="margin-top:5px; margin-bottom:10px;">
    	<a href='#' target='_blank'>Need help configuring 2Checkout?</a>
  	</div>
  	
  	<div style="margin-bottom:10px;">
  		<input type='checkbox' value='true' <?php echo (($p->getTestMode()==true)?"checked":""); ?> id='twocheckout_test_mode' name='payment_service[twocheckout][test_mode]' onChange="_2COTestModeChangeHandler(this)" />
  		<label for="twocheckout_test_mode">Enable Test Mode</label>
  	</div>
  
  	<div id="two-checkout-test-account-info" style="margin-bottom:10px; margin-left:10px; <?php echo (($p->getTestMode()==true)?"":"display:none;"); ?>">
  		<div style="margin-bottom:5px;"> 
			<?php echo MM_Utils::getIcon('external-link', 'blue', '1.2em', '2px', '', "margin-right:3px;"); ?>
			<a href="https://sandbox.2checkout.com/sandbox/signup" target="_blank">Sign Up for a Sandbox Account</a>
		</div>
		<div style="margin-bottom:5px;">
			<?php echo MM_Utils::getIcon('external-link', 'blue', '1.2em', '2px', '', "margin-right:3px;"); ?> 
			<a href="https://sandbox.2checkout.com/sandbox/" target="_blank">Log Into the Sandbox Account</a>
		</div>
		<div style="margin-bottom:5px;">
  			<?php echo MM_Utils::getIcon('credit-card', 'blue', '1.3em', '1px', "Test Credit Card Numbers", "margin-right:3px;"); ?>
  			<a href="javascript:show2COTestCardNumbers()">Test Credit Card Info</a>
  		</div>
		<div>
			<?php echo MM_Utils::getIcon('flask', 'blue', '1.3em', '1px', 'Setup Test Data', "margin-right:3px;"); ?>
			<a href="<?php echo MM_ModuleUtils::getUrl(MM_ModuleUtils::getPage(), MM_MODULE_TEST_DATA); ?>" target="_blank">Configure Test Data</a>
		</div>
  	</div>
	<div class="sandbox">
    	<div style="margin-bottom:10px;width:48%;float:left;">
    		<span id="twocheckout-seller-id-label">Account Number</span>    	
    		<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getSellerId('test'); ?>' id='twocheckout_seller_id' name='payment_service[twocheckout][test][seller_id]' style='width: 100%;' tabindex='1' />
    		</p>
    	</div>
   	<div style="margin-bottom:10px;margin-left:4%;width:48%;float:left;">
			<span id="twocheckout-secret-word-label">Secret Word</span>    	
    		<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getSecretWord('test'); ?>' id='twocheckout_secret_word' name='payment_service[twocheckout][test][secret_word]' style='width: 100%;' tabindex='6' />
    		</p>
     	</div>
 	 	<div style="clear:both;"></div>
    	<div style="margin-bottom:10px;width:48%;float:left;">
    		<span id="twocheckout-public-key-label">API Publishable Key</span>
    		<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getPublicKey('test'); ?>' id='twocheckout_public_key' name='payment_service[twocheckout][test][public_key]' style='width: 100%;' tabindex='3' />
    		</p>
    	</div>
     	<div style="margin-bottom:5px;margin-left:4%;width:48%;float:left;">
     		<span id="twocheckout-private-key-label">API Private Key</span>
			<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getPrivateKey('test'); ?>' id='twocheckout_private_key' name='payment_service[twocheckout][test][private_key]' style='width: 100%;' tabindex='2' />
    		</p>
     	</div>
 	 	<div style="clear:both;"></div>
    	<div style="margin-bottom:10px;width:48%;float:left;">
    		<span id="twocheckout-username-label">API Access Username</span>    	
     		<p style="margin-left:10px; font-family:courier; font-size:11px;">
   			<input type='text' value='<?php echo $p->getUsername('test', 'api'); ?>' id='twocheckout_api_username' name='payment_service[twocheckout][test][api][username]' style='width: 100%;' tabindex='4' />
     		</p>
    	</div>
    	<div style="margin-bottom:10px;margin-left:4%;width:48%;float:left;">
     		<span id="twocheckout-password-label">API Access Password</span>
     		<p style="margin-left:10px; font-family:courier; font-size:11px;">
     			<input type='text' value='<?php echo $p->getPassword('test', 'api'); ?>' id='twocheckout_api_password' name='payment_service[twocheckout][test][api][password]' style='width: 100%;' tabindex='5' />
     		</p>
    	</div>
	</div>
  	<div class="live">
    	<div style="margin-bottom:10px;width:48%;float:left;">
    		<span id="twocheckout-seller-id-label">Account Number</span>    	
    		<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getSellerId('live'); ?>' id='twocheckout_seller_id' name='payment_service[twocheckout][live][seller_id]' style='width: 100%;' tabindex='7' />
    		</p>
    	</div>
   		<div style="margin-bottom:10px;margin-left:4%;width:48%;float:left;">
    		<span id="twocheckout-secret-word-label">Secret Word</span>    	
    		<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getSecretWord('live'); ?>' id='twocheckout_secret_word' name='payment_service[twocheckout][live][secret_word]' style='width: 100%;' tabindex='8' />
    		</p>
     	</div>
 	 	<div style="clear:both;"></div>
    	<div style="margin-bottom:10px;width:48%;float:left;">
    		<span id="twocheckout-public-key-label">API Publishable Key</span>
    		<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getPublicKey('live'); ?>' id='twocheckout_public_key' name='payment_service[twocheckout][live][public_key]' style='width: 100%;' tabindex='5' />
    		</p>
    	</div>
     	<div style="margin-bottom:5px;margin-left:4%;width:48%;float:left;">
    		<span id="twocheckout-private-key-label">API Private Key</span>
			<p style="margin-left:10px; font-family:courier; font-size:11px;">
    			<input type='text' value='<?php echo $p->getPrivateKey('live'); ?>' id='twocheckout_private_key' name='payment_service[twocheckout][live][private_key]' style='width: 100%;' tabindex='6' />
    		</p>
     	</div>
		<div style="clear:both;"></div>
    	<div style="margin-bottom:10px;width:48%;float:left;">
			<span id="twocheckout-username-label">API Access Username</span>    	
     		<p style="margin-left:10px; font-family:courier; font-size:11px;">
   			<input type='text' value='<?php echo $p->getUsername('live', 'api'); ?>' id='twocheckout_api_username' name='payment_service[twocheckout][live][api][username]' style='width: 100%;' tabindex='3' />
     		</p>
    	</div>
    	<div style="margin-bottom:10px;margin-left:4%;width:48%;float:left;">
     		<span id="twocheckout-password-label">API Access Password</span>
     		<p style="margin-left:10px; font-family:courier; font-size:11px;">
     			<input type='text' value='<?php echo $p->getPassword('live', 'api'); ?>' id='twocheckout_api_password' name='payment_service[twocheckout][live][api][password]' style='width: 100%;' tabindex='4' />
     		</p>
    	</div>
  	</div>
  
	<div style="clear:both;margin-bottom:10px;">
		<hr />
	</div>
	<div style="margin-bottom:10px;">
		<span id="twocheckout-secret-word-label">Instant Notification URL</span>    	
		<p style="margin-left:10px; font-family:courier; font-size:11px;">
			<input id="mm-2co-ipn-url" type='text' value='<?php echo MM_PLUGIN_URL."/x.php?service=TWOCHECKOUT"; ?>'  style='width: 100%;background-color:#fff;' tabindex='7' readonly onclick="jQuery('#mm-2co-ipn-url').focus(); jQuery('#mm-2co-ipn-url').select();" />
		</p>
		
	  	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
		  	<?php echo MM_Utils::getInfoIcon("", ""); ?>
		  	2Checkout uses silent posts to inform 3rd party systems when events happen within 2Checkout
		  	such as successful payments, subscription cancellations, etc. MemberMouse keeps member accounts in sync with 2Checkout by listening 
		  	for these notifications. In order for this to work, you must 
		  	<a href='https://www.2checkout.com/documentation/notifications/' target='_blank'>register the notification URL above with 2Checkout</a>.
		</p>
		
		
	</div>
	<div style="clear:both;margin-bottom:10px;"></div>
  
</div>
