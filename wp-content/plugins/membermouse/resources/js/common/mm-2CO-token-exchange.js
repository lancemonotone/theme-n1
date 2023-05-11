var MM_2COTokenExchanger = Class.extend({

  	errorCallback: function(data) 
	{
  		if (data.errorCode === 200) {
      	// This error code indicates that the ajax call failed. We recommend that you retry the token request.
      	alert(data.errorMsg);
    	} else {
      	alert(data.errorMsg);
    	}

		// readd precheckout callback
		mmjs.addPrecheckoutCallback('onsite',mm2COTokenExchanger.doTokenExchange);
		
    	return false;
	},
  	
	successCallback: function(data) 
	{
  		mmjs.usingTokenExchange = true;
	  	mmjs.addPaymentTokenToForm(data.response.token.token);
	  	document.mm_checkout_form.submit();
  		return false;
	},
	
	doTokenExchange: function()
	{	
  		TCO.loadPubKey(_2COJSInfo.environment, function(){
      	var args = {
        		sellerId: _2COJSInfo._2COSellerId,
				publishableKey: _2COJSInfo._2COPublicKey,
				ccNo: jQuery('#mm_field_cc_number').val(),
				cvv: jQuery('#mm_field_cc_cvv').val(),
				expMonth: jQuery('#mm_field_cc_exp_month').val(),
				expYear: jQuery('#mm_field_cc_exp_year').val()
      	};

      	// Make the token request
      	TCO.requestToken(mm2COTokenExchanger.successCallback, mm2COTokenExchanger.errorCallback, args);	
      	return false;
  		});
  		
		return false;
	}
	
});
var mm2COTokenExchanger = new MM_2COTokenExchanger();
mmjs.addPrecheckoutCallback('onsite',mm2COTokenExchanger.doTokenExchange);