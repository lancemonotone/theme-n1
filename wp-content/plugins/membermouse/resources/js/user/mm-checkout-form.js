/*!
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_CheckoutView = MM_Core.extend({
 
	usingTokenExchange : false,
	maskCVV : true,
	useFreeCoupon : false,
	usePaymentTokenField : false,
	
    preCheckoutCallbacks : [],
    submitDependency: null,
	
	tokenSuccessfullyExchanged: false,
	
	/*
	 * This leverages feedback from the  
	 * applyCouponCallback event handler.  If any other 
	 * process mirrors that of the stripe tokenization they 
	 * can use this method to handle free coupons.
	 */
	hasFreeCoupon: function (){
		return this.useFreeCoupon;
	},
	
    formatMoney: function(number, places, symbol, thousand, decimal) {
        ci = MemberMouseGlobal.currencyInfo;
        currencyParams = {
            "places": "frac_digits",
            "symbol": "currency_symbol",
            "thousand": "mon_thousands_sep",
            "decimal": "mon_decimal_point",
            "symbol_space" : "p_sep_by_space"
        };
        number = number || 0;
        places = !isNaN(parseInt(Math.abs(places))) ? parseInt(places) : parseInt(ci[currencyParams['places']]);
        symbol = symbol !== undefined ? symbol : ci[currencyParams['symbol']];
        thousand = thousand || ci[currencyParams['thousand']];
        decimal = decimal || ci[currencyParams['decimal']];
        var symbolSpace = parseInt(ci[currencyParams['symbol_space']]);
        symbolSpace = !isNaN(symbolSpace) ? symbolSpace : 0;
        
        var negative = number < 0 ? "-" : "",
            i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        var retval = negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
        var symbolSpaceString = (symbolSpace > 0) ? " " : "";
        retval = (ci.p_cs_precedes == "1") ? (symbol + symbolSpaceString + retval) : (retval + symbolSpaceString + symbol); //position the symbol correctly based on the currency info
        retval = (ci.postfixIso == 1) ? (retval + " " + ci.currency) : retval; //postfix iso currency code if selected
        return retval;
    },
    unformatMoney: function(formattedNumber) {
        if (formattedNumber == undefined) {
            return 0.00;
        }
        unformattedNumber = formattedNumber.replace(/&[^;]*;/g, ""); //remove html entities
        //thousands separator is always superfluos
        var reThou = new RegExp(mmjs.regexEscape(ci[currencyParams['thousand']]), "g");
        unformattedNumber = unformattedNumber.replace(reThou, "");
        //replace decimal seperator with the decimal point if necessary
        if (ci[currencyParams['decimal']] != ".") {
            var reDec = new RegExp(mmjs.regexEscape(ci[currencyParams['decimal']]), "g");
            unformattedNumber = unformattedNumber.replace(reDec, ".");
        }
        return unformattedNumber.replace(/[^0-9-.]/g, "");
    },
    regexEscape: function(text) {
        return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
    },
    ltrim: function(str) {
        if (str != undefined) {
            return str.replace(/^\s+/, "");
        } else {
            return "";
        }
    },
    checkoutx: function(serviceToken, doSubmit) {  
        var isFree = (parseInt(jQuery("#mm_is_free").val()) == 1) ? true : false;
        if (isFree == true) {
            mmjs.checkout(doSubmit, "");
            return;
        } else {
            jQuery("#mm_field_payment_service").val(serviceToken);
            mmjs.checkout(doSubmit, "mm_field_billing_address,mm_field_billing_city,mm_field_billing_state,mm_field_billing_zip,mm_field_billing_country," + "mm_field_shipping_address,mm_field_shipping_city,mm_field_shipping_state,mm_field_shipping_zip,mm_field_shipping_country," + "mm_field_cc_number,mm_field_cc_cvv");
            return;
        }
    },
    checkout: function(doSubmit, ignoreFields) {
        var standard_fields = new Array();
        var standard_fields_label = new Array();
        var standard_fields_paid_only = new Array();
        var standard_fields_non_members_only = new Array();
        var standard_fields_optional = new Array();
        standard_fields[0] = 'mm_field_first_name';
        standard_fields_label[0] = 'first name';
        standard_fields_non_members_only[0] = '1';
        standard_fields_paid_only[0] = '0';
        standard_fields_optional[0] = '0';
        standard_fields[1] = 'mm_field_last_name';
        standard_fields_label[1] = 'last name';
        standard_fields_non_members_only[1] = '1';
        standard_fields_paid_only[1] = '0';
        standard_fields_optional[1] = '0';
        standard_fields[2] = 'mm_field_email';
        standard_fields_label[2] = 'email address';
        standard_fields_non_members_only[2] = '0';
        standard_fields_paid_only[2] = '0';
        standard_fields_optional[2] = '0';
        standard_fields[3] = 'mm_field_email_confirm';
        standard_fields_label[3] = 'confirmation email address';
        standard_fields_non_members_only[3] = '1';
        standard_fields_paid_only[3] = '0';
        standard_fields_optional[3] = '1';
        standard_fields[4] = 'mm_field_password';
        standard_fields_label[4] = 'password';
        standard_fields_non_members_only[4] = '1';
        standard_fields_paid_only[4] = '0';
        standard_fields_optional[4] = '0';
        standard_fields[5] = 'mm_field_phone';
        standard_fields_label[5] = 'phone number';
        standard_fields_non_members_only[5] = '1';
        standard_fields_paid_only[5] = '0';
        standard_fields_optional[5] = '0';
        standard_fields[6] = 'mm_field_billing_address';
        standard_fields_label[6] = 'billing address';
        standard_fields_non_members_only[6] = '0';
        standard_fields_paid_only[6] = '1';
        standard_fields_optional[6] = '0';
        standard_fields[7] = 'mm_field_billing_city';
        standard_fields_label[7] = 'billing city';
        standard_fields_non_members_only[7] = '0';
        standard_fields_paid_only[7] = '1';
        standard_fields_optional[7] = '0';
        standard_fields[8] = 'mm_field_billing_state';
        standard_fields_label[8] = 'billing state';
        standard_fields_non_members_only[8] = '0';
        standard_fields_paid_only[8] = '1';
        standard_fields_optional[8] = '0';
        standard_fields[9] = 'mm_field_billing_zip';
        standard_fields_label[9] = 'billing zip code or postal code';
        standard_fields_non_members_only[9] = '0';
        standard_fields_paid_only[9] = '1';
        standard_fields_optional[9] = '0';
        standard_fields[10] = 'mm_field_billing_country';
        standard_fields_label[10] = 'billing country';
        standard_fields_non_members_only[10] = '0';
        standard_fields_paid_only[10] = '1';
        standard_fields_optional[10] = '0';
        standard_fields[11] = 'mm_field_shipping_address';
        standard_fields_label[11] = 'shipping address';
        standard_fields_non_members_only[11] = '0';
        standard_fields_paid_only[11] = '1';
        standard_fields_optional[11] = '0';
        standard_fields[12] = 'mm_field_shipping_city';
        standard_fields_label[12] = 'shipping city';
        standard_fields_non_members_only[12] = '0';
        standard_fields_paid_only[12] = '1';
        standard_fields_optional[12] = '0';
        standard_fields[13] = 'mm_field_shipping_state';
        standard_fields_label[13] = 'shipping state';
        standard_fields_non_members_only[13] = '0';
        standard_fields_paid_only[13] = '1';
        standard_fields_optional[13] = '0';
        standard_fields[14] = 'mm_field_shipping_zip';
        standard_fields_label[14] = 'shipping zip code or postal code';
        standard_fields_non_members_only[14] = '0';
        standard_fields_paid_only[14] = '1';
        standard_fields_optional[14] = '0';
        standard_fields[15] = 'mm_field_shipping_country';
        standard_fields_label[15] = 'shipping country';
        standard_fields_non_members_only[15] = '0';
        standard_fields_paid_only[15] = '1';
        standard_fields_optional[15] = '0';
        standard_fields[16] = 'mm_field_cc_number';
        standard_fields_label[16] = 'credit card number';
        standard_fields_non_members_only[16] = '0';
        standard_fields_paid_only[16] = '1';
        standard_fields_optional[16] = '0';
        standard_fields[17] = 'mm_field_cc_cvv';
        standard_fields_label[17] = 'security code';
        standard_fields_non_members_only[17] = '0';
        standard_fields_paid_only[17] = '1';
        standard_fields_optional[17] = '0';
        standard_fields[18] = 'mm_field_username';
        standard_fields_label[18] = 'username';
        standard_fields_non_members_only[18] = '1';
        standard_fields_paid_only[18] = '0';
        standard_fields_optional[18] = '1';
        if (jQuery('#hasFormSubmitted').val() != '') {
            var prevPost = new Date(jQuery('#hasFormSubmitted').val());
            prevPost.setSeconds(prevPost.getSeconds() + 3);
            var currentTime = new Date();
            if (prevPost < currentTime) {
                // -- blank out the formsubmitted because user went back and forth and hidden values still cached
                jQuery('#hasFormSubmitted').val("");
            }
        }
        if (ignoreFields == null || ignoreFields == "") {
            ignoreFields = "";
        }
        var ccReq = false;
        var crntField;
        var crntValue;
        var isAdmin = (parseInt(jQuery("#mm_is_admin").val()) == 1) ? true : false;
        var isCustomerSupportOrder = (parseInt(jQuery("#mm_is_customer_support_order").val()) == 1) ? true : false;
        var isMember = (parseInt(jQuery("#mm_is_member").val()) == 1) ? true : false;
        var isFree = (parseInt(jQuery("#mm_is_free").val()) == 1) ? true : false;
        var doComp = (parseInt(jQuery("#mm_do_comp").val()) == 1) ? true : false;
        
        var shippingSame = jQuery("#mm_field_billing_equals_shipping").val(); 
        
        
        var shippingReq = (parseInt(shippingSame) == 0) ? true : false;
        // validate standard fields 
        for (i = 0; i < standard_fields.length; i++) {
            // mark if credit card information is required
            if (standard_fields[i] == "mm_field_cc_number" && isFree == false && doComp == false && ignoreFields.indexOf(standard_fields[i]) == -1) {
                ccReq = true;
            }

            if((standard_fields[i] == "mm_field_cc_number" || standard_fields[i] == "mm_field_cc_cvv") && this.usePaymentTokenField)
	    	  {
	        	ccReq = false;
                continue;
	    	  }
            
            // skip shipping fields if shipping is not required
            if (standard_fields[i].indexOf("shipping") != -1 && shippingReq == false) {
                continue;
            } 
            
            // skip fields that are overridden or marked to be ignored
            if (jQuery("#" + standard_fields[i] + "_override").length > 0 || jQuery("#" + standard_fields[i] + "_optional").length > 0 || ignoreFields.indexOf(standard_fields[i]) != -1) {
                continue;
            }
            crntField = jQuery("#" + standard_fields[i]);
            
            // skip optional fields that aren't included in the form
            if (standard_fields_optional[i] == '1' && crntField.length == 0) {
                continue;
            }
            
            // if is not a member, check if the current field is only required for non-members
            if ((isMember == true && standard_fields_non_members_only[i] == '0' || isMember == false) && (isFree == true && standard_fields_paid_only[i] == '0' || isFree == false) && (doComp == true && standard_fields_paid_only[i] == '0' || doComp == false)) {
                crntValue = mmjs.ltrim(crntField.val());
                
                /**
                 * When dealing with the billing/shipping state, if the relative country 
                 * entered by the user is NOT the US, continue, it's not required
                 * 
                 * If the billing or shipping state doesn't exist, also do not require.
                 */
                var match;
                if(match = standard_fields[i].match(/^mm_field_(.*)_state$/)) { 
                  if(!jQuery("#" + standard_fields[i]).length){ 
                	continue;
                  }
                  else if(jQuery("#mm_field_"+match[1]+"_country").val() != "US") {
                    continue;
                  } 
                }
                
                /**
                 * Check for missing shipping fields if the product is a shippable product
                */
                if(jQuery("#mm_is_shippable").val() == "1" && standard_fields[i].match(/^mm_field_shipping_(.*)/) && !crntValue) {
						doSubmit = false;
                  alert('Please enter your ' + standard_fields_label[i]);
                  jQuery("#" + standard_fields[i]).focus();
                  return;
                }
                
                if ((crntValue == '') || (crntValue == ' ') || (crntField.val().length == 0) || (crntField.val() == null) || (crntField.val() == '')) {
                	doSubmit = false;
                  if (standard_fields[i] == "mm_field_email_confirm") {
                    alert('Please confirm your email address');
                  } else {
                    alert('Please enter your '+ standard_fields_label[i]);
                  }
                  jQuery("#" + standard_fields[i]).focus();
                  return;
                }
                
                // if validating confirm email address, check if confirm email address and email address match
                if (standard_fields[i] == "mm_field_email_confirm") {
                  emailField = jQuery("#mm_field_email");
                  confirmEmailField = jQuery("#" + standard_fields[i]);
                  emailFieldValue = mmjs.ltrim(emailField.val());
                  confirmEmailValue = mmjs.ltrim(confirmEmailField.val());
                  if (emailFieldValue != confirmEmailValue) {
						  doSubmit = false;
                    alert('The confirmation email must match your email address');
                    jQuery("#" + standard_fields[i]).focus();
                    return;
                  }
                }
            }
        }
        
        // validate custom fields
        var customFields = jQuery('input[id^="mm_custom"]'); //.serializeArray();  
 
        for (i = 0; i < customFields.length; i++) {  
            if (jQuery("#" + customFields[i].name + "_required").length > 0 && jQuery("#" + customFields[i].name + "_required").val() == "1") {
                crntValue = mmjs.ltrim(customFields[i].value); 
                // get the custom field type
                if (jQuery("#" + customFields[i].name + "_type").length > 0) {
                    fieldType = jQuery("#" + customFields[i].name + "_type").val();
                } else {
                    fieldType = "input";
                }
                if (fieldType == "checkbox") {
                    if (crntValue == "mm_cb_off") {
                        // the checkbox implementation always puts the checkbox after the off value with the same name
                        // so if the next field value equals mm_cb_on the checkbox is checked off, otherwise it's not
                        nextValue = "";
//                        if (customFields.length > (i + 1)) {
//                            nextValue = mmjs.ltrim(customFields[i + 1].value);
//                        } 
                        
                        /// new implementation where we use the specific helper field which
                        /// appears to be toggled on demand. 
                        /// The above implementation was not obtaining the right field value in
                        /// checkbox situations.
                        var helper = jQuery("#"+customFields[i].name+"_helper");
                        if(helper.length>0){
                        	nextValue = helper.val();
                        }
                        // end implementation
                        
                        if (nextValue != "mm_cb_on") {
									 doSubmit = false;
                            if (jQuery("#" + customFields[i].name + "_label").length > 0) {
                                alert('Please check off ' + jQuery("#" + customFields[i].name + "_label").val());
                            } else {
                                alert('This field is required');
                            }
                            jQuery("#" + customFields[i].name).focus();
                            return;
                        }
                    }
                } if (fieldType == "radio") { 
                	var radioObj = jQuery('input[name='+customFields[i].name+']:checked');
                	if(radioObj!=null && radioObj!=undefined){ 
                    	var selectedValue = radioObj.val(); 
                    	if ((selectedValue == null) || (selectedValue == undefined) || (selectedValue == '') || (selectedValue == ' ') || (selectedValue.length == 0) || (selectedValue == '')) {
                    		 doSubmit = false; 
    		                 alert('This field is required'); 
    		                 jQuery("#" + customFields[i].name).focus();
    		                 return;
                    	}	
                	}  
                }else {
                    if ((crntValue == '') || (crntValue == ' ') || (customFields[i].value.length == 0) || (customFields[i].value == null) || (customFields[i].value == '')) {
							   doSubmit = false;
                        if (jQuery("#" + customFields[i].name + "_label").length > 0) {
                            alert('Please enter your ' + jQuery("#" + customFields[i].name + "_label").val());
                        } else {
                            alert('This field is required');
                        }
                        jQuery("#" + customFields[i].name).focus();
                        return;
                    }
                }
            }
        }  
        
        if (ccReq && jQuery('#mm_field_cc_number').length > 0) {
            if (jQuery('#mm_field_cc_number').val().length < 13) {
					 doSubmit = false;
                alert('Invalid credit card number');
                return;
            }
        }
        
        if (isAdmin && isCustomerSupportOrder == false) {
				 doSubmit = false;
            var msg = "Checkout form validated successfully.\n\n";
            msg += "Checkout processing is disabled for administrators.\n\nTo test the entire checkout process, ";
            msg += "please log out or login with a test member account.";
            alert(msg);
            return void(0);
        } else 
        {

        	 if (doSubmit && (mmjs.callPreCheckoutFunctions(isFree))) 
             { 
        		 mmjs.submitCheckoutForm(isFree);
        		 return;
             } 
             else 
             {
             	return;
             }
        } 
    },
    submitCheckoutForm: function(isFree) 
    {
    	if (jQuery('#hasFormSubmitted').val() == '') 
    	{
    		//ensure token exchange has happened, if required
    		if (mmjs.usingTokenExchange && !mmjs.tokenSuccessfullyExchanged)
    		{
    			return false;
    		}

			if(mmjs.callPreCheckoutFunctions(isFree) === false) 
			{
				return false;
			}
			
            var d = new Date();
            jQuery('#hasFormSubmitted').val(d.toUTCString());
            document.charset = 'UTF-8';
        	if (isFree) 
        	{ 
                jQuery.blockUI({ message: MemberMouseGlobal.checkoutProcessingFreeMessage });
            } 
        	else 
        	{
                jQuery.blockUI({ message: MemberMouseGlobal.checkoutProcessingPaidMessage });
            }
            jQuery(".blockMsg").addClass(MemberMouseGlobal.checkoutProcessingMessageCSS);
                
            
            // Safari fix?
            var isSafari = navigator.userAgent.indexOf("Safari") > -1;
            if(isSafari)
            { 
                
            	setTimeout(function(){
                        if(mmjs.hasSubmitDependancy())
                        {
                            mmjs.callSubmitDependencyFunctions(isFree, document.mm_checkout_form);
                        }
                        else
                        {
                            document.mm_checkout_form.submit();
                        }
            		}, 500);
            }
            else
            {
                if(mmjs.hasSubmitDependancy())
                {
                    mmjs.callSubmitDependencyFunctions(isFree, document.mm_checkout_form);
                }
                else
                {
                    document.mm_checkout_form.submit();
                }
            }
        }
    },
    isValidCreditCard: function(type, ccnum) {
        if (type == "visa") {
            // Visa: length 16, prefix 4, dashes optional.
            var re = /^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/;
        } else if (type == "master") {
            // Mastercard: length 16, prefix 51-55, dashes optional.
            var re = /^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/;
        } else if (type == "discover") {
            // Discover: length 16, prefix 6011, dashes optional.
            var re = /^6011-?\d{4}-?\d{4}-?\d{4}$/;
        } else if (type == "amex") {
            // American Express: length 15, prefix 34 or 37.
            var re = /^3[4,7]\d{13}$/;
        }
        if (!re.test(ccnum)) return false;
        // Remove all dashes for the checksum checks to eliminate negative numbers
        ccnum = ccnum.split("-").join("");
        // Checksum ("Mod 10")
        // Add even digits in even length strings or odd digits in odd length strings.
        var checksum = 0;
        for (var i = (2 - (ccnum.length % 2)); i <= ccnum.length; i += 2) {
            checksum += parseInt(ccnum.charAt(i - 1));
        }
        // Analyze odd digits in even length strings or even digits in odd length strings.
        for (var i = (ccnum.length % 2) + 1; i < ccnum.length; i += 2) {
            var digit = parseInt(ccnum.charAt(i - 1)) * 2;
            if (digit < 10) {
                checksum += digit;
            } else {
                checksum += (digit - 9);
            }
        }
        if ((checksum % 10) == 0) {
            return true;
        } else {
            return false;
        }
    },
	 isValidEmail: function(str) {
		 var email = str.trim();
		 var ajax = new MM_Ajax(false, this.module, this.action, this.method);

		 var values = {
			 mm_action: "validateInput"
		 };

		values.input_type  = 'EMAIL';
		values.input_label = 'Email';
		values.input_value = email; 
		
		this.validated = false;
		if(email.length>0){
			ajax.async		= false;
			ajax.send(values, false, 'mmjs', 'validateInputCallback');
		}
		return this.validated;

	},
 
	isAndroidDevice:function(){ 
		  return /(android)/i.test(navigator.userAgent);
	},
	
    onlyNumbers: function(e, type) {
        var keynum;
        var keychar;
        var numcheck = /\d/;
        
        if (window.event) // IE
        {
            keynum = e.keyCode;
        } else if (e.which) // Netscape/Firefox/Opera
        {
            keynum = e.which;
        }
 
        /*
         * If browser is on Android devices, we will want to dynamically get the value
         * and test it for digits. Some devices  do not support key events and results in a 
         * 229 keycode.
         */ 
        if(mmjs.isAndroidDevice() &&
        		e.type == "keyup"){
    		var controlID = jQuery(e.target).attr("id");  
    	    if(controlID!=null && controlID.length>0){ 
	        	var str = jQuery("#"+controlID).val();
	        	var entry = str.substr(str.length-1);
	        	var isValidRet = numcheck.test(entry);
	        	if(!isValidRet){
	        		// bad entry, remove.
	        		var newEntry = str.substr(0, str.length-1);
	        		jQuery("#"+controlID).val(newEntry); 
	        	} 
	        	return isValidRet;
    	    }
        }
        else{
            keychar = String.fromCharCode(keynum); 
        } 

        switch (keynum) {
        case 8:
            //backspace
        case 9:
            //tab
        case 13:
            //enter
        case 35:
            //end
        case 36:
            //home
        case 37:
            //left arrow
        case 38:
            //right arrow
        case 39:
            //insert
        case 45:
            //delete
        case 46:
            //0
        case 48:
            //1
        case 49:
            //2
        case 50:
            //3
        case 51:
            //4
        case 52:
            //5
        case 54:
            //6
        case 55:
            //7
        case 56:
            //8
        case 57:
            //9
        case 96:
            //0
        case 97:
            //1
        case 98:
            //2
        case 99:
            //3
        case 100:
            //4
        case 101:
            //5
        case 102:
            //6
        case 103:
            //7
        case 104:
            //8
        case 105:
            //9
            result2 = true;
            break;
        case 109:
            // -
        case 189:
            // -
        case 48:
            // )
        case 57:
            // (
        case 32:
            // space
        case 191:
            // /
            if (type == 'phone') {
                result2 = true;
            } else { 
                result2 = false;
            }
            break;
        default:
            result2 = numcheck.test(keychar);
            break;
        } 
        return result2;
    },
    
    toggleShippingInfo: function() {
        if (jQuery("#mm_checkbox_billing_equals_shipping").length > 0) {
            if (jQuery("#mm_checkbox_billing_equals_shipping").is(":checked")) {
                jQuery("#mm-shipping-info-block").hide();
                jQuery("#mm_field_billing_equals_shipping").val("1");
            } else {
                jQuery("#mm-shipping-info-block").show();
                jQuery("#mm_field_billing_equals_shipping").val("0");
            }
        }
    },
    
    toggleGiftSection: function(doUpdateDiscount) {
        if (jQuery("#mm_checkbox_is_gift").length > 0) {
            if (jQuery("#mm_checkbox_is_gift").is(":checked")) {
                jQuery("#mm-gift-info-block").show();
                jQuery("#mm_is_gift").val("1");
            } else {
                jQuery("#mm-gift-info-block").hide();
                jQuery("#mm_is_gift").val("0");
            }

			// when something is marked as a gift we need to update the discount value
			// and order total to ensure it is correct. The applyCoupon function does 
			// all of this so we just reuse this function here.
			if(doUpdateDiscount && doUpdateDiscount == true)
			{
				mmjs.applyCoupon();
			}
        }
    },
    
    shippingMethodChangeHandler: function() {
        var methodPrice = mmjs.formatMoney(0.00);
        if (jQuery("#mm_is_shippable").val() == "1" && jQuery("#mm_field_shipping_method").length > 0) {
            var selectedMethod = jQuery("#mm_field_shipping_method option:selected").text();
            var startIndex = selectedMethod.lastIndexOf("(") + 1;
            var endIndex = selectedMethod.lastIndexOf(")");
            methodPrice = selectedMethod.substring(startIndex, endIndex);
        }
        jQuery("#mm_data_shipping_price").val(methodPrice);
        if (jQuery("#mm_label_shipping_price").length > 0) {
            jQuery("#mm_label_shipping_price").html(jQuery("#mm_data_shipping_price").val());
        }
        mmjs.updateOrderTotal();
    },
    discountChangeHandler: function(discount) {
        jQuery("#mm_data_discount").val(discount);
        if (jQuery("#mm_label_discount").length > 0) {
            jQuery("#mm_label_discount").html(jQuery("#mm_data_discount").val());
        }
        discountAmt = parseFloat(mmjs.unformatMoney(discount));
        if (isNaN(discountAmt)) {
            discountAmt = 0;
        }
        if (discountAmt > 0 || jQuery("#mm_label_coupon_success").is(":visible")) {
            jQuery('div.mm-discount-true').each(function(index) {
                jQuery(this).show();
            });
            jQuery('div.mm-discount-false').each(function(index) {
                jQuery(this).hide();
            });
        } else {
            jQuery('div.mm-discount-true').each(function(index) {
                jQuery(this).hide();
            });
            jQuery('div.mm-discount-false').each(function(index) {
                jQuery(this).show();
            });
        }
        mmjs.updateOrderTotal();
    },
    updateOrderTotal: function() {
        var productPrice = 0;
        var shippingPrice = 0;
        var discount = 0;
        var totalPrice = 0;
        if (jQuery("#mm_data_product_price").length > 0) {
            productPrice = parseFloat(mmjs.unformatMoney(jQuery("#mm_data_product_price").val()));
            if (isNaN(productPrice)) {
                productPrice = 0;
            }
        }
        if (jQuery("#mm_data_shipping_price").length > 0) {
            shippingPrice = parseFloat(mmjs.unformatMoney(jQuery("#mm_data_shipping_price").val()));
            if (isNaN(shippingPrice)) {
                shippingPrice = 0;
            }
        }
        if (jQuery("#mm_data_discount").length > 0) {
            discount = parseFloat(mmjs.unformatMoney(jQuery("#mm_data_discount").val()));
            if (isNaN(discount)) {
                discount = 0;
            }
        }
        totalPrice = (productPrice + shippingPrice) - discount;
        if (totalPrice < 0) {
            totalPrice = 0;
        }
        if (jQuery("#mm_label_total_price").length > 0) {
            jQuery("#mm_label_total_price").html(mmjs.formatMoney(totalPrice));
        }
    },
    couponCheck: function(evt) {
        var keynum;
        var keychar;
        var numcheck;
        if (window.event) // IE
        {
            keynum = evt.keyCode;
        } else if (evt.which) // Netscape/Firefox/Opera
        {
            keynum = evt.which;
        }
        keychar = String.fromCharCode(keynum);
        numcheck = /\d/;
        switch (keynum) {
        case 13:
            //enter
            evt.preventDefault();
            evt.stopPropagation();
            mmjs.applyCoupon();
            return false;
            break;
        default:
            return true;
            break;
        }
        return true;
    },
    applyCoupon: function() {
        var values = {};
        values.mm_action = "applyCoupon";
        values.product_price = parseFloat(mmjs.unformatMoney(jQuery("#mm_data_product_price").val()));
        if (jQuery("#mm_field_coupon_code").length > 0 && jQuery("#mm_field_coupon_code").val().length > 0 && jQuery("#mm_field_coupon_code").val() != "") {
            values.coupon_code = jQuery("#mm_field_coupon_code").val();
        } else {
            values.coupon_code = "";
        }
		if (jQuery("#mm_checkbox_is_gift").length > 0 && jQuery("#mm_checkbox_is_gift").is(":checked")) {
            values.is_gift = 1;
        } else {
            values.is_gift = 0;
        }
        values.product_id = jQuery("#mm_product_id").val();
        var ajax = new MM_Ajax('wp-admin/admin-ajax.php', this.module, this.action, this.method);
        ajax.useLoader = false;
        ajax.send(values, false, 'mmjs', 'applyCouponCallback');
    },
    applyCouponCallback: function(response) {
        // hide and clear labels
    	this.useFreeCoupon = false;
        jQuery("#mm_label_coupon_success").hide();
        jQuery("#mm_label_coupon_success").html("");
        jQuery("#mm_label_coupon_error").hide();
        jQuery("#mm_label_coupon_error").html("");
        jQuery("#mm_do_comp").val("0");
        if (response.message != undefined) {
            var result = jQuery.parseJSON(response.message);
            if (result.isError == "1") {
                jQuery("#mm_label_coupon_error").html(result.message);
                jQuery("#mm_label_coupon_error").show();
                mmjs.discountChangeHandler(mmjs.formatMoney(result.discount));
            } else if (result.message != "") {
                jQuery("#mm_label_coupon_success").html(result.message);
                jQuery("#mm_label_coupon_success").show();
            }
            if (result.discount == "free") {
                // calculate order total (product price plus shipping cost)
                var orderTotal = 0;
                var productPrice;
                var shippingPrice;
                if (jQuery("#mm_data_product_price").length > 0) {
                    productPrice = parseFloat(mmjs.unformatMoney(jQuery("#mm_data_product_price").val()));
                    if (!isNaN(productPrice)) {
                        orderTotal += productPrice;
                    }
                }
                if (jQuery("#mm_data_shipping_price").length > 0) {
                    shippingPrice = parseFloat(mmjs.unformatMoney(jQuery("#mm_data_shipping_price").val()));
                    if (!isNaN(shippingPrice)) {
                        orderTotal += shippingPrice;
                    }
                }
                mmjs.discountChangeHandler(mmjs.formatMoney(orderTotal));
                jQuery("#mm_do_comp").val("1");
                this.useFreeCoupon = true;
            } else {
                mmjs.discountChangeHandler(mmjs.formatMoney(result.discount));
            }
        } else {
            jQuery("#mm_label_coupon_error").html("Invalid coupon code");
            jQuery("#mm_label_coupon_error").show();
            mmjs.discountChangeHandler(mmjs.formatMoney(result.discount));
        }
    },
    
	captchaTokenRequest: function(submitObj)
    {
		var siteKey = jQuery("#mm_grsk").val();
    	if ((siteKey.length == 0) || (siteKey == ""))
    	{
			return false;
		}
		var actionName = jQuery("#mm_graction").val();
    	if ((actionName.length == 0) || (actionName == ""))
    	{
			return false;
		}
		
        grecaptcha.ready(function() {
          grecaptcha.execute(siteKey, {action: actionName}).then(function(token) {
            jQuery("#mm_grtok").val(token); 
            if(submitObj!=undefined && submitObj!=null)
            {
                submitObj.submit();   
            }
            else
            {
                alert("Invalid form object for submission via the captcha token request.");
            }
          });
        }); 
        return true;
    },

    hasSubmitDependancy: function()
    {
        return (this.submitDependency != null);
    },
    

    /**
     * This will take the submit dependency and dynamically call the function and pass the form object
     * as a parameter.
     * 
     * @param {*} isFree 
     * @param {*} submitObj 
     */
    callSubmitDependencyFunctions: function(isFree, submitObj)
    {
    	var serviceToken = jQuery("#mm_field_payment_service").val();
		var callbackInfo;
    	if ((serviceToken.length == 0) || (serviceToken == ""))
    	{
    		serviceToken = "onsite";
    	}
    	
		callbackInfo = this.submitDependency;
        if ((callbackInfo != null) && (typeof callbackInfo == 'object') && (callbackInfo.serviceToken) && (callbackInfo.callback))
        {
            if((!isFree && (serviceToken == callbackInfo.serviceToken)) || (callbackInfo.serviceToken == "captcha"))
            {
                if (callbackInfo.callback(submitObj) === false)
                { 
                    console.log("Submit dependency failed in callback.");
                    alert("Unable to complete call cycle. Please check your input and try again.");
                    return;
                }
            }
        }
    },  
    
    callPreCheckoutFunctions: function(isFree)
    {
    	var serviceToken = jQuery("#mm_field_payment_service").val();
		var callbackInfo;
    	if ((serviceToken.length == 0) || (serviceToken == ""))
    	{
    		serviceToken = "onsite";
    	}
    	
		while(callbackInfo = this.preCheckoutCallbacks.shift())
		{
            if ((callbackInfo != null) && (typeof callbackInfo == 'object') && (callbackInfo.serviceToken) && (callbackInfo.callback))
            {
				if((!isFree && (serviceToken == callbackInfo.serviceToken)) || (callbackInfo.serviceToken == "captcha"))
				{
	            	if (callbackInfo.callback() === false)
	            	{
	            		return false;
	            	}
				}
            }
        }
    	return true;
    },  

    /** 
     * You can add one asynchronous function as a submission dependency such that it'll wait for the callback
     * prior to submitting the form in question.
     * 
     * @param {*} serviceToken 
     * @param {*} callback 
     */
    addSubmitDependency : function(serviceToken, callback) 
    {
    	if ((serviceToken !== undefined) && (serviceToken != "") && (callback !== undefined) && (typeof callback == 'function' || false))
    	{
    		this.submitDependency = {'serviceToken':serviceToken,'callback':callback};
    	}
    },
    
    
    addPrecheckoutCallback : function(serviceToken, callback) 
    {
    	if ((serviceToken !== undefined) && (serviceToken != "") && (callback !== undefined) && (typeof callback == 'function' || false))
    	{
    		this.preCheckoutCallbacks.push({'serviceToken':serviceToken,'callback':callback});
    	}
    },
     
    addPaymentTokenToForm: function(paymentToken) 
    {
    	if ((paymentToken !== undefined) && (paymentToken.length > 0))
    	{
    		if (jQuery('#mm_field_payment_token').length == 0)
    		{
	    		var ccField = jQuery('#mm_field_cc_number');
	    		if(this.usePaymentTokenField)
	    		{   
	    			jQuery("#mm_checkout_form").append("<input type='hidden' name='mm_field_payment_token' id='mm_field_payment_token' value='" + paymentToken + "'>");
	    		}
	    		else if (ccField.length > 0)
	    		{
	    			jQuery(ccField[0].form).append("<input type='hidden' name='mm_field_payment_token' id='mm_field_payment_token' value='" + paymentToken + "'>");
	    		}
	    		else 
	    		{
	    			jQuery('#mm_field_payment_token').val(paymentToken);
	    		}
	    		
	    		
	    		if(!this.usePaymentTokenField)
	    		{ 
		    		//mask the cc number
		    		var ccVal = ccField.val();
		    		var ccLen = ccVal.length;
		    		var maskLen = (ccLen>4)?(ccLen-4):ccLen;
		    		var maskedVal = Array(maskLen+1).join("*");
		    		if (ccLen != maskLen)
		    		{
		    			maskedVal += ccVal.substring(maskLen,ccLen);
		    		}
		    		jQuery('#mm_field_cc_number').val(maskedVal);
	    		
		    		//some payment services require CVV To be sent unmasked to the server to enable CVV handling
		    		if (this.maskCVV)
		    		{
			    		var cvvLen = jQuery('#mm_field_cc_cvv').val().length;
			    		if (cvvLen > 0)
			    		{
			    			var maskedCVV = Array(cvvLen+1).join("*");
			    			jQuery('#mm_field_cc_cvv').val(maskedCVV);
			    		}
		    		}
	    		} 
	    		
	    		this.tokenSuccessfullyExchanged = true;
    		}
    	}
    }
    
});
var mmjs = new MM_CheckoutView("MM_CheckoutView", "Checkout");
jQuery(document).ready(function() {
    jQuery.blockUI.defaults.css = {};
    var field = 'mm-checkout-preview';
    var url = window.location.href;
    if ((url.indexOf('?' + field + '=') != -1) || (url.indexOf('&' + field + '=') != -1)) {
        jQuery.blockUI({
            message: MemberMouseGlobal.checkoutProcessingPaidMessage
        });
        jQuery(".blockMsg").addClass(MemberMouseGlobal.checkoutProcessingMessageCSS);
    }
}); 