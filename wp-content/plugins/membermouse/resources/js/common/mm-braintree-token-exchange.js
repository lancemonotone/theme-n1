
var MM_BraintreeTokenExchanger = Class.extend({
	
	pageObject: {},
	myAccount: false,
	hostedFieldsInstance: null,
	hostedFieldsClonedStyles: {},
	threeDSecure: null,
	
	getStyleObject: function(selector)
	{
		//Element.matches polyfill, if necessary
		if (!Element.prototype.matches) 
		{
		  Element.prototype.matches = Element.prototype.msMatchesSelector ||
		                              Element.prototype.webkitMatchesSelector;
		}
		//End Element.matches polyfill
		
		var source = document.querySelector(selector);
		var sheets = document.styleSheets;
		var outputStyles = {};

	    for(var i = 0; i < sheets.length; i++) 
	    {
	    	try
	    	{
		        var rules = sheets[i].cssRules || sheets[i].rules;   
		        for(var r = 0; r < rules.length; r++) 
		        {
		            var rule = rules[r];
		            var selectorText = rule.selectorText;
		            if ((source != null) && (source.matches(selectorText)))
		            {
				        for(var l = 0; l < rule.style.length; l++)
				        {
				            outputStyles[rule.style[l]] = rule.style[rule.style[l]];
				        }
		            }
		        }
	    	}
	    	catch (e)
	    	{
	    		//nothing to do, probably a securityerror
	    	}
	    }
	    return outputStyles;
	},
	
	initializeHostedFields: function() 
	{
		mmBraintreeTokenExchanger.hostedFieldsClonedStyles = {
            'input': 
            {
              'font-size': '14px'
            },
            'input.invalid': 
            {
              'color': 'red'
            },
            'input.valid': 
            {
              'color': 'green'
            }
         };
		var mm_checkout_braintree_field_map = { "#mm_field_cc_number": { newField:"mm_braintree_cc_number", cssRef:".number"},
									  			"#mm_field_cc_exp_month":{ newField:"mm_braintree_cc_exp_month", cssRef:".expirationMonth"},
									  			"#mm_field_cc_exp_year": { newField:"mm_braintree_cc_exp_year", cssRef:".expirationYear"},
									  			"#mm_field_cc_cvv": { newField:"mm_braintree_cc_cvv", cssRef:".cvv"} };
		for (var i in mm_checkout_braintree_field_map)
		{
			/* This code copied styles 
			var origStyles = mmBraintreeTokenExchanger.getStyleObject(i);
			if (Object.keys(origStyles).length)
			{
				mmBraintreeTokenExchanger.hostedFieldsClonedStyles[mm_checkout_braintree_field_map[i].cssRef] = origStyles;
			}
			*/
			
			/* this code modified previous forms
			if (jQuery(i).length)
			{
				jQuery(i).replaceWith("<div id='" + mm_checkout_braintree_field_map[i].newField +"' class='mm-braintree-hosted-field'></div>");
			}
			*/
		}
				
		braintree.client.create(
		{
			authorization: braintreeJSInfo.clientToken
		}, 
		function (clientErr, clientInstance) 
		{
			if (clientErr) 
			{
				//Error creating the braintree client, don't allow checkout
				console.error("Error creating Braintree javascript client: " + JSON.stringify(clientErr));
				mmBraintreeTokenExchanger.displayError("Error setting up payment, please contact a site administrator");
				return false;
			}
			
			braintree.hostedFields.create(
			{
	          client: clientInstance,
	          styles: mmBraintreeTokenExchanger.hostedFieldsClonedStyles,
	          fields: 
	          {
	        	  number: 
	        	  {
	        		  selector: '#mm_braintree_cc_number',
	        		  placeholder: ''
	        	  },
	        	  expirationMonth: 
	        	  {
	        	      selector: '#mm_braintree_cc_exp_month',
	        	      select: 
	        	      {
	        	        options: [
	        	          '(01) Jan',
	        	          '(02) Feb',
	        	          '(03) Mar',
	        	          '(04) Apr',
	        	          '(05) May',
	        	          '(06) Jun',
	        	          '(07) Jul',
	        	          '(08) Aug',
	        	          '(09) Sep',
	        	          '(10) Oct',
	        	          '(11) Nov',
	        	          '(12) Dec'
	        	        ]
	        	      }
	        	  },
	        	  expirationYear: 
	        	  {
	        	    selector: '#mm_braintree_cc_exp_year',
	        	    select: true
	        	  },
	        	  cvv: 
	        	  {
	        		  selector: '#mm_braintree_cc_cvv',
	        		  placeholder: ''
	        	  }
	          }
			}, 
			function (hostedFieldsErr, hostedFieldsInstance) 
			{
				if (hostedFieldsErr) 
				{
					//Error creating the hostedfields instance, don't allow checkout
					console.error("Error setting up hosted fields client:" + JSON.stringify(hostedFieldsErr));
					mmBraintreeTokenExchanger.displayError("Error setting up payment, please contact a site administrator");
					return false;
				}
				
				mmBraintreeTokenExchanger.hostedFieldsInstance = hostedFieldsInstance;
				
			}); //end create statement for hosted fields
			
			
			braintree.threeDSecure.create(
			{
				client: clientInstance,
				version: 2
			}, 
			function (threeDSecureErr, threeDSecureInstance) 
			{
				if (threeDSecureErr) 
				{
					//Error creating the 3D Secure 2 component instance, don't allow checkout
					console.error("Error setting up 3D Secure 2 client:" + JSON.stringify(threeDSecureErr));
					mmBraintreeTokenExchanger.displayError("Error setting up payment, please contact a site administrator");
					return false;
				}
				mmBraintreeTokenExchanger.threeDSecure = threeDSecureInstance;
			}); 
        }); 	
	},
	
	
	validateHostedFields: function(braintreeErr)
	{
		try 
		{
			var errMsg = '';
			if (('details' in braintreeErr) && ('invalidFieldKeys' in braintreeErr.details))
			{
				switch (braintreeErr.details.invalidFieldKeys[0])
				{
					case 'number':
						errMsg = "Please enter a valid credit card number";
						break;
					case 'expirationMonth':
						errMsg = "Please enter a valid credit card expiration date";
						break;
					case 'expirationYear':
						errMsg = "Please enter a valid credit card expiration date";
						break;
					case 'cvv':
						errMsg = "Please enter a valid cvv";
						break;
					default:
						errMsg = "An error was encountered while processing your payment, please reload the page and try again";
						break;
				}
				
				mmBraintreeTokenExchanger.displayError(errMsg);
			}
		}
		catch(e)
		{
			console.log("Error calling hosted fields tokenize():"+JSON.stringify(tokenizeErr));
		}
	},
	
	
	displayError: function(errMsg)
	{
		alert(errMsg);
	},
	
	
	doTokenExchange: function()
	{
		//return false if hosted fields and three3dsecure arent loaded
		if ((mmBraintreeTokenExchanger.hostedFieldsInstance === null) || (mmBraintreeTokenExchanger.threeDSecure === null))
		{
			//seems like multiple cases can lead to this (pressing submit before the components load, error in init, etc...
			console.log("Token-exchange cancelled, hosted fields and/or 3D Secure not loaded");
			return false;
		}
		
		mmBraintreeTokenExchanger.hostedFieldsInstance.tokenize(function (tokenizeErr, payload) 
		{ 
              if (tokenizeErr) 
              { 
            	  mmBraintreeTokenExchanger.validateHostedFields(tokenizeErr);
            	  return;
              }
              
              //3d secure verify the payment method
              var my3DSContainer = document.createElement('div');
               
              //TODO: move amounts and arithmetic into js
              var totalPrice = (!mmBraintreeTokenExchanger.myAccount)? mmjs.unformatMoney(jQuery("#mm_label_total_price").text()):1.00;
 
              mmBraintreeTokenExchanger.threeDSecure.verifyCard(
              {
                amount: totalPrice,
                nonce: payload.nonce,
                bin: payload.details.bin,
				challengeRequested: true,
                onLookupComplete: function (data, next) 
                { 
                  // use `data` here, then call `next()`. Useful for clearing a loading indicator before the 3ds modal pops up
                  next();
                }
              }, function (err, response) 
              { 
            	  if (err) 
            	  {
					  // token exchange failed readd the preupdate callback
					  if (mmBraintreeTokenExchanger.myAccount)
    				  {
    					  myaccount_js.addPreUpdateCallback('onsite',mmBraintreeTokenExchanger.doTokenExchange);
    				  }
    				  else
    				  {
    					  mmjs.addPrecheckoutCallback('onsite',mmBraintreeTokenExchanger.doTokenExchange);
    				  }
            		  mmBraintreeTokenExchanger.displayError("Please try again with another form of payment");
            		  return false;
            	  }
            	  
            	  //false values for "liabilityShifted" and "liabilityShiftPossible" indicate that the card is ineligible for 3DS... probably
                  if (response.liabilityShifted || !response.liabilityShifted && !response.liabilityShiftPossible) 
                  {
                	  //3DSecure success, do token exchange
    				  mmBraintreeTokenExchanger.pageObject.addPaymentTokenToForm(response.nonce);
    				  mmBraintreeTokenExchanger.pageObject.usingTokenExchange = true;
    				  if (mmBraintreeTokenExchanger.myAccount)
    				  {
    					  mmBraintreeTokenExchanger.pageObject.doSubscriptionBillingUpdate(); 
    				  }
    				  else
    				  {
    					  mmBraintreeTokenExchanger.pageObject.submitCheckoutForm(false); 
    				  }
                  } 
                  else 
                  {
                      mmBraintreeTokenExchanger.displayError("Please try again with another form of payment.");
                      return false;
                  } 
            	  
              });
               
        });
		return false; //prevents form submission. We will submit the form later, after verification
	}
});

var mmBraintreeTokenExchanger = new MM_BraintreeTokenExchanger();
 
if ((typeof myaccount_js !== 'undefined') && (myaccount_js instanceof MM_MyAccountView))
{
	//my account page
	mmBraintreeTokenExchanger.myAccount = true;
	mmBraintreeTokenExchanger.pageObject = myaccount_js;
	mmBraintreeTokenExchanger.pageObject.usePaymentTokenField = true;
	myaccount_js.addPreUpdateCallback('onsite',mmBraintreeTokenExchanger.doTokenExchange);
}
else
{
	//checkout page
	mmBraintreeTokenExchanger.pageObject = mmjs;
	mmBraintreeTokenExchanger.pageObject.usePaymentTokenField = true;
	mmjs.addPrecheckoutCallback('onsite',mmBraintreeTokenExchanger.doTokenExchange);
}


jQuery(document).ready(function(){
	//replace the checkout form inputs with hosted fields-ready divs  
	if(mmBraintreeTokenExchanger.myAccount)
	{ 
		jQuery( ".mm-update-subscription-button" ).on( "form:loaded", function( event ) { 
			mmBraintreeTokenExchanger.initializeHostedFields(); 
	    });
	}
	else
	{
		mmBraintreeTokenExchanger.initializeHostedFields(); 
	}
});