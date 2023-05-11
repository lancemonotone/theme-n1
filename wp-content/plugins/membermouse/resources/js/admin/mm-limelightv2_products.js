/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_LimeLightv2ProductsViewJS = MM_Core.extend({
  
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
		
		if(jQuery("#limelight_offer_id_selector").is(":visible"))
		{
			jQuery("#limelight_offer_id").attr('value', jQuery("#limelight_offer_id_selector").val());
			jQuery("#limelight_offer_name").attr('value', jQuery("#limelight_offer_id_selector :selected").text());
		}
		
		if(jQuery("#limelight_billing_model_id_selector").is(":visible"))
		{
			jQuery("#limelight_billing_model_id").attr('value', jQuery("#limelight_billing_model_id_selector").val());
			jQuery("#limelight_billing_model_name").attr('value', jQuery("#limelight_billing_model_id_selector :selected").text());
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
 
	saveLimeLightProduct: function()
	{  
		this.save(undefined, undefined);
	},
	

	dataUpdateHandler: function(data)
	{ 
	  if(data.type == "error")
	  {
		  if(data.message.length > 0)
		  {  
			  if(data.message.startsWith("Warning"))
			  {
				  var cnf = confirm(data.message+"Would you like to continue anyway?");
				  if(cnf)
				  {
					  var params = {};
					  params["force"] = 1;
					  this.save(null,params);
				  }
				  else
				  {
					  return false;
				  } 
			  }
			  else
			  {
				  this._alert(data.message); 
				  return false;
			  }
		  }
	  }
	  else {
		  if(data.message != undefined && data.message.length > 0)
		  {
			  this._alert(data.message);
		  } 

		  var values = {
			    sortBy: this.sortBy,
			    sortDir: this.sortDir,
			    crntPage: this.crntPage,
			    resultSize: this.resultSize,
			    v2:"1",
			    mm_action: "refreshView"
			};
			var ajax = new MM_Ajax(false, this.module, this.action, this.method);
			ajax.send(values, false, 'mmjs','refreshViewCallback'); 
		  
		  this.closeDialog();
	  }
	},
	
	getLimeLightOffers: function() {
		var values = {};
		values.mm_action = "getLimeLightOffers";
		values.campaign_id = jQuery("#limelight_campaign_id").val();
		
		// clear product ID
		lastOfferId = jQuery("#limelight_offer_id").val();
		jQuery("#limelight_offer_id").val("");
		
		// disable UI
		jQuery('#limelight_campaign_id').attr("disabled","disabled");
		jQuery('#limelight_offer_display_section').show();
		jQuery('#limelight_offer_display_section').html("<em>Loading Lime Light offers. Please wait...</em>");
		jQuery('#limelight_select_offer_section').hide();
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','limeLightOfferHandler');	
	},
	
	limeLightOfferHandler: function(data)
	{
		// enable UI
		jQuery('#limelight_campaign_id').removeAttr("disabled");
		jQuery('#limelight_offer_display_section').hide();
		
		if (data.type == 'error')
		{
			alert(data.message);
		}
		else
		{		
			jQuery("#offer_row").show();
			jQuery('#limelight_select_offer_section').show();
			jQuery('#limelight_offer_id_selector').html(data);
			
			if((lastOfferId != "") && (0 != jQuery('#limelight_offer_id_selector option[value='+lastOfferId+']').length))
			{
				jQuery('#limelight_offer_id_selector').val(lastOfferId);
			}
			
			jQuery('#limelight_offer_id_selector').show();
			lastOfferId = jQuery('#limelight_offer_id_selector').val();
			
			mmjs.processForm(); 
			mmjs.getLimeLightBillingModels();
		}
	}, 

	getLimeLightBillingModelsFromOffer: function() {
		lastOfferId = jQuery('#limelight_offer_id_selector').val(); 
		mmjs.getLimeLightBillingModels(); 
	},
	
	getLimeLightBillingModels: function() {
		var values = {};
		values.mm_action = "getLimeLightBillingModels"; 
		values.offer_id = lastOfferId;
		
		// clear product ID
		lastBillingModelId = jQuery("#limelight_billing_model_id").val();
		jQuery("#limelight_billing_model_id").val("");
		
		// disable UI
		jQuery('#limelight_campaign_id').attr("disabled","disabled");
		jQuery('#limelight_billing_model_display_section').show();
		jQuery('#limelight_billing_model_display_section').html("<em>Loading Lime Light billing models. Please wait...</em>");
		jQuery('#limelight_select_billing_model_section').hide();
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','limeLightBillingModelHandler');	
	},
	
	limeLightBillingModelHandler: function(data)
	{
		// enable UI
		jQuery('#limelight_campaign_id').removeAttr("disabled");
		jQuery('#limelight_billing_model_display_section').hide();
		
		if (data.type == 'error')
		{
			alert(data.message);
		}
		else
		{		
			jQuery("#billing_model_row").show();
			jQuery('#limelight_select_billing_model_section').show();
			jQuery('#limelight_billing_model_id_selector').html(data);
			
			if((lastBillingModelId != "") && (0 != jQuery('#limelight_billing_model_id_selector option[value='+lastBillingModelId+']').length))
			{
				jQuery('#limelight_billing_model_id_selector').val(lastBillingModelId);
			}
			
			jQuery('#limelight_billing_model_id_selector').show();
			lastBillingModelId = jQuery('#limelight_billing_model_id_selector').val();
			
			mmjs.processForm(); 
			mmjs.getLimeLightProducts();
		}
	},
	
	getLimeLightProducts: function() {
		var values = {};
		values.mm_action = "getLimeLightProducts";
		values.campaign_id = jQuery("#limelight_campaign_id").val();
		values.offer_id = lastOfferId;
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
			jQuery("#product_row").show();
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
	},
	

	  create: function(dialogId, width, height)
	  { 
		  mmdialog_js.showDialog(dialogId, this.module, width, height, "Create "+this.entityName, {'v2':'1'});
	  }, 
});

var lastBillingModelId = "";
var lastProductId = "";
var lastOfferId = "";
var mmjs = new MM_LimeLightv2ProductsViewJS("MM_LimeLightProductsView", "Product Mapping");