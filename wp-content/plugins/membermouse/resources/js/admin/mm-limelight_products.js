/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_LimeLightProductsViewJS = MM_Core.extend({
  
	processForm: function()
	{   
		//jQuery("#limelight_campaign_name").attr('value', jQuery("#limelight_campaign_id :selected").text());
		
	    var selectedNames = jQuery("#limelight_campaign_id option:selected").map(function () {
	        return jQuery(this).text();
	    }).get().join('|');
	    
	    jQuery("#limelight_campaign_name").attr('value', selectedNames);
	    
	    if(jQuery("#limelight_map_all_associated_campaigns").is(":checked"))
	    {
	    	jQuery("#limelight_campaign_map_all").val("1");
	    }
	    else
	    {
	    	jQuery("#limelight_campaign_map_all").val("0");
	    }
		
		if(jQuery("#limelight_product_id_selector").is(":visible"))
		{
			jQuery("#limelight_product_id").attr('value', jQuery("#limelight_product_id_selector").val());
			jQuery("#limelight_product_name").attr('value', jQuery("#limelight_product_id_selector :selected").text());
		}
	},
	
	validateForm: function()
	{   
		if(jQuery("#limelight_product_id").val() == 0 || jQuery("#limelight_product_id").val() == "")
		{
			alert("Please select a Lime Light product");
			return false;
		}
		
		return true;
	},
	
	getLimeLightProducts: function() {
		var values = {};
		values.mm_action = "getLimeLightProducts";
		values.campaign_id = jQuery("#limelight_campaign_id").val();
		
		// clear product ID
		lastProductId = jQuery("#limelight_product_id").val();
		jQuery("#limelight_product_id").val("");
		
		// disable UI
		jQuery('#limelight_campaign_id').attr("disabled","disabled");
		jQuery('#limelight_product_display_section').show();
		jQuery('#limelight_product_display_section').html("<em>Loading Lime Light products. Please wait...</em>");
		jQuery('#limelight_select_product_section').hide();
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','limeLightProductsHandler');	
	},
	
	doToggleCampaignSelection: function()
	{ 
		if(jQuery("#limelight_map_all_associated_campaigns").is(":checked"))
		{
			jQuery("#limelight_campaign_id").attr("disabled","disabled");
		}
		else
		{
			jQuery("#limelight_campaign_id").removeAttr("disabled");
		}
	},
	
	limeLightProductsHandler: function(data)
	{
		// enable UI
		jQuery('#limelight_campaign_id').removeAttr("disabled");
		jQuery('#limelight_product_display_section').hide();
		
		if (data.type == 'error')
		{
			alert(data.message);
		}
		else
		{		
			jQuery('#limelight_select_product_section').show();
			jQuery('#limelight_product_id_selector').html(data);
			
			if((lastProductId != "") && (0 != jQuery('#limelight_product_id_selector option[value='+lastProductId+']').length))
			{
				jQuery('#limelight_product_id_selector').val(lastProductId);
			}
			
			jQuery('#limelight_product_id_selector').show();
			mmjs.processForm();
		}
	},
	
	getLimeLightProductDescription: function(productId) {
		var values = {};
		values.mm_action = "getLLProductDescription";
		mmjs.processForm();
		
		if(productId != "")
		{
			values.product_id = productId;
		}
		else
		{
			values.product_id = jQuery("#limelight_product_id").val();
		}
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','llProductDescriptionHandler');	
	},
	
	llProductDescriptionHandler: function(data)
	{	
		alert(data.message);
	},
	
	getMMProductDescription: function() {
		var values = {};
		values.mm_action = "getMMProductDescription";
		values.mm_product_id = jQuery("#mm_product_id").val();
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','mmProductDescriptionHandler');	
	},
	
	mmProductDescriptionHandler: function(data)
	{	
		if (data.type == 'error')
		{
			alert(data.message);
		}
		else
		{		 
			jQuery('#mm_product_description').html(data.message);
		}
	}
});

var lastProductId = "";
var mmjs = new MM_LimeLightProductsViewJS("MM_LimeLightProductsView", "Product Mapping");