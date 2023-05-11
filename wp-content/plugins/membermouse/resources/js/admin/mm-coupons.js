/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_CouponViewJS = MM_Core.extend({
	createCoupon: function(){

		this.processForm();
		if(this.validateForm()) 
		{
			var form_obj = new MM_Form('mm-coupons-container');
		    var values = form_obj.getFields();
		    values.mm_action = "save";
		    
		    values.mm_products= this.getProducts();
		    values.mm_recurring_setting = (jQuery("#mm_recurring_setting_first").is(":checked"))?"first":"all";
		    
		    values.mm_status = (jQuery("#mm_status_active").is(":checked"))?"1":"0";
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', this.updateHandler); 
		}
	},
	  
	  archive: function(id)
	  { 
		msg = "Are you sure you want to archive this " + this.entityName.toLowerCase() + "?\n\n";
		msg += "Archived coupons cannot be used on new orders.";
	    var doAction = confirm(msg);
	    
	    if(doAction)
	    {
	        var values = {
	            id: id,
	            mm_action: "archive"
	        };
	        
	        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	        ajax.send(values, false, 'mmjs', this.updateHandler); 
	    }
	  },
	  
	  unarchive: function(id)
	  { 
		msg = "Are you sure you want to unarchive this " + this.entityName.toLowerCase() + "?";
	    var doAction = confirm(msg);
	    
	    if(doAction)
	    {
	        var values = {
	            id: id,
	            mm_action: "unarchive"
	        };
	        
	        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	        ajax.send(values, false, 'mmjs', this.updateHandler); 
	    }
	  },
	  
		storeCouponSearchOptions: function()
		{
			this.module = "MM_CouponView";
			this.method = "performAction";
			this.action = "module-handle";
			  
			var values = {
				mm_action: "storeCouponSearchOptions",
				mm_admin_id: jQuery("#mm-admin-id").val(),
				mm_show_archived_coupons: "0",
				mm_show_expired_coupons: "0"
			}
			 
			if(jQuery('#mm-show-archived-coupons').is(':checked'))
			{
				values.mm_show_archived_coupons = "1";
			}
			
			if(jQuery('#mm-show-expired-coupons').is(':checked'))
			{
				values.mm_show_expired_coupons = "1";
			}
			
			 var ajax = new MM_Ajax(false, this.module, this.action, this.method);
			 ajax.useLoader = true;
			 ajax.send(values, false, 'mmjs', "storeSearchOptionsCallback"); 
		},
		
		storeSearchOptionsCallback: function(data)
		{
			if(data == undefined)
			{
				alert("No response received");
			}
			else if(data.type == "error")
			{
				alert(data.message);
			}
			else
			{
				// refresh page
				document.location.href = document.location.href;
			}
		},
	
	getProducts: function(){
		var products = new Array();
		jQuery("input:checkbox[name=mm_products]:checked").each(function()
				{
				    // add jQuery(this).val() to your array
			products.push(jQuery(this).val());
	    	    });
	   return products;
	},
	
	typeChangeHandler: function(){
		if(jQuery("#mm_coupon_type").val() == "free")
		{
			jQuery("#mm_coupon_value").hide();
			jQuery("#mm_subscription_options_section").hide();
		}
		else
		{
			jQuery("#mm_coupon_value").show();
			jQuery("#mm_subscription_options_section").show();
		}
	},
	
	processForm: function(){
		
	},
	
	validateForm: function()
	{
		var re = new RegExp("^[0-9\.]+$","g");
		if(jQuery("#mm_coupon_name").val()==""){
			return this.throwError("Please enter a name for the coupon");
		}
		else if(jQuery("#mm_coupon_name").val().length > 255){
			return this.throwError("Please enter a name for the coupon less than 255 characters");
		}
		else if(jQuery("#mm_coupon_code").val().length > 50){
			return this.throwError("Please enter a code for the coupon less than 50 characters");
		}
		else if(jQuery("#mm_coupon_code").val()==""){
			return this.throwError("Please enter a coupon code");
		}
		else if(jQuery("#mm_coupon_type").val() != "free" && (jQuery("#mm_coupon_value").val()=="" || !re.test(jQuery("#mm_coupon_value").val()))){
			return this.throwError("Please enter a numeric discount amount");
		}
		else if(jQuery("#mm_coupon_type").val() == "percentage" && parseInt(jQuery("#mm_coupon_value").val())>100){
			return this.throwError("The coupon value must be less than or equal to 100.");
		}
		else if(jQuery("#mm_start_date").val()==""){
			return this.throwError("Please provide a coupon start date");
		}
		else if ((jQuery("#mm_end_date").val() != "") && (Date.parse(jQuery("#mm_start_date").val()) > Date.parse(jQuery("#mm_end_date").val()))) {
			return this.throwError("Start Date must be before End Date");
		}
		return true;
	},
	
	throwError: function(msg){
		alert(msg);
		return false;
	},
	
});

var mmjs = new MM_CouponViewJS("MM_CouponView", "Coupon");