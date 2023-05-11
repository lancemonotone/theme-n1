/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_StickyioProductsViewJS = MM_Core.extend({
  
	processForm: function()
	{   
		//jQuery("#stickyio_campaign_name").attr('value', jQuery("#stickyio_campaign_id :selected").text());
		
	    var selectedNames = jQuery("#stickyio_campaign_id option:selected").map(function () {
	        return jQuery(this).text();
	    }).get().join('|');
	    
	    jQuery("#stickyio_campaign_name").attr('value', selectedNames);
	    
	    if(jQuery("#stickyio_map_all_associated_campaigns").is(":checked"))
	    {
	    	jQuery("#stickyio_campaign_map_all").val("1");
	    }
	    else
	    {
	    	jQuery("#stickyio_campaign_map_all").val("0");
	    }
		
		if(jQuery("#stickyio_product_id_selector").is(":visible"))
		{
			jQuery("#stickyio_product_id").attr('value', jQuery("#stickyio_product_id_selector").val());
			jQuery("#stickyio_product_name").attr('value', jQuery("#stickyio_product_id_selector :selected").text());
		}
		
		if(jQuery("#stickyio_offer_id_selector").is(":visible"))
		{
			jQuery("#stickyio_offer_id").attr('value', jQuery("#stickyio_offer_id_selector").val());
			jQuery("#stickyio_offer_name").attr('value', jQuery("#stickyio_offer_id_selector :selected").text());
		}
		
		if(jQuery("#stickyio_billing_model_id_selector").is(":visible"))
		{
			jQuery("#stickyio_billing_model_id").attr('value', jQuery("#stickyio_billing_model_id_selector").val());
			jQuery("#stickyio_billing_model_name").attr('value', jQuery("#stickyio_billing_model_id_selector :selected").text());
		}
	},
	
	validateForm: function()
	{   
		if(jQuery("#stickyio_product_id").val() == 0 || jQuery("#stickyio_product_id").val() == "")
		{
			alert("Please select a Lime Light product");
			return false;
		}
		
		return true;
	},
 
	saveStickyioProduct: function()
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
	
	getStickyioOffers: function() {
		var values = {};
		values.mm_action = "getStickyioOffers";
		values.campaign_id = jQuery("#stickyio_campaign_id").val();
		
		// clear product ID
		lastOfferId = jQuery("#stickyio_offer_id").val();
		jQuery("#stickyio_offer_id").val("");
		
		// disable UI
		jQuery('#stickyio_campaign_id').attr("disabled","disabled");
		jQuery('#stickyio_offer_display_section').show();
		jQuery('#stickyio_offer_display_section').html("<em>Loading Lime Light offers. Please wait...</em>");
		jQuery('#stickyio_select_offer_section').hide();
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','limeLightOfferHandler');	
	},
	
	limeLightOfferHandler: function(data)
	{
		// enable UI
		jQuery('#stickyio_campaign_id').removeAttr("disabled");
		jQuery('#stickyio_offer_display_section').hide();
		
		if (data.type == 'error')
		{
			alert(data.message);
		}
		else
		{		
			jQuery("#offer_row").show();
			jQuery('#stickyio_select_offer_section').show();
			jQuery('#stickyio_offer_id_selector').html(data);
			
			if((lastOfferId != "") && (0 != jQuery('#stickyio_offer_id_selector option[value='+lastOfferId+']').length))
			{
				jQuery('#stickyio_offer_id_selector').val(lastOfferId);
			}
			
			jQuery('#stickyio_offer_id_selector').show();
			lastOfferId = jQuery('#stickyio_offer_id_selector').val();
			
			mmjs.processForm(); 
			mmjs.getStickyioBillingModels();
		}
	}, 

	getStickyioBillingModelsFromOffer: function() {
		lastOfferId = jQuery('#stickyio_offer_id_selector').val(); 
		mmjs.getStickyioBillingModels(); 
	},
	
	getStickyioBillingModels: function() {
		var values = {};
		values.mm_action = "getStickyioBillingModels"; 
		values.offer_id = lastOfferId;
		
		// clear product ID
		lastBillingModelId = jQuery("#stickyio_billing_model_id").val();
		jQuery("#stickyio_billing_model_id").val("");
		
		// disable UI
		jQuery('#stickyio_campaign_id').attr("disabled","disabled");
		jQuery('#stickyio_billing_model_display_section').show();
		jQuery('#stickyio_billing_model_display_section').html("<em>Loading Lime Light billing models. Please wait...</em>");
		jQuery('#stickyio_select_billing_model_section').hide();
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','limeLightBillingModelHandler');	
	},
	
	limeLightBillingModelHandler: function(data)
	{
		// enable UI
		jQuery('#stickyio_campaign_id').removeAttr("disabled");
		jQuery('#stickyio_billing_model_display_section').hide();
		
		if (data.type == 'error')
		{
			alert(data.message);
		}
		else
		{		
			jQuery("#billing_model_row").show();
			jQuery('#stickyio_select_billing_model_section').show();
			jQuery('#stickyio_billing_model_id_selector').html(data);
			
			if((lastBillingModelId != "") && (0 != jQuery('#stickyio_billing_model_id_selector option[value='+lastBillingModelId+']').length))
			{
				jQuery('#stickyio_billing_model_id_selector').val(lastBillingModelId);
			}
			
			jQuery('#stickyio_billing_model_id_selector').show();
			lastBillingModelId = jQuery('#stickyio_billing_model_id_selector').val();
			
			mmjs.processForm(); 
			mmjs.getStickyioProducts();
		}
	},
	
	getStickyioProducts: function() {
		var values = {};
		values.mm_action = "getStickyioProducts";
		values.campaign_id = jQuery("#stickyio_campaign_id").val();
		values.offer_id = lastOfferId;
		// clear product ID
		lastProductId = jQuery("#stickyio_product_id").val();
		jQuery("#stickyio_product_id").val("");
		
		// disable UI
		jQuery('#stickyio_campaign_id').attr("disabled","disabled");
		jQuery('#stickyio_product_display_section').show();
		jQuery('#stickyio_product_display_section').html("<em>Loading Lime Light products. Please wait...</em>");
		jQuery('#stickyio_select_product_section').hide();
		
		var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	    
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','limeLightProductsHandler');	
	},
	
	doToggleCampaignSelection: function()
	{ 
		if(jQuery("#stickyio_map_all_associated_campaigns").is(":checked"))
		{
			jQuery("#stickyio_campaign_id").attr("disabled","disabled");
		}
		else
		{
			jQuery("#stickyio_campaign_id").removeAttr("disabled");
		}
	},
	
	limeLightProductsHandler: function(data)
	{
		// enable UI
		jQuery('#stickyio_campaign_id').removeAttr("disabled");
		jQuery('#stickyio_product_display_section').hide();
		
		if (data.type == 'error')
		{
			alert(data.message);
		}
		else
		{		
			jQuery("#product_row").show();
			jQuery('#stickyio_select_product_section').show();
			jQuery('#stickyio_product_id_selector').html(data);
			
			if((lastProductId != "") && (0 != jQuery('#stickyio_product_id_selector option[value='+lastProductId+']').length))
			{
				jQuery('#stickyio_product_id_selector').val(lastProductId);
			}
			
			jQuery('#stickyio_product_id_selector').show();
			mmjs.processForm();
		}
	},
	
	getStickyioProductDescription: function(productId) {
		var values = {};
		values.mm_action = "getLLProductDescription";
		mmjs.processForm();
		
		if(productId != "")
		{
			values.product_id = productId;
		}
		else
		{
			values.product_id = jQuery("#stickyio_product_id").val();
		}
		values.offer_id = jQuery('#stickyio_offer_id_selector').val();
		  
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
var mmjs = new MM_StickyioProductsViewJS("MM_StickyioProductsView", "Product Mapping");