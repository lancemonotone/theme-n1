/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_MemberDetailsViewJS = MM_Core.extend({

	updateMember: function(id)
	{	
		this.id = id;
		
		if(this.validateForm()) 
		{
			var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
		     
		    this.page = values.page;
		    this.mm_module = values.module;
		    
		    values.mm_action = "updateMember";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "memberUpdateHandler"); 
		}
	},
	
	memberUpdateHandler: function(data)
	{
		if(data.type == "error")
		{
			if(data.message.length > 0)
			{  
				alert(data.message);
				return false;
			}
		}
		else 
		{
			if(data.message != undefined && data.message.length > 0)
			{
				alert(data.message);
			}
		  
			this.refreshView();
		}
	},
  
	refreshView: function()
	{
		var values = {
			user_id: this.id,
			page: this.page,
			mm_module: this.mm_module,
			mm_action: "refreshView"
		};
    
		var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		ajax.send(values, false, 'mmjs','refreshViewCallback'); 
	},
	
	sendPasswordEmail: function(user_id)
	{
	    var isOk = confirm("Are you sure you want to send this member a reset password email?");
	    if(isOk){
			var values = {};
		     
		    values.mm_action = "sendPasswordEmail";
		    values.user_id = user_id;
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "passwordUpdateHandler"); 
	    }
	},
	
	passwordUpdateHandler: function(data)
	{
		if(data.type=='error'){
			alert(data.message);
		}
		else{
			alert("A reset password email was sent successfully");
		}
	},
	
	validateForm: function()
	{
		if(jQuery('#mm-email').val() == "") 
		{
			alert("Email is required");
			jQuery('#mm-email').focus();
			return false;
		}
	   
		if(!this.validateEmail(jQuery('#mm-email').val())) 
		{
			alert("Please enter a valid email address");
			jQuery('#mm-email').focus();
			return false;
		}
		
		if(jQuery('#mm-username').val() == "") 
		{
			alert("Username is required");
			jQuery('#mm-username').focus();
			return false;
		}
		
		if(jQuery('#mm-new-password').val() != "") 
		{
			if(jQuery('#mm-new-password').val() != jQuery('#mm-confirm-password').val()) 
			{
				alert("The new and confirm passwords don't match");
				jQuery('#mm-confirm-password').focus();
				return false;
			}
		}

		return true;
	},
	
	/** 
	 * SEND WELCOME EMAIL FUNCTIONS 
	 */
	
	sendWelcomeEmail: function(user_id)
	{
	    var isOk = confirm("Are you sure you want to resend the welcome email to this member?");
	    if(isOk){
			var values = {};
		     
		    values.mm_action = "sendWelcomeEmail";
		    values.user_id = user_id;
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "sendWelcomeEmailHandler"); 
	    }
	},
	
	sendWelcomeEmailHandler: function(data)
	{
		if(data.type=='error'){
			alert(data.message);
		}
		else{
			alert("The welcome email was resent successfully");
			location.reload(); 
		}
	},
	
	/** 
	 * LOGIN AS MEMBER FUNCTIONS 
	 */
	
	loginAsMember: function(user_id)
	{
	    var isOk = confirm("Are you sure you want to login as this member?\n\nYou will be logged out of your account if you proceed.");
	    if(isOk){
			var values = {};
		     
		    values.mm_action = "loginAsMember";
		    values.user_id = user_id;
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "loginAsMemberHandler"); 
	    }
	},
	
	loginAsMemberHandler: function(data)
	{
		if(data.type=='error'){
			alert(data.message);
		}
		else{
			document.location = data.message;
		}
	},
	
	/** 
	 * DELETE MEMBER FUNCTIONS 
	 */
	
	deleteMember: function(id, redirectUrl)
	{
		var msg = "Are you sure you want to delete this member?\n\nAll data associated with this member will be deleted. This operation cannot be undone.";
		
		if(confirm(msg))
		{
			this.redirectUrl = redirectUrl;

			var values = {};
		    values.id = id;
		    values.mm_action = "deleteMember";
		        
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "deleteMemberHandler"); 
		}
	},
	
	deleteMemberHandler: function(data)
	{
		if(data.type == "error")
		{
			if(data.message.length > 0)
			{  
				alert(data.message);
				return false;
			}
		}
		else 
		{
			if(data.message != undefined && data.message.length > 0)
			{
				alert(data.message);
			}
			
			document.location = this.redirectUrl;
		}
	},
	
	/** 
	 * FORGET MEMBER FUNCTIONS 
	 */
	
	forgetMember: function(user_id)
	{
	    var isOk = confirm("Are you sure you want to forget this member?\n\nAny active subscriptions will be cancelled, and user information will immediately be anonymized if you proceed.");
	    if(isOk){
			var values = {};
		     
		    values.mm_action = "forgetMember";
		    values.user_id = user_id;
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "forgetMemberHandler"); 
	    }
	},
	
	forgetMemberHandler: function(data)
	{
		if(data.type=='error')
		{
			alert(data.message);
		}
		else
		{
			alert("The member was successfully forgotten");
			location.reload(); 
		}
	},
	
	/** 
	 * MERGE ACCOUNT FUNCTIONS 
	 */
	showMergeAccountsDialog: function(user_id)
	{
		var dialogId = "mm-edit-merge-dialog";
		jQuery("#"+dialogId).dialog({autoOpen: false});
		
		var values = {};
	    values.user_id = user_id; 
	    values.mm_action = "showMergeAccountsDialog";
		mmdialog_js.showDialog(dialogId, this.module, 700, 720, "Merge Accounts Tool", values, "performAction");
	}, 

	/**
	 * This method powers the member autocomplete field for the secondary user. It searches for available 
	 * members to merge the primary user with and returns that list. 
	 */	
	findMembersToMerge: function(response, searchString)
	{  
		mmjs.response = response;
		var form_obj = new MM_Form('mm-edit-subscription-div');
	    var values = form_obj.getFields();
	    values.user_id = jQuery("#user_id").val();
		values.searchString = searchString;
		values.mm_action = "findMembersToMerge";
	   
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method); 
	    ajax.send(values, false, 'mmjs', "findMembersToMergeCallback"); 
	},
	
	findMembersToMergeCallback: function(data)
	{ 
		if(mmjs.response == undefined || mmjs.response == null || data.type=="error")
		{
			jQuery("#compare_btn").attr("disabled","disabled");
			jQuery("#compare_btn").attr("class","mm-ui-button grey"); 
			alert(data.message);
		}
		else
		{
			if(Array.isArray(data.message) && data.message.length > 0)
			{
				jQuery("#compare_btn").removeAttr("disabled");
				jQuery("#compare_btn").attr("class","mm-ui-button blue");
				mmjs.response(data.message); 
			}
		}
	},
	
	/**
	 * Retrieve data for both member accounts involved in the merge and open the 'General' accordian tab.
	 */
	getMergeUserData: function() {
		var id = jQuery("#secondary_user_id").val();
		
		if (id == undefined || id == null || id == "" || parseInt(id) <= 0) {
			alert("Please select a secondary member account before proceeding");
			return false;
		}
		
		jQuery("#secondary_user_id_field").attr("readonly", "readonly");
		
		var values = {};
		values.mm_action = "getMergeUserData"; 
	    values.event_type = "select";
	    values.user_id = jQuery("#user_id").val();
		values.secondary_user_id = jQuery("#secondary_user_id").val();

	    var ajax = new MM_Ajax(false, this.module, this.action, this.method); 
	    ajax.send(values, false, 'mmjs', "getMergeUserDataCallback"); 
	},
	
	getMergeUserDataCallback: function(data)
	{ 
		if(mmjs.response == undefined || mmjs.response == null || data.type=="error")
		{ 
			alert(data.message);
		}
		else
		{  
			mmjs.updateMergeAccountsDialog(data.message);
			showSection("general");
		}
	},
	
	updateMergeAccountsDialog: function(allData) {
		primaryUser = allData.user_id;
		secondaryUser = allData.secondary_user_id;
		
		// Bundles
		var primaryUserBundles = "";
		
		if (primaryUser.bundles != undefined && primaryUser.bundles !== null && Array.isArray(primaryUser.bundles)) {
			for (var eachBundle in primaryUser.bundles) {
				var bundle = primaryUser.bundles[eachBundle];
				primaryUserBundles += '<label class="mm-edit-input-option">'; 
				primaryUserBundles += ' <span class="option-label">' + bundle.name + '</span>';
				if(bundle.status == "-1") {
					if(bundle.is_complimentary != undefined && bundle.is_complimentary == '1')
					{
						primaryUserBundles += ' <span class="option-desc"><a title="This bundle is complimentary"><i class="fa fa-ticket"></i></a> Complimentary</span>';
					}
					else
					{
						primaryUserBundles += ' <span class="option-desc">No active billing</span>';
					}
				} else { 
					primaryUserBundles += ' <span class="option-desc">' + bundle.rebill_description + '</span>';
				}
				primaryUserBundles += '</label>';
			}
		}
 
		var secondaryUserBundles = "";
		if (secondaryUser.bundles != undefined && secondaryUser.bundles !== null && Array.isArray(secondaryUser.bundles)) {
			for (var eachBundle in secondaryUser.bundles) {
				var bundle = secondaryUser.bundles[eachBundle];
				secondaryUserBundles += '<label class="mm-edit-input-option">';
				
				// check if this bundle is complimentary and disable the checkbox if it is as transferring complimentary bundles isn't supported
				var disabled = ''
				if(bundle.is_complimentary != undefined && bundle.is_complimentary == '1')
				{
					disabled = ' disabled';
				}
				
				if(primaryUser.bundles.find(primaryUserBundle => {return primaryUserBundle.id === bundle.id}))
				{
					secondaryUserBundles += ' <input type="checkbox" name="option-bundle" value="' + bundle.id + '" data-replace-existing-bundle="' + bundle.id + '" ' + disabled + ' />';
				}
				else
				{
					secondaryUserBundles += ' <input type="checkbox" name="option-bundle" value="' + bundle.id + '" data-replace-existing-bundle="0" ' + disabled + ' />';
				}
				secondaryUserBundles += ' <span class="option-label">' + bundle.name + '</span>';
				if(bundle.status == "-1") {
					if(bundle.is_complimentary != undefined && bundle.is_complimentary == '1')
					{
						secondaryUserBundles += ' <span class="option-desc"><a title="This bundle is complimentary"><i class="fa fa-ticket"></i></a> Complimentary bundles can\'t be merged</span>';
					}
					else
					{
						secondaryUserBundles += ' <span class="option-desc">No active billing</span>';
					}
				} else { 
					secondaryUserBundles += ' <span class="option-desc">' + bundle.rebill_description + '</span>';
				}
				secondaryUserBundles += '</label>';
			}
		} 

		// Products
		var primaryUserProducts = "";
		if (primaryUser.products != undefined && primaryUser.products !== null && Array.isArray(primaryUser.products)) {
			for (var eachProduct in primaryUser.products) {
				var product = primaryUser.products[eachProduct];
				primaryUserProducts += '<label class="mm-edit-input-option">';
				primaryUserProducts += ' <span class="option-label">' + product.name + '</span>';
				primaryUserProducts += ' <span class="option-desc">';
				
				if(product.is_gift == '1')
				{
					primaryUserProducts += '<a title="This product was purchased as a gift"><i class="fa fa-gift"></i></a> ';
				}
				
				primaryUserProducts += product.description + '</span>';
				primaryUserProducts += '</label>';
			}
		}

		var secondaryUserProducts = "";
		if (secondaryUser.products != undefined && secondaryUser.products !== null && Array.isArray(secondaryUser.products)) {
			for (var eachProduct in secondaryUser.products) {
				var product = secondaryUser.products[eachProduct];
				secondaryUserProducts += '<label class="mm-edit-input-option">';
				secondaryUserProducts += ' <input type="checkbox" name="option-product" value="' + product.order_item_id + '" />';
				secondaryUserProducts += ' <span class="option-label">' + product.name + '</span>';
				secondaryUserProducts += ' <span class="option-desc">';
				
				if(product.is_gift == '1')
				{
					secondaryUserProducts += '<a title="This product was purchased as a gift"><i class="fa fa-gift"></i></a> ';
				}
				
				secondaryUserProducts += product.description + '</span>';
				secondaryUserProducts += '</label>';
			}
		} 
		
		// Card on File
		var primaryUserCardOnFile = "";
		primaryUserCardOnFile += '<label class="mm-edit-input-option">';

		// check if first user has a card on file
		if(primaryUser.has_card_on_file == "0")
		{
			primaryUserCardOnFile += ' <input type="radio" id="primary-user-card-on-file" name="option-cc-on-file" disabled value="' + CHOICE_PRIMARY +'" data-has-card-on-file="0" />';
			primaryUserCardOnFile += ' <span class="option-label"><em>No credit card on file</em></span>';
		}
		else
		{
			primaryUserCardOnFile += ' <input type="radio" id="primary-user-card-on-file" name="option-cc-on-file" checked="checked" value="' + CHOICE_PRIMARY +'" data-has-card-on-file="1" />';
			primaryUserCardOnFile += ' <span class="option-label">Use current account credit card on file</span>';
		}
		
		primaryUserCardOnFile += '</label>';
		
		var secondaryUserCardOnFile = "";
		secondaryUserCardOnFile += '<label class="mm-edit-input-option">';

		// check if second user has a card on file
		if(secondaryUser.has_card_on_file == "0")
		{
			secondaryUserCardOnFile += ' <input type="radio" id="secondary-user-card-on-file" name="option-cc-on-file" disabled value="' + CHOICE_SECONDARY +'" data-has-card-on-file="0" />';
			secondaryUserCardOnFile += ' <span class="option-label"><em>No credit card on file</em></span>';
		}
		else
		{
			secondaryUserCardOnFile += ' <input type="radio" id="secondary-user-card-on-file" name="option-cc-on-file" ';
			
			// check this by default if the first user doesn't have a card on file
			if(primaryUser.has_card_on_file == "0")
			{
				secondaryUserCardOnFile += 'checked="checked" '; 
			}
			
			secondaryUserCardOnFile += 'value="' + CHOICE_SECONDARY +'" data-has-card-on-file="1" />';
			secondaryUserCardOnFile += ' <span class="option-label">Use secondary account credit card on file</span>';
		}
		
		secondaryUserCardOnFile += '</label>';

		// Custom Fields
		var customFields = "<p><em>No custom fields defined</em></p>";
		if (secondaryUser.custom_fields != undefined && secondaryUser.custom_fields !== null && Array.isArray(secondaryUser.custom_fields)) {

			if (primaryUser.custom_fields != undefined && primaryUser.custom_fields !== null && Array.isArray(primaryUser.custom_fields)) {
				if (secondaryUser.custom_fields.length == primaryUser.custom_fields.length) {
					var customFields = "";
					
					for (i = 0; i < primaryUser.custom_fields.length; i++) {
						var user1Data = primaryUser.custom_fields[i];
						var user2Data = secondaryUser.custom_fields[i];
						
						user1Data.value = (mmjs.isEmptyField(user1Data.value)) ? emtpyStr : user1Data.value;
						user2Data.value = (mmjs.isEmptyField(user2Data.value)) ? emtpyStr : user2Data.value;

						user1Data.value = (user1Data.value == "mm_cb_off" || user1Data.value == "mm_cb_on") ? user1Data.value.replace("mm_cb_","") : user1Data.value;
						user2Data.value = (user2Data.value == "mm_cb_off" || user2Data.value == "mm_cb_on") ? user2Data.value.replace("mm_cb_","") : user2Data.value;
		  
						customFields += "<tr>";
						customFields += "<td><strong><span class='custom-name'>" + user1Data.name + "</span></strong></td>";
						customFields += '<td><label class="mm-edit-input-option">';
						customFields += ' <input type="radio"  name="option-customfields-' + user1Data.id + '" value="' + CHOICE_PRIMARY +'" checked /><span>' + user1Data.value + "</span>";
						customFields += '</label></td>';
						customFields += '<td><label class="mm-edit-input-option">';
						customFields += ' <input type="radio"  name="option-customfields-' + user2Data.id + '" value="' + CHOICE_SECONDARY +'" /><span>' + user2Data.value + '</span>';
						customFields += '</label></td>';
						customFields += "</tr>";
					}
				}
			}
		}
		
		/**
		 * UPDATE UI
		 */
		
		// Account Info
		jQuery("#fname1").html((primaryUser.first_name != "") ? primaryUser.first_name : emtpyStr);
		jQuery("#lname1").html((primaryUser.last_name != "") ? primaryUser.last_name : emtpyStr);
		jQuery("#email1").html((primaryUser.email != "") ? primaryUser.email : emtpyStr);
		jQuery("#username1").html((primaryUser.username != "") ? primaryUser.username : emtpyStr);
		jQuery("#phone1").html((primaryUser.phone != "") ? primaryUser.phone : emtpyStr);
		
		jQuery("#fname2").html((secondaryUser.first_name != "") ? secondaryUser.first_name : emtpyStr);
		jQuery("#lname2").html((secondaryUser.last_name != "") ? secondaryUser.last_name : emtpyStr);
		jQuery("#email2").html((secondaryUser.email != "") ? secondaryUser.email : emtpyStr);
		jQuery("#username2").html((secondaryUser.username != "") ? secondaryUser.username : emtpyStr);
		jQuery("#phone2").html((secondaryUser.phone != "") ? secondaryUser.phone : emtpyStr);

		var arr = ["address", "address2", "city", "state", "zip", "country"];
		var shippingCnt=0;
		var billingCnt=0;
		for (var field in arr) {
			var fieldName = "shipping_" + arr[field];
			var firstVal = (primaryUser[fieldName] != "") ? primaryUser[fieldName] : emtpyStr;
			var secondVal =  (secondaryUser[fieldName] != "") ? secondaryUser[fieldName] : emtpyStr;

			if(mmjs.isEmptyField(firstVal, emtpyStr) && mmjs.isEmptyField(secondVal, emtpyStr))
			{
				var rowField = arr[field];  
				if(arr[field]=="address")
				{
					rowField = rowField+"1";
				}
				jQuery("#" + rowField + "_row").hide(); 
			}
			else
			{
				jQuery("#" + arr[field] + "1").html(firstVal);
				jQuery("#" + arr[field] + "2").html(secondVal);
				shippingCnt++;
			}

			var fieldName = "billing_" + arr[field];
			firstVal = (primaryUser[fieldName] != "") ? primaryUser[fieldName] : emtpyStr;
			secondVal = (secondaryUser[fieldName] != "") ? secondaryUser[fieldName] : emtpyStr;
			if(mmjs.isEmptyField(firstVal, emtpyStr) && mmjs.isEmptyField(secondVal, emtpyStr))
			{
				var rowField = arr[field];  
				if(arr[field]=="address")
				{
					rowField = rowField+"1";
				}
				jQuery("#billing_" + rowField + "_row").hide(); 
			}
			else
			{ 
				jQuery("#billing_" + arr[field] + "1").html(firstVal);
				jQuery("#billing_" + arr[field] + "2").html(secondVal);
				billingCnt++;
			}
		}

		if(shippingCnt==0)
		{
			jQuery("#shipping_rows").hide();
		}

		if(billingCnt==0)
		{
			jQuery("#billing_rows").hide();
		}
		
		// Custom Fields
		jQuery("#custom_fields").empty();
		jQuery("#custom_fields").append(customFields);
		
		// Membership Level 
		jQuery("#membership_level1").html(primaryUser.membership_level);
		jQuery("#membership_level2").html(secondaryUser.membership_level);
		
		var primaryUserMembershipDescription = primaryUser.membership_description;
		if(primaryUser.membership_is_complimentary != undefined && primaryUser.membership_is_complimentary == '1')
		{
			primaryUserMembershipDescription = '<a title="This membership level is complimentary"><i class="fa fa-ticket"></i></a> Complimentary';
		}
		jQuery("#membership_description1").html(primaryUserMembershipDescription);
		
		var secondaryUserMembershipDescription = secondaryUser.membership_description;
		if(secondaryUser.membership_is_complimentary != undefined && secondaryUser.membership_is_complimentary == '1')
		{
			secondaryUserMembershipDescription = '<a title="This membership level is complimentary"><i class="fa fa-ticket"></i></a> Complimentary';
		}
		jQuery("#membership_description2").html(secondaryUserMembershipDescription);

		// Bundles
		jQuery("#bundles td.dynamic_bundle").remove();
		jQuery("#bundles").append("<td class='dynamic_bundle'>" + primaryUserBundles + "</td>");
		jQuery("#bundles").append("<td class='dynamic_bundle'>" + secondaryUserBundles + "</td>");
		
		if(primaryUserBundles.length<=0 && secondaryUserBundles.length<=0)
		{
			jQuery("#bundles").hide();
		}
		
		// Other Subscriptions
		jQuery("#products td.dynamic_products").remove();
		jQuery("#products").append("<td class='dynamic_products'>" + primaryUserProducts + "</td>");
		jQuery("#products").append("<td class='dynamic_products'>" + secondaryUserProducts + "</td>");

		if(primaryUserProducts.length<=0 && secondaryUserProducts.length<=0)
		{
			jQuery("#products").hide();
		}
		else
		{
			jQuery("#products").show();
		}

		// Card on File
		jQuery("#card_on_file td.dynamic_card_on_file_options").remove();
		jQuery("#card_on_file").append("<td class='dynamic_card_on_file_options'>" + primaryUserCardOnFile + "</td>");
		jQuery("#card_on_file").append("<td class='dynamic_card_on_file_options'>" + secondaryUserCardOnFile + "</td>");
		
		if(primaryUser.has_card_on_file == "0" && secondaryUser.has_card_on_file == "0")
		{
			jQuery("#card_on_file").hide();
		}
	},
	
	
	/**
	 * Review all the choices made by the user in all sections of the form and populate the userChoices global variable
	 * according to the selections that indicate how the accounts should be merged. Also, render a preview
     * of how the merged account will appear based on the choices made. 
	 */ 
	generatePreview: function() {
		userChoices = [];
		userChoices["general"] = [];
		userChoices["custom_fields"] = [];
		userChoices["bundles"] = [];
		userChoices["orderItems"] = [];
		
		// clear previous preview data
		jQuery("#preview_general").empty();
		jQuery("#preview_custom_fields").empty();
		jQuery("#preview_access_rights").empty();

		hideAllSections();

		// gather account information choices
		var generalPreview = "<tr>";
		var div = jQuery("#general-options");
		var index = 0;
		jQuery(div).find('input:radio')
			.each(function() 
			{ 
				if (jQuery(this).is(":checked")) 
				{
					var fieldName = jQuery(this).attr("name").replace("general-option-", "");
					var fieldLabel = fieldName;
					var prefix = "";
					if (fieldLabel.indexOf("billing-") >= 0) {
						fieldLabel = jQuery(this).attr("name").replace("general-option-billing-", "");
						prefix = "Billing ";
					}
					fieldLabel = fieldLabel.replace("_", " ");
					fieldLabel = prefix + mmjs.capitalize(fieldLabel);

					userChoices["general"][index++] = {fieldName:fieldName, choice:jQuery(this).val()};
					var value = jQuery(this).parent().find("span").html();
					var choice = jQuery(this).val();
					if(!mmjs.isEmptyField(value, emtpyStr)) 
					{
						if(fieldLabel.indexOf("Address1")>=0)
						{
							fieldLabel=fieldLabel.replace("Address1","Address 1");
						}
						else if(fieldLabel.indexOf("Address2")>=0)
						{
							fieldLabel=fieldLabel.replace("Address2","Address 2");
						}

						if(fieldLabel.indexOf("Billing")<0 && fieldName.match(/(address|country|city|state|zip)/gi))
						{
							fieldLabel = "Shipping "+fieldLabel;
						}
						
						if(choice == CHOICE_SECONDARY)
						{
							generalPreview += "<tr class='mm-row-changed'>";
							generalPreview += "<td><strong>" + fieldLabel + "</strong></td>";
						}
						else
						{
							generalPreview += "<tr>";
							generalPreview += "<td>" + fieldLabel + "</td>";
						}
						
						generalPreview += "<td>" + value + "</td>";
						generalPreview += "</tr>";
					} 
				}

			});
			
		// gather custom field choices
		var customFieldsPreview = ""; 
		var div = jQuery("#custom-fields"); 
		jQuery(div).find('input:radio')
			.each(function() {

				if (jQuery(this).is(":checked")) {
					var fieldName = jQuery(this).attr("name");
					
					if (fieldName.indexOf("customfields") >= 0) {
						var fieldId = fieldName.replace("option-customfields-", "");
						var fieldValue = jQuery(this).parent().find("span").html();
						var choice = jQuery(this).val();
						var fieldName = jQuery(this).parent().parent().parent().find("span.custom-name").html(); 
						userChoices["custom_fields"][fieldId] = jQuery(this).val();
						
						if(choice == CHOICE_SECONDARY)
						{
							customFieldsPreview += "<tr class='mm-row-changed'>";
							customFieldsPreview += "<td><strong>" + fieldName + "</strong></td>";
						}
						else
						{
							customFieldsPreview += "<tr>";
							customFieldsPreview += "<td>" + fieldName + "</td>";
						}
						
						customFieldsPreview += "<td>" + fieldValue + "</td>";
						customFieldsPreview += "</tr>";  
					}
				}
			});
		
		if(customFieldsPreview == "")
		{
			customFieldsPreview = "<em>No custom field defined</em>";
		}

		// membership details
		var membership = jQuery('input[name=option-membership]:checked').val()
		userChoices["membership"] = membership;
		var accessRightsHtml = "";
		if (membership == CHOICE_PRIMARY) {
			accessRightsHtml = "<tr>";
			accessRightsHtml += "<td>Membership Level</td>";
			accessRightsHtml += "<td>The membership level '" + jQuery("#membership_level1").html() + "' on the current account will be used.</td>";
			accessRightsHtml += "</tr>";
		}
		if (membership == CHOICE_SECONDARY) {
			accessRightsHtml = "<tr class='mm-row-changed'>";
			accessRightsHtml += "<td><strong>Membership Level</strong></td>";
			accessRightsHtml += "<td>The membership level '" + jQuery("#membership_level2").html() + "' on the secondary account will be used.</td>";
			accessRightsHtml += "</tr>";
		}

		// bundles & products details
		var bundleData = "";
		var productsData = "";
		var primaryBundlesBeingReplaced = [];
		var bundlesChanged = false;
		var productsChanged = false;
		var div = jQuery("#access-options");
		
		jQuery(div).find('input:checkbox')
			.each(function() {

				if (jQuery(this).is(":checked")) 
				{
					var fieldName = jQuery(this).attr("name"); 
					if (fieldName.indexOf("bundle") >= 0) 
					{
						bundlesChanged = true;
						var fieldName = jQuery(this).parent().find("span.option-label").html();
						var description = jQuery(this).parent().find("span.option-desc").html();
						
						if(jQuery(this).data("replace-existing-bundle") != "0")
						{
							primaryBundlesBeingReplaced.push(jQuery(this).data("replace-existing-bundle"));
						}
						
						bundleData += fieldName + ": " + description + "<br />";
						userChoices["bundles"].push(jQuery(this).val());
					} 
					else if (fieldName.indexOf("product") >= 0) 
					{
						productsChanged = true;
						var fieldName = jQuery(this).parent().find("span.option-label").html();
						var description = jQuery(this).parent().find("span.option-desc").html();
						productsData += fieldName + ": " + description + "<br />";
						userChoices["orderItems"].push(jQuery(this).val());
					}
				}
			});
		
		bundlesHtml = "";
		hasExistingBundles = false;
		doTransferBundles = (bundleData.length > 0);
		if(bundlesChanged == true)
		{
			bundlesHtml += "<tr class='mm-row-changed'>";
			bundlesHtml += "<td><strong>Bundles</strong></td>";
		}
		else
		{
			bundlesHtml += "<tr>";
			bundlesHtml += "<td>Bundles</td>";
		}
		
		bundlesHtml += "<td>";
		
		// existing bundles
		if (primaryUser.bundles != undefined && primaryUser.bundles !== null && Array.isArray(primaryUser.bundles) && primaryUser.bundles.length > 0) {
			hasExistingBundles = true;
			bundlesHtml += "Existing Bundles:<br/>";
			for (var eachBundle in primaryUser.bundles) {
				showBundles = true;
				var bundle = primaryUser.bundles[eachBundle];
				
				if(primaryBundlesBeingReplaced.includes(parseInt(bundle.id)) == true) 
				{
					bundlesHtml += "<span style='text-decoration: line-through;'>";
				}
				
				bundlesHtml += bundle.name + ': ';
				if(bundle.status == "-1") {
					if(bundle.is_complimentary != undefined && bundle.is_complimentary == '1')
					{
						bundlesHtml += '<a title="This bundle is complimentary"><i class="fa fa-ticket"></i></a> Complimentary';
					}
					else
					{
						bundlesHtml += 'No active billing';
					}
				} else { 
					bundlesHtml += bundle.rebill_description;
				}
				
				if(primaryBundlesBeingReplaced.includes(parseInt(bundle.id)) == true)
				{
					bundlesHtml += "</span> [replaced by secondary account]";
				}
				
				bundlesHtml += "<br/>";
			}
			bundlesHtml += "<br/>";
		}
		
		if(doTransferBundles == true)
		{
			showBundles = true;
			bundlesHtml += "<strong>The following bundles will be added to the current account:</strong><br/>" + bundleData;	
		}
		bundlesHtml += "</td></tr>";
		
		if(hasExistingBundles || doTransferBundles)
		{
			accessRightsHtml += bundlesHtml;
		}
		else
		{
			accessRightsHtml += "<tr>";
			accessRightsHtml += "<td>Bundles</td>";
			accessRightsHtml += "<td><em>No bundles selected.</em></td>";
			accessRightsHtml += "</tr>";
		}
		
		productsHtml = "";
		showProducts = false;
		
		hasExistingProducts = false;
		doTransferProducts = (productsData.length > 0);
		if(productsChanged == true)
		{
			productsHtml += "<tr class='mm-row-changed'>";
			productsHtml += "<td><strong>Products</strong></td>";
		}
		else
		{
			productsHtml += "<tr>";
			productsHtml += "<td>Products</td>";
		}
		
		productsHtml += "<td>";
		
		// existing products
		if (primaryUser.products != undefined && primaryUser.products !== null && Array.isArray(primaryUser.products) && primaryUser.products.length > 0) {
			hasExistingProducts = true;
			productsHtml += "Existing Products:<br/>";
			for (var eachProduct in primaryUser.products) {
				showProducts = true;
				var product = primaryUser.products[eachProduct];
				productsHtml += product.name + ': ';
				
				if(product.is_gift == '1')
				{
					productsHtml += '<a title="This product was purchased as a gift"><i class="fa fa-gift"></i></a> ';
				}
				
				productsHtml += product.description;
				productsHtml += "<br/>";
			}
			productsHtml += "<br/>";
		}
		
		if(doTransferProducts == true)
		{
			productsHtml += "<strong>The following products will be added to the current account:</strong><br/>" + productsData;
		}
		productsHtml += "</td></tr>";
		
		if((hasExistingProducts == true) || (doTransferProducts == true))
		{
			accessRightsHtml += productsHtml;
		}
		else
		{
			accessRightsHtml += "<tr>";
			accessRightsHtml += "<td>Products</td>";
			accessRightsHtml += "<td><em>No products selected.</em></td>";
			accessRightsHtml += "</tr>";
		}
		
		userChoices["credit-card-on-file"] = CHOICE_PRIMARY;
		
		if(jQuery("#primary-user-card-on-file").data("has-card-on-file") == "1" || jQuery("#secondary-user-card-on-file").data("has-card-on-file") == "1")
    	{
    		var ccMsg = "Use current account credit card on file";
    		var radioValue = jQuery("input[name='option-cc-on-file']:checked").val();
    		
    		if (radioValue == CHOICE_SECONDARY)
    		{
    			userChoices["credit-card-on-file"] = CHOICE_SECONDARY;
    			ccMsg = "Use secondary account credit card on file";
				
				accessRightsHtml += "<tr class='mm-row-changed'>";
				accessRightsHtml += "<td><strong>Credit Card on File</strong></td>";
    		}
			else
			{
				accessRightsHtml += "<tr>";
				accessRightsHtml += "<td>Credit Card on File</td>";
			}
    	}
    	else
    	{
			accessRightsHtml += "<tr>";
			accessRightsHtml += "<td>Credit Card on File</td>";
    		ccMsg = "<em>No credit card on file on either account.</em>";
    	}
    	
		accessRightsHtml += "<td>" + ccMsg + "</td>";
		accessRightsHtml += "</tr>";

		jQuery("#preview_general").append(generalPreview);
		jQuery("#preview_custom_fields").html(customFieldsPreview);
		jQuery("#preview_access_rights").append(accessRightsHtml);
		jQuery("#preview").show();
	},

	executeMerge: function()
	{     
		var values = {};
	    values.user_id = jQuery("#user_id").val();
		values.secondary_user_id = jQuery("#secondary_user_id").val();
		values.mm_action = "executeMerge"; 
		for(var eachvar in userChoices)
		{
			values[eachvar] = userChoices[eachvar];
		}

		if(values.secondary_user_id == undefined || values.secondary_user_id == null)
		{
			alert("Please first choose another member to merge into this member's account.");
			jQuery("#secondary_user_id").focus();
			return;
		}
		
		var msg = "This operation is irreversible. If you continue, the current account will be updated according to the choices you've made.\n\n";
		msg += "The secondary account will be removed and any data or subscriptions that you haven't selected will be deleted or cancelled.\n\n";
		msg += "Are you sure you want to continue?";
 
		var doProceed = confirm(msg);
		if(doProceed)
		{
			var ajax = new MM_Ajax(false, this.module, this.action, this.method); 
		    ajax.send(values, false, 'mmjs', "executeMergeCallback");
		}
	}, 

	executeMergeCallback: function(data)
	{ 
		alert(data.message);
		
		if(data.type=="success")
		{
			mmjs.closeDialog();
			document.location.reload();
		}
	},
	
	/**
	 * MERGE ACCOUNTS HELPER FUNCTIONS
	 */

	isEmptyField: function(fieldVal, defaultValue)
	{
		if(fieldVal == defaultValue || fieldVal == "" || fieldVal == null)
		{
			return true;
		}
		return false;
	},

	capitalize: function(str) {
		var splitStr = str.toLowerCase().split(' ');
		for (var i = 0; i < splitStr.length; i++) {
			// You do not need to check if i is larger than splitStr length, as your for does that for you
			// Assign it back to the array
			splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
		}
		// Directly return the joined string
		return splitStr.join(' ');
	}
	
});

var CHOICE_PRIMARY = "1";
var CHOICE_SECONDARY = "2";
var emtpyStr = "&mdash;";

var primaryUser;
var secondaryUser;
var userChoices = [];

var redirectUrl = "";

var mmjs = new MM_MemberDetailsViewJS("MM_MemberDetailsView", "Member");