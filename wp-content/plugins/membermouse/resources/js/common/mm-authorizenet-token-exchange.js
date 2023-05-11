var MM_AuthorizenetTokenExchanger = Class.extend({
	
	pageObject: {},
	myAccount: false,
	
	doTokenExchange: function()
	{
		try
		{ 
			// Check to see that a coupon with free type is not used if this is checkout. 
			// If it is, no need to send information (or initiate tokenization process).
			if((mmAuthorizenetTokenExchanger.myAccount == false) && (mmAuthorizenetTokenExchanger.pageObject.hasFreeCoupon()))
			{ 
				mmAuthorizenetTokenExchanger.pageObject.usingTokenExchange = false;
				mmAuthorizenetTokenExchanger.pageObject.submitCheckoutForm(false);
				return true;
			}
			
			var secureData = {}, authData = {}, cardData = {};
			cardData.cardNumber = jQuery('#mm_field_cc_number').val();
			cardData.month = jQuery('#mm_field_cc_exp_month').val();
			cardData.year = jQuery('#mm_field_cc_exp_year').val();
			cardData.cardCode = jQuery('#mm_field_cc_cvv').val();
			if (jQuery('#mm_field_billing_zip').length > 0)
			{
				cardData.zip = jQuery('#mm_field_billing_zip').val();
			}
			secureData.cardData = cardData;
			
			authData.clientKey = authorizenetJSInfo.authnetPublicClientKey;
			authData.apiLoginID = authorizenetJSInfo.apiLoginID;
			secureData.authData = authData;
			
			Accept.dispatchData(secureData, "authnetResponseHandler");
		}
		catch (e)
		{
			mmAuthorizenetTokenExchanger.errorHandler(e.message);
		}
		return false; //prevents the form submission, we will do that ourselves when the token exchange is completed
	},
	
	errorHandler: function(errorMessage)
	{
		//for now, alert the message
		alert(errorMessage);
	},
	
	authnetResponseHandler: function(response) 
	{
		if (response.messages.resultCode === 'Error') 
		{
	        for (var i = 0; i < response.messages.message.length; i++) 
	        {
	        	mmAuthorizenetTokenExchanger.errorHandler(response.messages.message[i].code + ':' + response.messages.message[i].text);
	        }
	    } 
		else 
		{
			if ((response.opaqueData == undefined) ||
				(response.opaqueData.dataDescriptor == undefined) ||
				(response.opaqueData.dataValue == undefined)) 
			{
						        // token exchange failed readd the preupdate callback
					        if (mmAuthorizenetTokenExchanger.myAccount)
		        {
		        	myaccount_js.addPreUpdateCallback('onsite',mmAuthorizenetTokenExchanger.doTokenExchange);
		        }
		        else
		        {
		        	mmjs.addPrecheckoutCallback('onsite',mmAuthorizenetTokenExchanger.doTokenExchange);
		        }

				//the fields required to do a token-exchange are not present, display a generic error message
				mmAuthorizenetTokenExchanger.errorHandler(authorizenetJSInfo.improperAuthnetCIMResponseErrorMsg);
				return false;
			}
	        var compositeToken = response.opaqueData.dataDescriptor + "|" + response.opaqueData.dataValue
	       
	        if (mmAuthorizenetTokenExchanger.myAccount)
	        {
	        	mmAuthorizenetTokenExchanger.pageObject.addPaymentTokenToForm(compositeToken);
	        	mmAuthorizenetTokenExchanger.pageObject.usingTokenExchange = true;
	        	mmAuthorizenetTokenExchanger.pageObject.doSubscriptionBillingUpdate();
	        }
	        else
	        {
	        	mmAuthorizenetTokenExchanger.pageObject.addPaymentTokenToForm(compositeToken);
	        	mmAuthorizenetTokenExchanger.pageObject.usingTokenExchange = true;
	        	mmAuthorizenetTokenExchanger.pageObject.submitCheckoutForm(false);
	        }
			return true;
	    }
	}
});
var mmAuthorizenetTokenExchanger = new MM_AuthorizenetTokenExchanger();
window['authnetResponseHandler'] = mmAuthorizenetTokenExchanger.authnetResponseHandler;
if ((typeof myaccount_js !== 'undefined') && (myaccount_js instanceof MM_MyAccountView))
{
	//my account page
	mmAuthorizenetTokenExchanger.myAccount = true;
	mmAuthorizenetTokenExchanger.pageObject = myaccount_js;
	myaccount_js.maskCVV = false;
	myaccount_js.addPreUpdateCallback('onsite',mmAuthorizenetTokenExchanger.doTokenExchange);
}
else
{
	//checkout page
	mmAuthorizenetTokenExchanger.pageObject = mmjs;
	mmjs.addPrecheckoutCallback('onsite',mmAuthorizenetTokenExchanger.doTokenExchange);
	mmjs.maskCVV = false;
}