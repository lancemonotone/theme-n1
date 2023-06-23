/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_BundlesViewJS = MM_EnhancedDatagridView.extend({
	
	productsLink: "",
	bulkActionKey: "bundle",
	
	setToExpire: function(){
		if(jQuery("#expiry-setting").is(":checked")){
			jQuery("#expires_div").show();
			jQuery("#should-expire").val("1");
		}
		else{
			jQuery("#should-expire").val("0");
			jQuery("#expires_div").hide();	
		}
	},

	storeSearchOptions: function()
	{
		this.module = "MM_BundlesView";
		this.method = "performAction";
		this.action = "module-handle";
			
		var values = {
			mm_action: "storeSearchOptions",
			mm_admin_id: jQuery("#mm-admin-id").val(),
			mm_show_hidden_bundles: "0"
		}
		if(jQuery('#mm-show-hidden-bundles').is(':checked'))
		{ 
			values.mm_show_hidden_bundles = "1";
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
			document.location.href = document.location.href.replace(/#/, '');
		}
	}, 

	hideBundle: function(id,currentStatus)
	{  
	  this.confirmShowHideWithStatusOption(id, currentStatus, true, "Bundle", "bundle"); 
	},
	
	showBundle: function(id,currentStatus)
	{  
	  this.confirmShowHideWithStatusOption(id, currentStatus, false, "Bundle", "bundle"); 
	},
  
	processForm: function()
	{
		// status
		jQuery("#mm-status").attr('value', jQuery('#mm-status-container input:radio:checked').val());
 	  
		// subscribtion type
 	    var subTypeSelection = "free";
 	    
 	    if(jQuery("#subscription-type-paid").is(":checked"))
 	    {
 	    	subTypeSelection = "paid";
 	    }
		
		if(subTypeSelection == 'paid')
 	 	{
		  jQuery("#mm-paid-bundle-settings").show();
 	 	}
		else
		{
		  jQuery("#mm-paid-bundle-settings").hide();
		}
 	    
		jQuery("#mm-subscription-type").attr('value', subTypeSelection);
 	  
		if(subTypeSelection == 'paid') 
		{
			jQuery("#mm-products\\[\\]").removeAttr("disabled");
			jQuery("#mm-products\\[\\]").show();
		} else 
		{
			jQuery("#mm-products\\[\\]").attr("disabled","disabled");
			jQuery("#mm-products\\[\\]").hide();
		}
		
	    jQuery("select[id=mm-products\\[\\]] :disabled").each(function()
	    {
	    	jQuery(this).attr("selected","selected");
	    	jQuery(this).removeAttr("disabled");
	     });
	},
   
	validateForm: function()
	{
		// display name 
		if(jQuery('#mm-display-name').val() == "") {
			alert("Please enter a bundle name");
			return false;
		}
	   
		// subscription type
		if(jQuery("#mm-subscription-type").val() == "paid" && (jQuery("#mm-products\\[\\]").val() == null || jQuery("#mm-products\\[\\]").val() == "")) 
		{
			alert("Please select one or more products or set the bundle type to Free");
			return false;
		}
		
		var autogen_shortname = (jQuery('#autogen_shortname').val() == "true");

		//short name
		if(!autogen_shortname && (jQuery('#mm-short-name').val() == "")){
			alert("Please enter a short name");
			return false;
		}
	   
		return true;
	},
	
	  showPurchaseLinks: function(accessTypeId, accessTypeName, productIds)
	  {	
			var values =  {};
			values.access_type_id = accessTypeId;
			values.access_type_name = accessTypeName;
			values.product_ids = productIds;
			values.mm_action = "showPurchaseLinks";
			
			mmdialog_js.showDialog("mm-purchaselinks-dialog", this.module, 515, 600, "Purchase Links", values);
	  },
		
	  productChangeHandler: function()
	  {	
		  $lastProductId = jQuery("#mm-last-selected-product-id").val();
		  $productId = jQuery("#mm-product-selector").val();
		  
		  if($lastProductId != 0)
		  {
			  jQuery("#mm-purchaselinks-"+$lastProductId).hide(); 
		  }
		  
		  if($productId != 0)
		  {
			  jQuery("#mm-purchaselinks-"+$productId).show(); 
		  }
		  
		  jQuery("#mm-last-selected-product-id").val($productId);
	  },


	renderNameSubscribers: function(value,dataCell,dataObj,grid)
	{
		let nameSpan = grid.ce(dataCell,"span");
		nameSpan.title = value;
		let nameNode = document.createTextNode(mmjs.abbrevString(value));
		nameSpan.appendChild(nameNode);
		
		let subscriberEl = grid.ce(dataCell,"p")
		let memberCount = (dataObj.member_count && (Number(dataObj.member_count) > 0)) ? Number(dataObj.member_count) : 0;
		let icon = (memberCount > 0) ? grid.getImage("subscribers") : grid.getImage("no-subscribers");
		let manageMembersLink = mmjs.manageMembersLink + dataObj.id;
		
		subscriberEl.innerHTML = icon;
		if (memberCount > 0)
		{
			let mmLinkEl = grid.ce(subscriberEl,"a");
			mmLinkEl.href = manageMembersLink;
			mmLinkEl.innerHTML = memberCount + " " + grid.tr("Members"); //TODO: add this to translation list
		}
		else
		{
			let noSubscribers = document.createTextNode(grid.tr("No Subscribers"));
			subscriberEl.appendChild(noSubscribers);
		}
	},


	renderType: function(value,dataCell,dataObj,grid)
	{
		return (Number(value) == 1) ? { "images":"type-free" } : { "images":"type-paid" };
	},
	
	
	renderProducts: function(value,dataCell,dataObj,grid)
	{
		if ((dataObj.is_free != 1) && (dataObj.products))//product associations for paid membership
		{
			let productLinks = dataObj.products.reduce((acc,product) => {
				acc += (acc == "") ? "" : ", ";
				acc += `<a role="button" href="${mmjs.productsLink}${product.id}">${product.name}</a>`;
				return acc;
			},"");
			if (productLinks == "")
			{
				return "&mdash;";
			}
			dataCell.innerHTML = `${grid.getImage('shopping-cart')} ${productLinks}`;
		}
		else //free membership
		{
			return "&mdash;";
		}
	},
	
	renderPurchaseLinks: function(value,dataCell,dataObj,grid)
	{
		let purchaseLink = document.createElement("a");
		purchaseLink.title = grid.tr("Get purchase links");
		purchaseLink.setAttribute("role","button");
		purchaseLink.setAttribute("data-bundle-id",dataObj.id);
		purchaseLink.setAttribute("data-product-ids",dataObj.products.reduce((acc,item) => {
			return acc += ((acc == "") ? `${item.id}` : `,${item.id}`);
		},""));
		purchaseLink.setAttribute("data-bundle-name",mmjs.htmlentities(dataObj.name));
		purchaseLink.className = "mm-ui-button mm-bundle-purchase-links";
		purchaseLink.innerHTML = grid.getImage("purchase-link");
		dataCell.appendChild(purchaseLink);
	},
	
	
	renderActions: function(value,dataCell,dataObj,grid)
	{
		let editAction = mmjs.createImageButton(grid.getImage("edit"),"mm-bundle-edit",{"data-bundle-id" : dataObj.id});
		
		let deleteAction = "";
		if ((Number(dataObj.has_associations) == 0) && (Number(dataObj.member_count) <= 0))
		{
			deleteAction = mmjs.createImageButton(grid.getImage("delete"),"mm-bundle-delete",{"data-bundle-id" : dataObj.id});
		}
		else
		{
			deleteAction = document.createElement("span");
			deleteAction.innerHTML = grid.getImage("cant-delete");
		}
		
		let showHideAction = "";
		if (Number(dataObj.is_hidden) != 1)
		{
			showHideAction = mmjs.createImageButton(grid.getImage("hide"),"mm-bundle-hide",
												 {"data-bundle-id" : dataObj.id,
												  "data-bundle-status" : dataObj.status});
		}
		else
		{
			showHideAction = mmjs.createImageButton(grid.getImage("unhide"),"mm-bundle-show",
												 {"data-bundle-id" : dataObj.id,
												  "data-bundle-status" : dataObj.status});
		}
		let duplicateAction = mmjs.createImageButton(grid.getImage("duplicate"),"mm-bundle-duplicate",{"data-bundle-id" : dataObj.id});
		
		dataCell.className = "mm-ehd-table-cell-nowrap";
		dataCell.appendChild(editAction);
		dataCell.appendChild(deleteAction);
		if (showHideAction)
		{
			dataCell.appendChild(showHideAction);
		}
		dataCell.appendChild(duplicateAction);
	},

	 renderGrid: function(imageList,translationList)
	 {
		jQuery(document).ready(function() {
			let gridConfig = { "columns" : [ { "id": "id", "name":"ID", "sortable":true, "searchType":"numeric", "defaultSortType":"DESC" },
											 { "id": "name", "name":"Name / Subscribers", "sortable":true, "searchType":"text", "defaultSearchField":true, "render":mmjs.renderNameSubscribers },
											 { "id": "is_free", "name":"Type", "sortable":true, "render":mmjs.renderType },
											 { "id": "products", "name":"Products", "render":mmjs.renderProducts },
											 { "id": "purchase_links", "name":"Purchase Links", "render":mmjs.renderPurchaseLinks },
											 { "id": "status", "name":"Status", "sortable":true, "render":mmjs.renderStatus },
											 { "id": "actions", "name":"Actions", "render":mmjs.renderActions }
											],
							   "version"    : 1, //MUST increment this every time the column definitions change!
							   "datasource" : mmjs.search.bind(mmjs) 
				};
			grid = new MM_EnhancedDatagridJS(document.getElementById("gridHolder"),"bundle",gridConfig);
			if (imageList)
			{
				grid.setImageReferences(imageList);
			}
			if (translationList)
			{
				grid.setTranslationObject(translationList);
			}
			grid.bulkAdminFunction = mmjs.bulkopAdminister.bind(mmjs);
			grid.render();
			mmjs.setGrid(grid);
		});
	 },

	 bindEventListeners: function()
	 {
	 	mmjs.bindByClassName("#gridHolder","click",".mm-bundle-purchase-links",['bundleId','bundleName','productIds'],mmjs.showPurchaseLinks.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-bundle-edit",['bundleId'],(bundleId) => {
			mmjs.edit.call(mmjs,'mm-bundles-dialog', bundleId);
		});
		mmjs.bindByClassName("#gridHolder","click",".mm-bundle-show",['bundleId','bundleArchived'],mmjs.showBundle.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-bundle-hide",['bundleId','bundleArchived'],mmjs.hideBundle.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-bundle-delete",['bundleId'],mmjs.remove.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-bundle-duplicate",['bundleId'],(bundleId) => {
			mmjs.duplicate.call(mmjs,'mm-bundles-dialog', bundleId);
		});
	 }	
});

var mmjs = new MM_BundlesViewJS("MM_BundlesView", "Bundle");