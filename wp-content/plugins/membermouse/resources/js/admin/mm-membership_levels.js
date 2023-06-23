/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_MembershipLevelsViewJS = MM_EnhancedDatagridView.extend({
	
	manageMembersLink: "",
	productsLink: "",
	bundlesLink: "",
	bulkActionKey: "membershiplevel",
	
	setToExpire: function(){
		if(jQuery("#expiry-setting").is(":checked")){
			jQuery("#expires_div").show();
			jQuery("#expiry_chk").val("1");
		}
		else{
			jQuery("#expiry_chk").val("0");
			jQuery("#expires_div").hide();	
		}
	},
	  
	storeSearchOptions: function()
	{
		this.module = "MM_MembershipLevelsView";
		this.method = "performAction";
		this.action = "module-handle";
			
		var values = {
			mm_action: "storeSearchOptions",
			mm_admin_id: jQuery("#mm-admin-id").val(),
			mm_show_hidden_membership_levels: "0"
		}
			
		if(jQuery('#mm-show-hidden-membership-levels').is(':checked'))
		{
			values.mm_show_hidden_membership_levels = "1";
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

	hideMembershipLevel: function(id,currentStatus)
	{  
	  this.confirmShowHideWithStatusOption(id, currentStatus, true, "Membership Level", "membershiplevel"); 
	},
	
	showMembershipLevel: function(id,currentStatus)
	{  
	  this.confirmShowHideWithStatusOption(id, currentStatus, false, "Membership Level", "membershiplevel"); 
	}, 
	
	setDefault: function(id)
	{
		var doSet = confirm("Are you sure you want to set this membership level as the default?");
	    
	    if(doSet)
	    {
	        var values = {
	            id:id,
	            mm_action: "setDefault"
	        };

	        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	        ajax.send(values, false, 'mmjs',this.updateHandler); 
	    }
	},
	
	welcomeEmailChanged: function()
	{
		if(!jQuery("#mm-welcome-email-enabled-field").is(":checked"))
		{
			jQuery("#mm-welcome-email-row").hide();
			jQuery("#mm-welcome-email-enabled").val("0");
		}
		else{
			jQuery("#mm-welcome-email-row").show();
			jQuery("#mm-welcome-email-enabled").val("1");
		}
	},
	
	filterRegistrationProducts: function()
	{
	    var selected = jQuery("#mm-default-product-id").val();
		jQuery("#mm-default-product-id").find('option').remove().end();
		
		var options = new Array();
	    jQuery("select[id='mm-products[]'] :selected").each(function()
	    {
		    	jQuery("#mm-default-product-id").append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
	    });
	    
	    
	    jQuery("select[id='mm-products[]'] :disabled").each(function()
	    {
			var val = jQuery(this).val();
			if(!jQuery.inArray(val, options))
			{
				jQuery("#mm-default-product-id").append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
			}
	    });
	    
	    if(jQuery("select#mm-default-product-id option").length > 0)
	    {
	    	if(selected)
	    	{
	    		jQuery("#mm-default-product-id").val(selected);
	    	}
	    }
	    else
	    {
	    	jQuery("#mm-default-product-id").append("<option value=''>Select a product</option>");
	    }
	},
	
  processForm: function()
  {	
 	  // status
 	  jQuery("#mm-status").attr('value', jQuery('#mm-status-container input:radio:checked').val());
 	  
 	  // subscribtion type
 	  var subTypeSelection = jQuery('#mm-subscription-container input:radio:checked').val();

 	  jQuery("#mm-subscription-type").attr('value', subTypeSelection);
 	  
 	  if(subTypeSelection == 'paid' && jQuery("#mm-has-associations").val() == "no") 
 	  {
 		  jQuery("#mm-products\\[\\]").removeAttr("disabled");
 		  jQuery("#mm-default-product-id").removeAttr("disabled");
 	  } 
 	  
 	  if(subTypeSelection == 'paid')
 	  {
		  jQuery("#mm-paid-membership-settings").show();
 		  jQuery("#mm-free-membership-settings").hide();
 	  }
 	  else
 	  {
		  jQuery("#mm-paid-membership-settings").hide();
 		  jQuery("#mm-free-membership-settings").show();
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
		   this._alert("Please enter a membership level name");
		   return false;
	   }
	   
	   // subscription type
	   if(jQuery("#mm-subscription-type").val() == "paid" && (jQuery("#mm-products\\[\\]").val() == null || jQuery("#mm-products\\[\\]").val() == "")) 
	   {
		   this._alert("Please select one or more products or set the membership type to Free");
		   return false;
	   }
	   
	   if(jQuery("#mm-welcome-email-enabled-field").is(":checked"))
	   {
		   // email subject
		   if(jQuery("#mm-email-subject").val() == "") {
			   this._alert("Please enter a subject for the welcome email");
			   return false;
		   }
		   
		   // email body
		   if(jQuery("#mm-email-body").val() == "") {
			   this._alert("Please enter a body for the welcome email");
			   return false;
		   }
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

  renderGrid: function(imageList,translationList)
  {
		jQuery(document).ready(function() {
			let gridConfig = { "columns" : [ { "id": "id", "name":"ID", "sortable":true, "searchType":"numeric", "defaultSortType":"DESC" },
											 { "id": "name", "name":"Name / Subscribers", "sortable":true, "searchType":"text", "defaultSearchField":true, "render":mmjs.renderNameSubscribers },
											 { "id": "is_free", "name":"Type", "sortable":true, "render":mmjs.renderType },
											 { "id": "products", "name":"Products", "render":mmjs.renderProducts },
											 { "id": "bundles", "name":"Bundles", "render":mmjs.renderBundles },
											 { "id": "purchase_links", "name":"Purchase Links", "render":mmjs.renderPurchaseLinks },
											 { "id": "status", "name":"Status", "sortable":true, "render":mmjs.renderStatus },
											 { "id": "actions", "name":"Actions", "render":mmjs.renderActions }
											],
							   "version"    : 1, //MUST increment this every time the column definitions change!
							   "datasource" : mmjs.search.bind(mmjs) 
				};
			grid = new MM_EnhancedDatagridJS(document.getElementById("gridHolder"),"membership_levels",gridConfig);
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

	renderType: function(value,dataCell,dataObj,grid)
	{
		return (Number(value) == 1) ? { "images":"type-free" } : { "images":"type-paid" };
	},

	renderNameSubscribers: function(value,dataCell,dataObj,grid)
	{
		let defaultFlagSpan = document.createElement("span");
		if (Number(dataObj.is_default) == 1)
		{
			defaultFlagSpan.innerHTML = grid.getImage("default-flag");
		}
		else if ((Number(dataObj.status) == 1) && (Number(dataObj.is_free) == 1) && (Number(dataObj.is_hidden) != 1))
		{
			let defaultFlagLink = grid.ce(defaultFlagSpan,"a");
			defaultFlagLink.setAttribute("role","button");
			defaultFlagLink.classList.add("mm-membership-level-non-default-flag");
			defaultFlagLink.setAttribute("data-membership-level-id",dataObj.id);
			defaultFlagLink.innerHTML = grid.getImage("set-default-flag");
		}
		else
		{
			defaultFlagSpan.innerHTML = grid.getImage("clear");
		}
		
		dataCell.appendChild(defaultFlagSpan);
		let nameNode = document.createTextNode(value);
		dataCell.appendChild(nameNode);
		
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
	
	renderPurchaseLinks: function(value,dataCell,dataObj,grid)
	{
		let purchaseLink = document.createElement("a");
		purchaseLink.title = grid.tr("Get purchase links");
		purchaseLink.setAttribute("role","button");
		purchaseLink.setAttribute("data-membership-level-id",dataObj.id);
		purchaseLink.setAttribute("data-product-ids",dataObj.products.reduce((acc,item) => {
			return acc += ((acc == "") ? `${item.id}` : `,${item.id}`);
		},""));
		purchaseLink.setAttribute("data-membership-level-name",mmjs.htmlentities(dataObj.name));
		purchaseLink.className = "mm-ui-button mm-membership-level-purchase-links";
		purchaseLink.innerHTML = grid.getImage("purchase-link");
		dataCell.appendChild(purchaseLink);
	},
	
	
	renderProducts: function(value,dataCell,dataObj,grid)
	{
		if ((dataObj.is_free != 1) && (dataObj.products))//product associations for paid membership
		{
			let productLinks = dataObj.products.reduce((acc,product) => {
				acc += (acc == "") ? "" : ", ";
				acc += `<a href="${mmjs.productsLink}${product.id}">${product.name}</a>`;
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
	
	
	renderActions: function(value,dataCell,dataObj,grid)
	{
		let editAction = mmjs.createImageButton(grid.getImage("edit"),"mm-membership-level-edit",{"data-membership-level-id" : dataObj.id});
		
		let deleteAction = "";
		if ((Number(dataObj.has_associations) == 0) && (Number(dataObj.member_count) <= 0) && (Number(dataObj.is_hidden) != 1))
		{
			deleteAction = mmjs.createImageButton(grid.getImage("delete"),"mm-membership-level-delete",{"data-membership-level-id" : dataObj.id});
		}
		else
		{
			deleteAction = document.createElement("span");
			deleteAction.innerHTML = grid.getImage("cant-delete");
		}
		
		let showHideAction = "";
		if (Number(dataObj.is_default) != 1)
		{
			if (Number(dataObj.is_hidden) != 1)
			{
				showHideAction = mmjs.createImageButton(grid.getImage("hide"),"mm-membership-level-hide",
													 {"data-membership-level-id" : dataObj.id,
													  "data-membership-level-status" : dataObj.status});
			}
			else
			{
				showHideAction = mmjs.createImageButton(grid.getImage("unhide"),"mm-membership-level-show",
													 {"data-membership-level-id" : dataObj.id,
													  "data-membership-level-status" : dataObj.status});
			}
		}
		
		let duplicateAction = mmjs.createImageButton(grid.getImage("duplicate"),"mm-membership-level-duplicate",{"data-membership-level-id" : dataObj.id});
		
		dataCell.className = "mm-ehd-table-cell-nowrap";
		dataCell.appendChild(editAction);
		dataCell.appendChild(deleteAction);
		if (showHideAction)
		{
			dataCell.appendChild(showHideAction);
		}
		dataCell.appendChild(duplicateAction);
	},
	
	
	renderBundles: function(value,dataCell,dataObj,grid)
	{
		if (dataObj.bundles)
		{
			if (dataObj.bundles.length == 0)
			{
				return "&mdash;";
			}
			
			let df = document.createDocumentFragment();
			
			let bundleIconSpan = document.createElement("span");
			bundleIconSpan.style.marginRight = "5px";
			bundleIconSpan.innerHTML = grid.getImage("bundle");
			df.appendChild(bundleIconSpan);
			
			let bundleLinkSpan = document.createElement("span");
			let first = true;
			dataObj.bundles.forEach( (bundle) => {
				first = (first === true) ? false : bundleLinkSpan.appendChild(document.createTextNode(", "));
				let bundleLink = document.createElement("a");
				bundleLink.setAttribute("role","button");
				bundleLink.href = `${mmjs.bundlesLink}${bundle.id}`;
				let bundleLinkText = document.createTextNode(bundle.name);
				bundleLink.appendChild(bundleLinkText);
				bundleLinkSpan.appendChild(bundleLink);				
			});
			df.appendChild(bundleLinkSpan);
			dataCell.appendChild(df);
		}
		else
		{
			return "&mdash;";
		}
	},
	
	
	bindEventListeners: function()
	{
		//purchase link listener
		mmjs.bindByClassName("#gridHolder","click",".mm-membership-level-purchase-links",['membershipLevelId','membershipLevelName','productIds'],mmjs.showPurchaseLinks.bind(mmjs));
		
		//default flag listener
		mmjs.bindByClassName("#gridHolder","click",".mm-membership-level-non-default-flag",['membershipLevelId'],mmjs.setDefault.bind(mmjs));
		
		//action buttons
		mmjs.bindByClassName("#gridHolder","click",".mm-membership-level-edit",['membershipLevelId'],(mlId) => {
			mmjs.edit.call(mmjs,'mm-member-types-dialog', mlId);
		});
		
		mmjs.bindByClassName("#gridHolder","click",".mm-membership-level-show",['membershipLevelId','membershipLevelArchived'],mmjs.showMembershipLevel.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-membership-level-hide",['membershipLevelId','membershipLevelArchived'],mmjs.hideMembershipLevel.bind(mmjs));
		
		mmjs.bindByClassName("#gridHolder","click",".mm-membership-level-delete",['membershipLevelId'],mmjs.remove.bind(mmjs));
		
		mmjs.bindByClassName("#gridHolder","click",".mm-membership-level-duplicate",['membershipLevelId'],(mlId) => {
			mmjs.duplicate.call(mmjs,'mm-member-types-dialog', mlId);
		});

	}
	
});

var mmjs = new MM_MembershipLevelsViewJS("MM_MembershipLevelsView", "Membership Level");
