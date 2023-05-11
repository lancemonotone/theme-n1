/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_MyAccountView = MM_Core.extend({

	usingTokenExchange : false,
	usePaymentTokenField : false,
	maskCVV : true,

	
	preUpdateCallbacks : [],
	
	createDialogContainer: function(id)
	{
		if(jQuery("#"+id).length == 0)
		{
			jQuery("<div id='"+id+"'></div>").hide().appendTo("body").fadeIn();
		}
	},
	
	
	loadElements: function()
	{
		if(myaccount_js.usePaymentTokenField)
		{  
			jQuery('.mm-update-subscription-button').trigger( "form:loaded" );
		} 
	},
	
	updateAccountDetails: function(userId)
	{
		var dialogId = "mm-myaccount-account-details-dialog";
		this.createDialogContainer(dialogId);
		jQuery("#"+dialogId).dialog({
			autoOpen: false,
			close: function(event, ui){
				jQuery(this).dialog("destroy");
				jQuery(this).remove();
			},
		});
		var values =  {};
		values.mm_action = "showDialog";
		values.id = userId;
		values.dialogId = dialogId;
		
		mmdialog_js.showDialog(dialogId, this.module, 500, 600, "Update Account Details", values);
	},
	
	updateSubscriptionInfo: function(userId, orderItemId)
	{
		var dialogId = "mm-myaccount-subscription-info-dialog";
		this.createDialogContainer(dialogId);
		jQuery("#"+dialogId).dialog({
			autoOpen: false,
			close: function(event, ui){
				jQuery(this).dialog("destroy");
				jQuery(this).remove();
			},
		});
		var values =  {};
		values.mm_action = "showDialog";
		values.id = userId;
		values.orderItemId = orderItemId;
		values.dialogId = dialogId;
		
		mmdialog_js.showDialog(dialogId, this.module, 500, 425, "Update Billing Details", values);
	},
	
	updateBillingInfo: function(userId)
	{
		var dialogId = "mm-myaccount-billing-info-dialog";
		this.createDialogContainer(dialogId);
		jQuery("#"+dialogId).dialog({
			autoOpen: false,
			close: function(event, ui){
				jQuery(this).dialog("destroy");
				jQuery(this).remove();
			},
		});
		var values =  {};
		values.mm_action = "showDialog";
		values.id = userId;
		values.dialogId = dialogId;
		
		mmdialog_js.showDialog(dialogId, this.module, 500, 450, "Update Billing Address", values);
	},
	
	updateShippingInfo: function(userId)
	{
		var dialogId = "mm-myaccount-shipping-info-dialog";
		this.createDialogContainer(dialogId);
		jQuery("#"+dialogId).dialog({
			autoOpen: false,
			close: function(event, ui){
				jQuery(this).dialog("destroy");
				jQuery(this).remove();
			},
		});
		var values =  {};
		values.mm_action = "showDialog";
		values.id = userId;
		values.dialogId = dialogId;
		
		mmdialog_js.showDialog(dialogId, this.module, 500, 450, "Update Shipping Address", values);
	},
	
	updateMemberData: function(userId, dataType)
	{
		var doContinue = false;
		var form = new MM_Form("mm-form-container");
	    var values = form.getFields();
		values.userId = userId;
		
		// set checkout and radio buttons values by using helper fields
		jQuery.each(values, function(id, value) 
		{
			if(jQuery('#' + id + "_helper").length > 0)
			{
				values[id] = jQuery('#' + id + "_helper").val();
			}
		});
		
		switch(dataType) 
		{
			case "account-details":
				doContinue = this.validateAccountDetailsForm()
				values.mm_action = "updateAccountDetails";
				break;
			
			case "billing-info":
				doContinue = this.validateBillingInfoForm();
				values.mm_action = "updateBillingInfo";
				break;
			
			case "shipping-info":
				doContinue = true;
				values.mm_action = "updateShippingInfo";
				break;
		}
	    
		if(doContinue)
		{
			var ajax = new MM_Ajax(false, this.module, this.action, this.method);
			ajax.send(values, false, 'myaccount_js', "updateMemberDataCallback"); 
		}
	},
	
	updateMemberDataCallback: function(data)
	{
		if(data.type == "error")
		{
			alert(data.message);
			return;
		}
		else
		{
			myaccount_js.closeDialog();
			alert(data.message);
			document.location.reload();	
		}
	},
	
	validateAccountDetailsForm: function()
	{	
		if(jQuery('#mm_email').val() == "") 
		{
			alert("Please enter your email address");
			jQuery('#mm_email').focus();
			return false;
		}
	   
		if(!this.validateEmail(jQuery('#mm_email').val())) 
		{
			alert("Please enter a valid email address");
			jQuery('#mm_email').focus();
			return false;
		}
		
		if(jQuery('#mm_username').val() == "") 
		{
			alert("Please enter your username");
			jQuery('#mm_username').focus();
			return false;
		}
		
		if(jQuery('#mm_new_password').val() != "") 
		{
			if(jQuery('#mm_new_password_confirm').val() == "") 
			{
				alert("Please confirm your new password");
				jQuery('#mm_new_password_confirm').focus();
				return false;
			}
			
			if(jQuery('#mm_new_password').val() != jQuery('#mm_new_password_confirm').val()) 
			{
				alert("Your passwords don't match.");
				jQuery('#mm_new_password_confirm').focus();
				return false;
			}
		}

		if(jQuery('#mm_username').val() != jQuery('#mm_original_username').val()) 
		{	
			var msg = "If you change your username we'll need to log you out for security reasons. You can then log back in with your new username.\n\nDo you want to continue?";
			
			return confirm(msg);
		}
		
		return true;
	},
	
	validateBillingInfoForm: function()
	{	
		return true;
	},
	
	viewOrderHistory: function(userId)
	{
		var dialogId = "mm-myaccount-order-history-dialog";
		this.createDialogContainer(dialogId);
		jQuery("#"+dialogId).dialog({
			autoOpen: false,
			close: function(event, ui){
				jQuery(this).dialog("destroy");
				jQuery(this).remove();
			},
		});
		var values =  {};
		values.mm_action = "showDialog";
		values.id = userId;
		values.dialogId = dialogId;
		
		mmdialog_js.showDialog(dialogId, this.module, 700, 450, "Complete Order History", values);
	},
	
	viewGiftHistory: function(userId)
	{
		var dialogId = "mm-myaccount-gift-history-dialog";
		this.createDialogContainer(dialogId);
		jQuery("#"+dialogId).dialog({
			autoOpen: false,
			close: function(event, ui){
				jQuery(this).dialog("destroy");
				jQuery(this).remove();
			},
		});
		var values =  {};
		values.mm_action = "showDialog";
		values.id = userId;
		values.dialogId = dialogId;
		
		mmdialog_js.showDialog(dialogId, this.module, 700, 450, "Complete Gift History", values);
	},
	
	showGiftLink: function(link)
	{
		var dialogId = "mm-myaccount-show-gift-link-dialog";
		this.createDialogContainer(dialogId);
		jQuery("#"+dialogId).dialog({
			autoOpen: false,
			close: function(event, ui){
				jQuery(this).dialog("destroy");
				jQuery(this).remove();
			},
		});
		var values =  {};
		values.mm_action = "showDialog";
		values.id = '';
		values.link = link;
		values.dialogId = dialogId;
		
		mmdialog_js.showDialog(dialogId, this.module, 460, 108, "Gift Link", values);
	},
	
	cancelSubscription: function(orderItemId)
	{
	    var values = {};
		values.mm_order_item_id = orderItemId;
		values.mm_action = "cancelSubscription";
	    
		var doContinue = confirm("Are you sure you want to cancel this subscription? ");
		
		if(doContinue)
		{
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'myaccount_js', "cancelSubscriptionCallback"); 
		}
	},
	
	cancelSubscriptionCallback: function(data)
	{
		if(data.type == "error")
		{
			alert(data.message);
			return;
		}
		else
		{
			alert(data.message);
			document.location.reload();
		}
	},
	
	updateSubscriptionBilling: function()
	{
	    var form = new MM_Form("mm-form-container");
	    var values = form.getFields();
		values.mm_action = "updateSubscriptionBilling";
	    
		// validate form
		var required_fields = new Array();
		var required_fields_label = new Array();
   		
   		required_fields[0]='mm_field_billing_address';
   		required_fields[1]='mm_field_billing_city';
   		required_fields[2]='mm_field_billing_state';
   		required_fields[3]='mm_field_billing_zip';
   		required_fields[4]='mm_field_billing_country';
   		if(!this.usePaymentTokenField)
   		{
   			required_fields[5]='mm_field_cc_number';
   			required_fields[6]='mm_field_cc_cvv';
   		}
   		
   		required_fields_label[0]='billing address';
   		required_fields_label[1]='billing city';
   		required_fields_label[2]='billing state';
   		required_fields_label[3]='billing zip code or postal code';
   		required_fields_label[4]='billing country';
   		if(!this.usePaymentTokenField)
   		{
	   		required_fields_label[5]='credit card number';
	   		required_fields_label[6]='security code';
   		}
   		
      	for (i=0; i < required_fields.length; i++)
      	{	
         	crntField = jQuery("#" + required_fields[i]);
        	crntValue = myaccount_js.ltrim(crntField.val());
			
     		if ((crntValue == '') || (crntValue == ' ') || (crntField.val().length == 0) 
    	     		|| (crntField.val() == null) || (crntField.val() == ''))
        	{
     			alert('Please enter your ' + required_fields_label[i]);
	       		jQuery("#" + required_fields[i]).focus();
	       		return;
        	}
      	} 
      	if (this.callPreUpdateFunctions())
      	{ 
      		this.doSubscriptionBillingUpdate(values);
      	} 
	},
	
	//this method is responsible for form submission. Values contains the form values, and if blank the values will be fetched from the form
	doSubscriptionBillingUpdate: function(values)
	{ 
		if (values === undefined)
		{
			var form = new MM_Form("mm-form-container");
		    var values = form.getFields();
			values.mm_action = "updateSubscriptionBilling";
		} 
		
		if(this.callPreUpdateFunctions(values) === false)
		{
			return false;
		}
		
		var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'myaccount_js', "updateSubscriptionBillingCallback");
	},
	
	updateSubscriptionBillingCallback: function(data)
	{
		if(data.type == "error")
		{
			alert(data.message);
			return;
		}
		else
		{
			alert(data.message);
			document.location.reload();
		}
	},
	
	ltrim: function(str)
	{
		if(str != undefined)
		{
			return str.replace(/^\s+/,"");
		}
		else
		{
			return "";
		}
	},
	
	changeLinkedSocialNetwork: function()
	{
		
	},
	
	unlinkSocialNetwork: function(linkedAccountId, linkedAccountUserId)
	{
		var values = {};
		values.linkedAccountId = linkedAccountId;
		values.linkedAccountUserId = linkedAccountUserId;
		values.mm_action = "unlinkSocialNetwork";
		var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'myaccount_js', "updateSubscriptionBillingCallback"); //callback performs the same needed actions despite the name, so reuse
	},
	
	
	callPreUpdateFunctions: function()
    {
    	if (jQuery("#mm_field_payment_service").length > 0)
    	{
    		var serviceToken = jQuery("#mm_field_payment_service").val();  
			var callbackInfo;  	
    	
	    	while (callbackInfo = this.preUpdateCallbacks.shift()) 
	        {
	            if ((callbackInfo != null) && (typeof callbackInfo == 'object') && (callbackInfo.serviceToken) && (callbackInfo.callback))
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
    
    
    addPreUpdateCallback : function(serviceToken, callback) 
    {
    	if ((serviceToken !== undefined) && (serviceToken != "") && (callback !== undefined) && (typeof callback == 'function' || false))
    	{
    		this.preUpdateCallbacks.push({'serviceToken':serviceToken,'callback':callback});
    	}
    },
    
    
    addPaymentTokenToForm: function(paymentToken) 
    {
    	if ((paymentToken !== undefined) && (paymentToken.length > 0))
    	{
    		if(this.usePaymentTokenField)
    		{  
    			jQuery("#mm-form-container").append("<input type='hidden' name='mm_field_payment_token' id='mm_field_payment_token' value='" + paymentToken + "'>");
    		}
    		else if (jQuery('#mm_field_payment_token').length == 0)
    		{
	    		jQuery("#mm-form-container").append("<input type='hidden' name='mm_field_payment_token' id='mm_field_payment_token' value='" + paymentToken + "'>");
    		}
    		else 
    		{
    			jQuery('#mm_field_payment_token').val(paymentToken);
    		}
    		
    		if(!this.usePaymentTokenField)
    		{ 
	    		//mask the cc number
	    		var ccField = jQuery('#mm_field_cc_number');
	    		var ccVal = ccField.val();
	    		var ccLen = ccVal.length;
	    		var maskLen = (ccLen>4)?(ccLen-4):ccLen;
	    		var maskedVal = Array(maskLen+1).join("*");
	    		if (ccLen != maskLen)
	    		{
	    			maskedVal += ccVal.substring(maskLen,ccLen);
	    		}
	    		ccField.val(maskedVal);
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
    	}
    }
});


var myaccount_js = new MM_MyAccountView("MM_MyAccountView", "Member");