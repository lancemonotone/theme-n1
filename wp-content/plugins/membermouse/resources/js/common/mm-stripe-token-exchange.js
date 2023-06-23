var MM_StripeTokenExchanger = Class.extend({
	
	pageObject: {},
	myAccount: false,
	
	doTokenExchange: function()
	{
		try
		{ 
			/*
			 * Check to see that a coupon with free type is not used.
			 * If it is, no need to send information (or initiate tokenization process).
			 */
			if((mmStripeTokenExchanger.myAccount == false) && (mmStripeTokenExchanger.pageObject.hasFreeCoupon()))
			{ 
				mmStripeTokenExchanger.pageObject.usingTokenExchange = false;
				mmStripeTokenExchanger.pageObject.submitCheckoutForm(false);
				return true;
			}
			
			var tokenParameters = { number: jQuery('#mm_field_cc_number').val(),
					  				cvc: jQuery('#mm_field_cc_cvv').val(),
					  				exp_month: jQuery('#mm_field_cc_exp_month').val(),
					  				exp_year: jQuery('#mm_field_cc_exp_year').val(),
					  				name: jQuery('#mm_field_first_name').val() + ' ' + jQuery('#mm_field_last_name').val()};
			
			//Add the address fields that are present in the form
			var optionalAddressFieldMapping = {"mm_field_billing_address":"address_line1",
											   "mm_field_billing_city":"address_city",
											   "mm_field_billing_state":"address_state",
											   "mm_field_billing_zip":"address_zip",
											   "mm_field_billing_country":"address_country"
											  };
			for (var optionalFormField in optionalAddressFieldMapping)
			{
				if (jQuery("#" + optionalFormField).length)
				{
					var tmpIndex = optionalAddressFieldMapping[optionalFormField];
					tokenParameters[tmpIndex] = jQuery("#" + optionalFormField).val();
				}
			}
			
			Stripe.setPublishableKey(stripeJSInfo.stripePublishableKey);
			Stripe.card.createToken(tokenParameters, mmStripeTokenExchanger.stripeResponseHandler);
		}
		catch (e)
		{
			mmStripeTokenExchanger.errorHandler(e.message);
		}
		return false; //prevents the form submission, we will do that ourselves when the token exchange is completed
	},
	
	errorHandler: function(errorMessage)
	{
		mmjs.isAlreadySubmitting = false;
		if (jQuery.fn.block)
		{
			jQuery.unblockUI({ fadeIn:0, timeout:0, onUnblock: () => {alert(errorMessage);}});
		}
		else
		{
			alert(errorMessage);
		}
	},
	
	stripeResponseHandler: function(status, response) 
	{
		  if (response.error) 
		  {
				// token exchange failed read the preupdate callback
				if (mmStripeTokenExchanger.myAccount)
		    	{ 
		    		myaccount_js.addPreUpdateCallback('onsite',mmStripeTokenExchanger.doTokenExchange);
		    	}
	    		else{ 
		    		mmjs.addPrecheckoutCallback('onsite',mmStripeTokenExchanger.doTokenExchange); 
	    		}

			  // Show the errors on the form
			  var errorMessage = (response.error.message)?(response.error.message):(stripeJSInfo.improperStripeResponseErrorMsg);
			  mmStripeTokenExchanger.errorHandler(errorMessage);
			  return false;
		  } 
		  else 
		  {
			  //response from Stripe.js contains 'id' and 'card' which contains additional card details
			  if (mmStripeTokenExchanger.myAccount)
			  {
				  mmStripeTokenExchanger.pageObject.addPaymentTokenToForm(response.id);
				  mmStripeTokenExchanger.pageObject.usingTokenExchange = true;
				  mmStripeTokenExchanger.pageObject.doSubscriptionBillingUpdate(); 
			  }
			  else
		      {
				  mmStripeTokenExchanger.pageObject.addPaymentTokenToForm(response.id);
				  mmStripeTokenExchanger.pageObject.usingTokenExchange = true;
				  mmStripeTokenExchanger.pageObject.submitCheckoutForm(false);
		      }
			  return true;
		  }
	}
});
var mmStripeTokenExchanger = new MM_StripeTokenExchanger();
if ((typeof myaccount_js !== 'undefined') && (myaccount_js instanceof MM_MyAccountView))
{
	//my account page
	mmStripeTokenExchanger.myAccount = true;
	mmStripeTokenExchanger.pageObject = myaccount_js;
	myaccount_js.addPreUpdateCallback('onsite',mmStripeTokenExchanger.doTokenExchange);
}
else
{
	//checkout page
	mmStripeTokenExchanger.pageObject = mmjs;
	mmjs.addPrecheckoutCallback('onsite',mmStripeTokenExchanger.doTokenExchange);
}