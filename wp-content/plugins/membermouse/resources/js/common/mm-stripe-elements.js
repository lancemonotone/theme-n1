/* This class initializes Stripe Elements, parses custom css rules for Element fields, and is 
 * responsible for PaymentIntent management and payment authentication 
 */

/* IE polyfill for javascript startsWith function */
if (!String.prototype.startsWith) 
{
    Object.defineProperty(String.prototype, 'startsWith', {
        value: function(search, rawPos) 
		{
            var pos = rawPos > 0 ? rawPos|0 : 0;
            return this.substring(pos, pos + search.length) === search;
        }
    });
}

/**
 * 
 * MM_StripeElements is a support class that: 
 * * provides token exchange services
 * * controls configuration/display of Elements payment fields
 * * manages Stripe PaymentIntents/SetupIntents and handles pre-purchase authentication
 * 
 * @namespace MemberMouse Plugin
 * @name  MM_StripeElements
 * @class MM_StripeElements stripe elements support class
 */

class MM_StripeElements
{ 
	
	/**
 	 * MM_StripeElements constructor
	 */
	constructor() 
	{
		this.myAccount = false;
		this.pageObject = {};
		this.stripe = {};
		this.elements = {}; 
		this.card = {};
		this.cardExpiry = {};
		this.cardCvc = {};
		this.activeIntent = false;
	}
	
	parseCss(text) 
	{
		let tokenizer = /([\s\S]+?)\{([\s\S]*?)\}/gi;
		let	rules = [], rule, token, style;
		text = text.replace(/\/\*[\s\S]*?\*\//g, '');
		while ( (token=tokenizer.exec(text)) ) 
		{
			style = mmStripeElements.parseRule(token[2].trim());
			style.cssText = mmStripeElements.stringifyRule(style);
			rule = {
				selectorText : token[1].trim().replace(/\s*\,\s*/, ', '),
				style : style
			};
			rule.cssText = rule.selectorText + ' { ' + rule.style.cssText + ' }';
			rules.push(rule);
		}
		return rules;
	}
	
	
	parseRule(css) 
	{
		let tokenizer = /\s*([a-z\-]+)\s*:\s*((?:[^;]*url\(.*?\)[^;]*|[^;]*)*)\s*(?:;|$)/gi, obj = {}, token;
		while (token=tokenizer.exec(css)) 
		{
			obj[token[1]] = token[2];
		}
		return obj;
	}
	
	
	stringifyRule(style) 
	{
		let text = '';
		let keys = Object.keys(style).sort();
		for (let i=0; i<keys.length; i++) 
		{
			text += ' ' + keys[i] + ': ' + style[keys[i]] + ';';
		}
		return text.substring(1);
	}
	
	
	parseStyleRulesIntoElements(rules, matchingStr, myMMReferenceVar)
	{
		//TODO: use filter/map here
		if(myMMReferenceVar == null)
		{
			myMMReferenceVar = {};
		}

		if ((rules != undefined) && (rules != null))
		{
			if (rules.selectorText == matchingStr)
			{   
				for (var eachvar in rules.style)
				{
					if (eachvar != "cssText")
					{   
						if (myMMReferenceVar[eachvar] == undefined)
						{ 
							myMMReferenceVar[eachvar] = rules.style[eachvar]; 
						}
					}
				}
			}
		}
	}
	
	
	/*
	 * Replace css elements from valid 
	 */
	translateCssForStripe(css)
	{
		/*
		 * Stripe style elements:
		 * fontFamily, fontSize, fontSmoothing, fontStyle, fontVariant, fontWeight, iconColor, 
		 * lineHeight, letterSpacing, textAlign, textDecoration, textShadow, and textTransform
		 */ 
		var replaceArr = [];
		replaceArr["font-size"] = "fontSize";
		replaceArr["font-family"] = "fontFamily";
		replaceArr["font-smoothing"] = "fontSmoothing";
		replaceArr["font-style"] = "fontStyle";
		replaceArr["font-variant"] = "fontVariant";
		replaceArr["font-weight"] = "fontWeight";
		replaceArr["icon-color"] = "iconColor";
		replaceArr["line-height"] = "lineHeight";
		replaceArr["letter-spacing"] = "letterSpacing";
		replaceArr["text-align"] = "textAlign";
		replaceArr["text-decoration"] = "textDecoration";
		replaceArr["text-shadow"] = "textShadow";
		replaceArr["text-transform"] = "textTransform"; 
		for(var eachvar in replaceArr)
		{
    		css = css.replace(eachvar, replaceArr[eachvar]);
		}	
		return css;
	}
	
	
	getCustomStyleAndPlaceholder(divName)
	{  
		var divStr = "#"+divName+" .mm-stripe-elements-container"; 
		var divBaseStr = "#"+divName+" .mm-stripe-elements-container .base"; 
		var divInvalidStr = "#"+divName+" .mm-stripe-elements-container .invalid"; 
		var divEmptyStr = "#"+divName+" .mm-stripe-elements-container .empty"; 
		var divCompleteStr = "#"+divName+" .mm-stripe-elements-container .complete"; 

		var mmStripeElementsBaseStyle = {placeholder:''};
		var mmStripeElementsInvalidStyle = {};
		var mmStripeElementsEmptyStyle = {};
		var mmStripeElementsCompleteStyle = {};

		let rules = [];  
		var cssText = "";
		var foundStyleSheet = false;
		jQuery.each(document.styleSheets, function(sheetIndex, sheet) {
			try
			{
				if(sheet.cssRules!=undefined && sheet.cssRules!=null)
				{ 
				    jQuery.each(sheet.cssRules || sheet.rules, function(ruleIndex, rule) {
				        var css = rule.cssText;
				        if(css != undefined && css != null && css.length>0)
				        {
				        	if(css.includes(divStr) || 
				        			css.includes(divBaseStr) || 
				        			css.includes(divInvalidStr) || 
				        			css.includes(divEmptyStr) || 
				        			css.includes(divCompleteStr)
				        	)
				        	{
				        		css = mmStripeElements.translateCssForStripe(css); 
				        		cssText += css;
				        	}
				        }
				    });
				}
			}
			catch(ex)
			{
				console.log("MemberMouse :: Loading style sheet exception :: "+ex.message);
			}
		});
		
		if(cssText.length>0)
		{
			rules = mmStripeElements.parseCss(cssText);
			foundStyleSheet = true;
		}
		
		if(!foundStyleSheet)
		{
			if(jQuery('style[id="mm-stripe-elements-definitions"]').length)
			{ 	 
				rules = mmStripeElements.parseCss(jQuery('style').text());
			}
		}

		for (let i=0; i<rules.length; i++)
		{    
			// generic style definition for given div
			mmStripeElements.parseStyleRulesIntoElements(rules[i], divStr, mmStripeElementsBaseStyle); 
			
			// more specific base definition
			mmStripeElements.parseStyleRulesIntoElements(rules[i], divBaseStr, mmStripeElementsBaseStyle); 

			// specific invalid definition
			mmStripeElements.parseStyleRulesIntoElements(rules[i], divInvalidStr, mmStripeElementsInvalidStyle); 

			// specific empty definition
			mmStripeElements.parseStyleRulesIntoElements(rules[i], divEmptyStr, mmStripeElementsEmptyStyle);
			
			// specific complete definition
			mmStripeElements.parseStyleRulesIntoElements(rules[i], divCompleteStr, mmStripeElementsCompleteStyle);			 
		}   
		
		var placeholder = "";
		if(mmStripeElementsBaseStyle["content"]!=undefined && mmStripeElementsBaseStyle["content"]!=null)
		{
			placeholder = mmStripeElementsBaseStyle["content"].replace(/\"/g,"");
		}
		
		delete mmStripeElementsBaseStyle["content"];
		var styleForDiv = {
				"placeholder": placeholder,
			    "style": {
			        "base": mmStripeElementsBaseStyle 
			    }
			};
		if(mmStripeElementsInvalidStyle!=null)
		{
			styleForDiv["style"]["invalid"] = mmStripeElementsInvalidStyle;
		}
		if(mmStripeElementsEmptyStyle!=null)
		{
			styleForDiv["style"]["empty"] = mmStripeElementsEmptyStyle;
		} 
		if(mmStripeElementsCompleteStyle!=null)
		{
			styleForDiv["style"]["complete"] = mmStripeElementsCompleteStyle;
		} 
		return styleForDiv;   
	}
	
	load()
	{
		this.stripe = Stripe(stripeElementsInfo.stripePublishableKey); 
		
		this.elements = this.stripe.elements();  
		
		this.card = this.elements.create('cardNumber', mmStripeElements.getCustomStyleAndPlaceholder("mm_field_cc_number_div"));  
		this.card.mount('#mm_field_cc_number_div');
		
		this.cardExpiry = this.elements.create('cardExpiry', mmStripeElements.getCustomStyleAndPlaceholder("mm_field_cc_exp_div"));
		this.cardExpiry.mount('#mm_field_cc_exp_div');
		 
		this.cardCvc = this.elements.create('cardCvc', mmStripeElements.getCustomStyleAndPlaceholder("mm_field_cc_cvv_div"));
		this.cardCvc.mount('#mm_field_cc_cvv_div');   
		
		// Handle real-time validation errors from the card Element.
		this.card.addEventListener('change', function(event) {
			var displayError = document.getElementById('card-errors');
			if ((displayError != undefined) && (displayError != null))
			{ 
				if (event.error) 
				{
					displayError.textContent = event.error.message;
				} 
				else 
				{
					displayError.textContent = '';
				}
			}
			else
			{
				if (event.error) 
				{
					//TODO: only show this if debug is set
					//console.log("Error with Stripe Elements: ");
					//console.log(event.error);
					//mmStripeElements.errorHandler(event.error.message);
				}
			}
		});
	}
	
	
	errorHandler(errorMessage)
	{ 
		mmjs.isAlreadySubmitting = false;
		mmStripeElements.card.update({disabled: false});
		mmStripeElements.cardExpiry.update({disabled: false});
		mmStripeElements.cardCvc.update({disabled: false});
		
		if (jQuery.fn.block)
		{
			jQuery.unblockUI({ fadeIn:0, timeout:0, onUnblock: () => {alert(errorMessage);}});
		}
		else
		{
			alert(errorMessage);
		}
	}
	
	
	doTokenExchange()
	{     
		var cardholderName = "";
		
		var paymentMethodData = {};
		var billingDetailsData = {};
		var billingDetailsAddressData = {};
		
		var optionalBillingFieldMapping = {"mm_field_billing_address":"line1",
					   "mm_field_billing_address2":"line2",
					   "mm_field_billing_city":"city",
					   "mm_field_billing_state":"state",
					   "mm_field_billing_zip":"postal_code",
					   "mm_field_billing_country":"country",
					   "mm_field_email":"email",
					   "mm_field_phone":"phone"};
		
		mmStripeElements.card.update({disabled: true});
		mmStripeElements.cardExpiry.update({disabled: true});
		mmStripeElements.cardCvc.update({disabled: true});
		
		if (!mmStripeElements.myAccount)
	    { 
			optionalBillingFieldMapping["mm_field_first_name"] = "firstname";
			optionalBillingFieldMapping["mm_field_last_name"] = "lastname";
	    }
		else
		{
			optionalBillingFieldMapping["mm-data-first-name"] = "firstname";
			optionalBillingFieldMapping["mm-data-last-name"] = "lastname";
		}
		
		for (var optionalFormField in optionalBillingFieldMapping)
		{
			if (jQuery("#" + optionalFormField).length)
			{
				let tmpIndex = optionalBillingFieldMapping[optionalFormField];
				
				/// Note: Since javascript object assignment is by reference, any changes to targetObj are reflected in billingDetailsData or billingDetailsAddressData accordingly.
				let targetObj = (optionalFormField.startsWith("mm_field_billing_")) ? billingDetailsAddressData : billingDetailsData;
				if (optionalFormField.startsWith("mm-data-"))
				{
					if(jQuery("#" + optionalFormField).text().trim() != '')
					{
						targetObj[tmpIndex] = jQuery("#" + optionalFormField).text().trim();
					}
				}
				else
				{
					if(jQuery("#" + optionalFormField).val().trim() != '')
					{
						targetObj[tmpIndex] = jQuery("#" + optionalFormField).val().trim();
					}
				}
			}
		}
		
		if ('firstname' in billingDetailsData)
		{
			cardholderName = billingDetailsData.firstname;
			delete billingDetailsData.firstname;
		}
		
		if ('lastname' in billingDetailsData)
		{
			if (cardholderName !== "")
			{
				cardholderName += " ";
			}
			cardholderName += billingDetailsData.lastname;
			delete billingDetailsData.lastname;
		}
		
		if (cardholderName !== "")
		{
			billingDetailsData.name = cardholderName;
		}
		
		if (Object.keys(billingDetailsAddressData).length !== 0)
		{
			billingDetailsData.address = billingDetailsAddressData;
		}
		
		if (Object.keys(billingDetailsData).length !== 0)
		{
			paymentMethodData.billing_details = billingDetailsData;
		}

		paymentMethodData.card = mmStripeElements.card;

		if (mmStripeElements.myAccount != true)
		{
			//checkout page
			mmStripeElements.validateCaptcha(paymentMethodData);
			return false;
		}
		else
		{
			//my account page
			mmStripeElements.confirmIntent(paymentMethodData);
			return false; 
		}
	}
	
	
	validateCaptcha(paymentMethodData)
	{
		//TODO: verify captcha works if placed on myaccount page. Also verify this method passes through if no captcha is present for myaccount
		
		//Captcha must be validated prior to calling confirm on the paymentintent, so captcha verification is temporarily reimplemented here
		if ((mmStripeElements.pageObject.submitDependency != null) && 
			("serviceToken" in mmStripeElements.pageObject.submitDependency) && 
			(mmStripeElements.pageObject.submitDependency.serviceToken == "captcha"))
		{
			let siteKey = jQuery("#mm_grsk").val();
			let actionName = jQuery("#mm_graction").val();
	    	if (((siteKey.length == 0) || (siteKey == "")) || ((actionName.length == 0) || (actionName == "")))
	    	{
				return false;
			}
			
	        grecaptcha.ready(function() {
	          grecaptcha.execute(siteKey, {action: actionName}).then(function(token) {
	            jQuery("#mm_grtok").val(token); 
	            mmStripeElements.validateCheckout(paymentMethodData);
	          });
	        });
	        return false;
		}
		else
		{
			return mmStripeElements.validateCheckout(paymentMethodData);
		}
	}
	
	
	validateCheckout(paymentMethodData)
	{
		let data = {
					action : 'mm_stripe_validate_checkout',
					data: jQuery(document.mm_checkout_form).serialize(),
					'_ajax_nonce': stripeElementsInfo.stripeAjaxNonce
				   };

		jQuery.ajax({
					type: "POST",
				    url: MemberMouseGlobal.ajaxurl,
				    data: data,
				    dataType: 'json'
				}).done(function(response) {
				if ((response.type) && (response.type == "success"))
				{
					if ((mmStripeElements.pageObject.submitDependency != null) && 
						("serviceToken" in mmStripeElements.pageObject.submitDependency) && 
						(mmStripeElements.pageObject.submitDependency.serviceToken == "captcha"))
					{
						//captcha is in use, but it will have already been validated, so remove the submitDependency to avoid retesting and generating a new token
						mmStripeElements.pageObject.submitDependency = null;
					}
					mmStripeElements.activeIntent = response.message;
					mmStripeElements.confirmIntent(paymentMethodData);					
				}
				else
				{
					let errMsg = (response.message) ? response.message : "There was an error processing your payment, please try again";
					console.log("AJAX call to server to validate checkout returned an error, aborting...");
					mmStripeElements.restoreCallback();
					mmStripeElements.errorHandler(errMsg);
				}
			}).fail(function(result) {
				console.log("AJAX call to server to validate checkout failed, aborting...");
				mmStripeElements.restoreCallback();
				mmStripeElements.errorHandler("There was a network error when processing your payment, please try again");
			});
		return false;
	}


	confirmIntent(paymentMethodData)
	{
		if ((typeof paymentMethodData != 'object') || (paymentMethodData == null))
		{
			console.log("Incorrect data passed to confirmIntent method, aborting...");
			mmStripeElements.restoreCallback();
			mmStripeElements.errorHandler("There was an error processing your payment, please try again");
			return false;
		}

		if (mmStripeElements.activeIntent.type == 'setupintent')
		{
			mmStripeElements.stripe.confirmCardSetup(mmStripeElements.activeIntent.clientSecret, {
				payment_method: paymentMethodData,
			})
			.then(function(result) {
				mmStripeElements.postCardConfirm(result);
			});
		}
		else if (mmStripeElements.activeIntent.type == 'paymentintent')
		{
			mmStripeElements.stripe.confirmCardPayment(mmStripeElements.activeIntent.clientSecret, {
				payment_method: paymentMethodData,
			})
			.then(function(result) {
				mmStripeElements.postCardConfirm(result);
			});
		}
		else
		{
			console.log("Intent has unknown type, not sure how to confirm it, aborting...");
			mmStripeElements.restoreCallback();
			mmStripeElements.errorHandler("There was an error processing your payment, please try again");
		}
	}
	
	
	postCardConfirm(result)
	{
		if (result.error) 
		{  
			// token exchange failed
			mmStripeElements.restoreCallback();
			
			//display the error message and return false to halt the payment process
			mmStripeElements.errorHandler(result.error.message);
			return false
	  	} 
		else 
		{
			let pmToken = ('setupIntent' in result) ?  `si||${result.setupIntent.id}` : `pi||${result.paymentIntent.id}`;
		  	if (mmStripeElements.myAccount)
		    {
		    	mmStripeElements.pageObject.addPaymentTokenToForm(pmToken);  
	    		mmStripeElements.pageObject.doSubscriptionBillingUpdate(); 
		    }
	    	else
			{ 
		    	mmStripeElements.pageObject.addPaymentTokenToForm(pmToken);  
		    	mmStripeElements.pageObject.submitCheckoutForm(false); 
	    	}
	    	return true;
	    }
	}
	
	
	restoreCallback()
	{
		if (mmStripeElements.myAccount)
		{
			mmStripeElements.pageObject.addPreUpdateCallback('onsite',mmStripeElements.doTokenExchange);
		}
		else
		{
			//checkout page
			mmStripeElements.pageObject.addPrecheckoutCallback('onsite',mmStripeElements.doTokenExchange);
		}
	}
}


var mmStripeElements = new MM_StripeElements();
mmStripeElements.myAccount = ((typeof myaccount_js !== 'undefined') && (myaccount_js instanceof MM_MyAccountView)); //My Account page setup is slightly different from checkout pages
mmStripeElements.pageObject = (mmStripeElements.myAccount) ? myaccount_js : mmjs;
mmStripeElements.pageObject.usePaymentTokenField = true;


if (mmStripeElements.myAccount)
{
	mmStripeElements.pageObject.addPreUpdateCallback('onsite',mmStripeElements.doTokenExchange); 
	jQuery( ".mm-update-subscription-button" ).on( "form:loaded", function( event ) { 
		mmStripeElements.load(); 
    });
}
else
{
	mmStripeElements.pageObject.addPrecheckoutCallback('onsite',mmStripeElements.doTokenExchange); 
	jQuery(document).ready(function() 
	{
		mmStripeElements.load();
	}); 
}