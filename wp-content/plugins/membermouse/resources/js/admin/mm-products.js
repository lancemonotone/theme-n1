/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_ProductViewJS = MM_EnhancedDatagridView.extend({
	
	membershipLevelLink: false,
	bundleLink: false,
	bulkActionKey: "product",
	
	enableLimitPayments: function()
	{
		if(jQuery("#mm-limit_payments").is(":checked"))
		{
			jQuery("#mm-number_of_payments").removeAttr("disabled");
			jQuery("#mm-do_limit_payments").val(1);
			jQuery("#mm-number_of_payments").val("");
			jQuery("#mm-number_of_payments").focus();
		}
		else
		{
			jQuery("#mm-number_of_payments").attr("disabled","disabled");
			jQuery("#mm-do_limit_payments").val(0);
			jQuery("#mm-number_of_payments").val(0);
		}
	},
	  
	storeSearchOptions: function()
	{
		this.module = "MM_ProductView";
		this.method = "performAction";
		this.action = "module-handle";
			
		var values = {
			mm_action: "storeSearchOptions",
			mm_admin_id: jQuery("#mm-admin-id").val(),
			mm_show_hidden_products: "0"
		}
		if(jQuery('#mm-show-hidden-products').is(':checked'))
		{
			values.mm_show_hidden_products = "1";
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
	  
	hideProduct: function(id, currentStatus)
	{  
		this.confirmShowHideWithStatusOption(id, currentStatus, true, "Product", "product"); 
	},
	
	showProduct: function(id, currentStatus)
	{  
		this.confirmShowHideWithStatusOption(id, currentStatus, false, "Product", "product"); 
	},
	
	getMMProductDescription: function() {
		var values = {};
		values.mm_action = "getMMProductDescription";
		values.mm_product_id = jQuery("#mm-trial_alternate_product").val();
		
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
			jQuery('#mm_alt_product_description').html(data.message);
		}
	},
	
	enableLimitTrial: function()
	{
		if(jQuery("#mm-limit_trial").is(":checked"))
		{
			jQuery("#mm-limit_trial_alt_product").removeAttr("disabled");
			jQuery("#mm-do_limit_trial").val(1);
			jQuery("#mm_limit_trial_row").show();
		}
		else
		{
			jQuery("#mm-limit_trial_alt_product").attr("disabled","disabled");
			jQuery("#mm-do_limit_trial").val(0);
			jQuery("#mm_limit_trial_row").hide();
		}
	},
	
	processForm: function()
	{	
		// status
	 	jQuery("#mm-status").attr('value', jQuery('#mm-status-container input:radio:checked').val());
	},
	
	accessChangeHandler: function()
	{	
		jQuery("#mm-associated-access-value").attr('value', jQuery('#mm-access-container input:radio:checked').val());
		
		if(jQuery("#mm-associated-access-value").val() == "membership") 
		{
			jQuery("#mm-membership-access-container").show();
			jQuery("#mm-bundle-access-container").hide();
		}
		else if(jQuery("#mm-associated-access-value").val() == "bundle") 
		{
			jQuery("#mm-membership-access-container").hide();
			jQuery("#mm-bundle-access-container").show();
		}
		else
		{
			jQuery("#mm-membership-access-container").hide();
			jQuery("#mm-bundle-access-container").hide();
		}
	},
	
	saveProduct: function()
	{
		jQuery("#mm-associated-access-value").attr('value', jQuery('#mm-access-container input:radio:checked').val());
		
		var params = {};
		this.save(undefined, params);
	},
	
	validateForm: function()
	{
		if(jQuery("#mm-name").val() == "")
		{
			alert("Please enter a name");
			jQuery("#mm-name").focus();
			return false;
		}
		if(jQuery("#mm-price").val() == "")
		{
			alert("Please enter a price");
			jQuery("#mm-price").focus();
			return false;
		}
		
		// validate trial period
		if(jQuery("#mm-has_trial_val").val() == "1")
		{	
			if(jQuery("#mm-trial_duration").val() == "" || parseInt(jQuery("#mm-trial_duration").val()) == 0)
			{
				alert("The trial period must be greater than 0");
				jQuery("#mm-trial_duration").focus();
				return false;
			}
			
			switch(jQuery("#mm-trial_frequency").val())
			{
				case "days":
					if(parseInt(jQuery("#mm-trial_duration").val()) < 1 || parseInt(jQuery("#mm-trial_duration").val()) > 90)
					{
						alert("The trial period must be between 1 and 90 days");
						jQuery("#mm-trial_duration").focus();
						return false;
					}
					break;
				
				case "weeks":
					if(parseInt(jQuery("#mm-trial_duration").val()) < 1 || parseInt(jQuery("#mm-trial_duration").val()) > 52)
					{
						alert("The trial period must be between 1 and 52 weeks");
						jQuery("#mm-trial_duration").focus();
						return false;
					}
					break;
					
				case "months":
					if(parseInt(jQuery("#mm-trial_duration").val()) < 1 || parseInt(jQuery("#mm-trial_duration").val()) > 12)
					{
						alert("The trial period must be between 1 and 12 months");
						jQuery("#mm-trial_duration").focus();
						return false;
					}
					break;
					
				case "years":
					if(parseInt(jQuery("#mm-trial_duration").val()) != 1)
					{
						alert("The trial period must be 1 year");
						jQuery("#mm-trial_duration").focus();
						return false;
					}
					break;
			}
		}
		
		// validate rebill period
		if(jQuery("#mm-is_recurring_val").val() == "1")
		{	
			if(jQuery("#mm-rebill_period").val() == "" || parseInt(jQuery("#mm-rebill_period").val()) == 0)
			{
				alert("The rebill period must be greater than 0");
				jQuery("#mm-rebill_period").focus();
				return false;
			}
			
			switch(jQuery("#mm-rebill_frequency").val())
			{
				case "days":
					if(parseInt(jQuery("#mm-rebill_period").val()) < 7 || parseInt(jQuery("#mm-rebill_period").val()) > 90)
					{
						alert("The rebill period must be between 7 and 90 days");
						jQuery("#mm-rebill_period").focus();
						return false;
					}
					break;
				
				case "weeks":
					if(parseInt(jQuery("#mm-rebill_period").val()) < 1 || parseInt(jQuery("#mm-rebill_period").val()) > 52)
					{
						alert("The rebill period must be between 1 and 52 weeks");
						jQuery("#mm-rebill_period").focus();
						return false;
					}
					break;
					
				case "months":
					if(parseInt(jQuery("#mm-rebill_period").val()) < 1 || parseInt(jQuery("#mm-rebill_period").val()) > 12)
					{
						alert("The rebill period must be between 1 and 12 months");
						jQuery("#mm-rebill_period").focus();
						return false;
					}
					break;
					
				case "years":
					if(parseInt(jQuery("#mm-rebill_period").val()) != 1)
					{
						alert("The rebill period must be 1 year");
						jQuery("#mm-rebill_period").focus();
						return false;
					}
					break;
			}
		}
		
		return true;
	},
	
	changeOption: function(id)
	{
		if(jQuery("#"+id).is(":checked"))
		{
			jQuery("#"+id+"_val").val("1");
		}
		else
		{
			jQuery("#"+id+"_val").val("0");
		}
	},

	toggleTrial: function()
	{
		if(jQuery("#mm-has_trial").is(":checked"))
		{
			jQuery("#mm_has_trial_row").show();
		}
		else
		{
			jQuery("#mm_has_trial_row").hide();
		}
		
		this.changeOption('mm-has_trial');
	},
	
	toggleRecurring: function()
	{
		if(jQuery("#mm-is_recurring").is(":checked"))
		{
			jQuery("#mm_rebill_row").show();
		}
		else
		{
			jQuery("#mm_rebill_row").hide();
		}
		
		this.changeOption('mm-is_recurring');
	},
	
	showPurchaseLinks: function(productId, productName)
	{	
		var values =  {};
		values.product_id = productId;
		values.product_name = productName;
		values.mm_action = "showPurchaseLinks";
		
		mmdialog_js.showDialog("mm-purchaselinks-dialog", this.module, 515, 600, "Purchase Links", values);
	},
	
	addFieldOption: function(addImage, removeImage)
	{
		var optionId = -1;
		
		jQuery('input.field-option').each(function( index ) {
			crntOptionId = jQuery(this).attr('id').replace("mm-partner-", ""); 
			if(parseInt(crntOptionId) > optionId)
			{
				optionId = crntOptionId;
			}
		});
		
		optionId++;
		
		var html = "<div id=\"mm-partner-container-" + optionId + "\">";
		html += "<input id=\"mm-partner-" + optionId + "\" type=\"text\" class=\"field-option\" size=\"15\" /> ";
		html += "<select id=\"mm-commission-profile-" + optionId + "\">";
		html += jQuery("#mm-commission-profile-options-container").html();
		html += "</select> ";
		html += "<a href=\"javascript:mmjs.addFieldOption('" + String(addImage).replace(/"/g, '&quot;') + "', '" + String(removeImage).replace(/"/g, '&quot;') + "');\">" + addImage + "</a> ";
		html += "<a href=\"javascript:mmjs.removeFieldOption('mm-partner-container-" + optionId + "');\">" + removeImage + "</a>";
		html += "</div>";
		jQuery("#mm-partners").append(html);
		jQuery("#mm-partner-" + optionId).focus();
	},
	
	removeFieldOption: function(id)
	{
		jQuery("#"+id).remove();
	},
	
	
	renderAttributes: function(value,dataCell,dataObj, grid)
	{
		let icons = "";
		let clearImage = grid.getImage('clear');
		
		icons += ((+dataObj.has_trial == 1) ? grid.getImage('trial') : clearImage); //trial or not
		if (+dataObj.rebill_period > 0) //current test for recurring
		{
			icons += ((+dataObj.do_limit_payments) ? grid.getImage('payment-plan') : grid.getImage('subscription')); //payment plan or subscription
		}
		else
		{
			icons += clearImage;
		}
		icons += ((+dataObj.is_shippable == 1) ? grid.getImage('shippable') : clearImage);
		icons += ((dataObj.sku.trim() != "") ? grid.getImage('sku').replace("[SKU PLACEHOLDER]",`SKU [${dataObj.sku}]`) : clearImage);
		dataCell.innerHTML = icons;
	},
	
	renderAccess: function(value,dataCell,dataObj,grid)
	{
		let hasAccess = false;
		let frag = document.createDocumentFragment();
		
		if ((mmjs.membershipLevelLink) && (dataObj.membership_levels) && (Array.isArray(dataObj.membership_levels)) && (dataObj.membership_levels.length > 0))
		{
			hasAccess = true;
			let first = true;
			dataObj.membership_levels.forEach( (membership_level) => {
				first = (first === true) ? false : frag.appendChild(document.createTextNode(", "));
				let mlIconSpan = document.createElement("span");
				mlIconSpan.style.marginRight = "5px";
				mlIconSpan.innerHTML = grid.getImage("membership");
				frag.appendChild(mlIconSpan);
				let mlLink = document.createElement("a");
				mlLink.href = `${mmjs.membershipLevelLink}${membership_level.id}`;
				mlLink.title = `${membership_level.name} [ID:${membership_level.id}]`;
				let mlLinkText = document.createTextNode(mmjs.abbrevString(membership_level.name,30));
				mlLink.appendChild(mlLinkText);
				frag.appendChild(mlLink);				
			});
		}
		
		//if ((mmjs.bundleLink) && (dataObj.bundle_id != null))
		if ((mmjs.bundleLink) && (dataObj.bundles) && (Array.isArray(dataObj.bundles)) && (dataObj.bundles.length > 0))
		{
			hasAccess = true;
			let first = true;
			dataObj.bundles.forEach( (bundle) => {
				first = (first === true) ? false : frag.appendChild(document.createTextNode(", "));
				let bundleIconSpan = document.createElement("span");
				bundleIconSpan.style.marginRight = "5px";
				bundleIconSpan.innerHTML = grid.getImage("bundle");
				frag.appendChild(bundleIconSpan);
				let bundleLink = document.createElement("a");
				bundleLink.href = `${mmjs.bundleLink}${bundle.id}`;
				bundleLink.title = `${bundle.name} [ID:${bundle.id}]`;
				let bundleLinkText = document.createTextNode(mmjs.abbrevString(bundle.name,30));
				bundleLink.appendChild(bundleLinkText);
				frag.appendChild(bundleLink);				
			});
		}
		
		if (hasAccess)
		{
			dataCell.appendChild(frag);
		}
		else
		{
			dataCell.innerHTML = "&mdash;";
		}
	},
	
	
	renderPurchaseLinks: function(value,dataCell,dataObj,grid)
	{
		let purchaseLink = document.createElement("a");
		purchaseLink.title = grid.tr("Get purchase links");
		purchaseLink.setAttribute("role","button");
		purchaseLink.setAttribute("data-product-id",dataObj.id);
		purchaseLink.setAttribute("data-product-name",mmjs.htmlentities(dataObj.name));
		purchaseLink.className = "mm-ui-button mm-product-purchase-links";
		purchaseLink.innerHTML = grid.getImage("purchase-link");
		dataCell.appendChild(purchaseLink);
	},
	
	
	renderActions: function(value,dataCell,dataObj,grid)
	{
		let editAction = mmjs.createImageButton(grid.getImage("edit"),"mm-product-edit",{"data-product-id" : dataObj.id});
		
		let deleteAction = "";
		if ((Number(dataObj.is_being_used) == 0) && (Number(dataObj.has_been_purchased) == 0))
		{
			deleteAction = mmjs.createImageButton(grid.getImage("delete"),"mm-product-delete",{"data-product-id" : dataObj.id});
		}
		else
		{
			deleteAction = document.createElement("span");
			deleteAction.innerHTML = grid.getImage("cant-delete");
		}
		
		let showHideAction = "";
		if (Number(dataObj.is_hidden) != 1)
		{
			showHideAction = mmjs.createImageButton(grid.getImage("hide"),"mm-product-hide",
												 {"data-product-id" : dataObj.id,
												  "data-product-status" : dataObj.status});
		}
		else
		{
			showHideAction = mmjs.createImageButton(grid.getImage("unhide"),"mm-product-show",
												 {"data-product-id" : dataObj.id,
												  "data-product-status" : dataObj.status});
		}
		
		let duplicateAction = mmjs.createImageButton(grid.getImage("duplicate"),"mm-product-duplicate",{"data-product-id" : dataObj.id});

		dataCell.className = "mm-ehd-table-cell-nowrap";
		dataCell.appendChild(editAction);
		dataCell.appendChild(deleteAction);
		dataCell.appendChild(showHideAction);
		dataCell.appendChild(duplicateAction);
	},
	
	
	renderGrid: function(imageList,translationList)
	{
		jQuery(document).ready(function() {
			let gridConfig = { "columns" : [ { "id": "id", "name":"ID", "sortable":true, "searchType":"numeric", "defaultSortType":"DESC" },
											 { "id": "name", "name":"Name", "sortable":true, "searchType":"text", "defaultSearchField":true },
											 { "id": "billing_description", "name":"Billing Description" },
											 { "id": "attributes", "name":"Attributes", "render":mmjs.renderAttributes },
											 { "id": "access", "name":"Associated Access", "render":mmjs.renderAccess },
											 { "id": "purchase_links", "name":"Purchase Links", "render":mmjs.renderPurchaseLinks },
											 { "id": "status", "name":"Status", "sortable":true, "render":mmjs.renderStatus },
											 { "id": "actions", "name":"Actions", "render":mmjs.renderActions }
											],
							   "version"    : 3, //MUST increment this every time the column definitions change!
							   "datasource" : mmjs.search.bind(mmjs) 
				};
			grid = new MM_EnhancedDatagridJS(document.getElementById("gridHolder"),"product",gridConfig);
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
		mmjs.bindByClassName("#gridHolder","click",".mm-product-purchase-links",['productId','productName'],mmjs.showPurchaseLinks.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-product-edit",['productId'],(productId) => {
			mmjs.edit.call(mmjs,'mm-products-dialog', productId);
		});
		mmjs.bindByClassName("#gridHolder","click",".mm-product-show",['productId','productArchived'],mmjs.showProduct.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-product-hide",['productId','productArchived'],mmjs.hideProduct.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-product-delete",['productId'],mmjs.remove.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-product-duplicate",['productId'],(productId) => {
			mmjs.duplicate.call(mmjs,'mm-products-dialog',productId);
		});
	}	
	
});

var mmjs = new MM_ProductViewJS("MM_ProductView", "Product");