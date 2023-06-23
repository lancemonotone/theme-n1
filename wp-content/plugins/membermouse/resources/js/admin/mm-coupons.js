/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_CouponViewJS = MM_EnhancedDatagridView.extend({
	couponLogLink: false,
	productsLink: false,
	bulkActionKey: "coupon",
	
	createCoupon: function(){

		this.processForm();
		if(this.validateForm()) 
		{
			var form_obj = new MM_Form('mm-coupons-container');
		    var values = form_obj.getFields();
		    values.mm_action = "save";
		    
		    values.mm_products= this.getProducts();
		    values.mm_recurring_setting = (jQuery("#mm_recurring_setting_first").is(":checked"))?"first":"all";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', this.updateHandler); 
		}
	},

	hideCoupon: function(id,currentStatus)
	{  
	  this.confirmShowHideWithStatusOption(id, currentStatus, true, "Coupon", "coupon"); 
	},
	
	showCoupon: function(id,currentStatus)
	{  
	  this.confirmShowHideWithStatusOption(id, currentStatus, false, "Coupon", "coupon"); 
	},
	  
		storeCouponSearchOptions: function()
		{
			this.module = "MM_CouponView";
			this.method = "performAction";
			this.action = "module-handle";
			  
			var values = {
				mm_action: "storeCouponSearchOptions",
				mm_admin_id: jQuery("#mm-admin-id").val(),
				mm_show_hidden_coupons: "0",
				mm_show_expired_coupons: "0"
			}
			 
			if(jQuery('#mm-show-hidden-coupons').is(':checked'))
			{
				values.mm_show_hidden_coupons = "1";
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
				document.location.href = document.location.href.replace(/#/, '');
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
		// status
		jQuery("#mm-status").prop('value', jQuery('#mm-status-container input:radio:checked').val());
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
	
	renderQuantityUsed: function(value,dataCell,dataObj,grid)
	{
		let clAnchor = grid.ce(dataCell,"a");
		clAnchor.title = grid.tr("View customers who have used this coupon");
		clAnchor.href = `${mmjs.couponLogLink}${dataObj.coupon_code}`;
		let anchorText = `${dataObj.quantity_used} ${grid.tr("used")}`;
		if (Number(dataObj.quantity) !== -1)
		{
			anchorText += ` ${grid.tr("of")} ${Number(dataObj.quantity)}`;
		}
		let anchorTextNode = document.createTextNode(anchorText);
		clAnchor.appendChild(anchorTextNode);
	},
	
	renderProductRestrictions: function(value,dataCell,dataObj,grid)
	{
		if (dataObj.product_restrictions && Array.isArray(dataObj.product_restrictions))
		{
			let productLinks = dataObj.product_restrictions.map((prod) => {
				return `<a href="${mmjs.productsLink}${prod.id}" title="${grid.tr("Product ID")} ${prod.id}">${prod.name}</a>`;
			}).join(", ");
			if (productLinks == "")
			{
				return "&mdash;";
			}
			dataCell.innerHTML = productLinks;
		}
		else
		{
			return "&mdash;";
		}
	},
	
	
	renderValidDates: function(value,dataCell,dataObj,grid)
	{
		if (dataObj.start_date)
		{
			let dateText = "";
			let startDate = grid.isJSDate(dataObj.start_date) ? dataObj.start_date.toLocaleDateString() :dataObj.start_date;
			let endDate = grid.isJSDate(dataObj.end_date) ? dataObj.end_date : grid.dateFromMysqlDate(dataObj.end_date); 
			endDate = grid.isJSDate(endDate) ? endDate.toLocaleDateString() : endDate; 
			if (dataObj.end_date)
			{
				dateText = `${startDate} - ${endDate}`;
			}
			else
			{
				dateText = `${grid.tr("After")} ${startDate}`;
			}
			let dtn = document.createTextNode(dateText);
			dataCell.appendChild(dtn);
		}
		else
		{
			return "&mdash;";
		}
	},
	
	
	renderDescription: function(value,dataCell,dataObj,grid)
	{
		if (dataObj.coupon_value && dataObj.coupon_type)
		{
			let frag = document.createDocumentFragment();
			let descrSpan = document.createElement("span");
			descrSpan.style = "font-family:courier;";
			frag.appendChild(descrSpan);
			
			let nodeValue = (dataObj.coupon_type == "free") ? "FREE" : dataObj.formatted_value;
			nodeValue = (dataObj.coupon_type == "percentage") ? `${nodeValue}%` : nodeValue;
			let tn = document.createTextNode(nodeValue);
			descrSpan.appendChild(tn);
			
			if ((dataObj.coupon_type === "percentage") || (dataObj.coupon_type === "dollar"))
			{
				let msg = (dataObj.recurring_billing_setting === "first") ? "off the first charge" : "off all charges";
				let atn = document.createTextNode(` ${grid.tr(msg)}`);
				frag.appendChild(atn);
			}
			dataCell.appendChild(frag);
		}
	},
	
	
	renderCouponStatus: function(value,dataCell,dataObj,grid)
	{
		let invertedValue = (Number(value) == 1) ? 0 : 1;
		return mmjs.renderStatus(invertedValue,dataCell,dataObj,grid);
	},
	
	
	renderActions: function(value,dataCell,dataObj,grid)
	{
		let editAction = mmjs.createImageButton(grid.getImage("edit"),"mm-coupon-edit",{"data-coupon-id" : dataObj.id});
		
		let deleteAction = "";
		if (Number(dataObj.quantity_used) == 0)
		{
			deleteAction = mmjs.createImageButton(grid.getImage("delete"),"mm-coupon-delete",{"data-coupon-id" : dataObj.id});
		}
		else
		{
			deleteAction = document.createElement("span");
			deleteAction.innerHTML = grid.getImage("cant-delete");
		}
		
		let showHideAction = "";
		if (Number(dataObj.is_hidden) != 1)
		{
			showHideAction = mmjs.createImageButton(grid.getImage("hide"),"mm-coupon-hide",
												 {"data-coupon-id" : dataObj.id,
												  "data-coupon-archived" : dataObj.is_hidden});
		}
		else
		{
			showHideAction = mmjs.createImageButton(grid.getImage("unhide"),"mm-coupon-show",
												 {"data-coupon-id" : dataObj.id,
												  "data-coupon-archived" : dataObj.is_hidden});
		}
		
		
		let duplicateAction = mmjs.createImageButton(grid.getImage("duplicate"),"mm-coupon-duplicate",{"data-coupon-id" : dataObj.id});
		
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
											 { "id": "coupon_name", "name":"Name", "sortable":true, "searchType":"text", "defaultSearchField":true},
											 { "id": "coupon_code", "name":"Coupon Code", "sortable":true },
											 { "id": "description", "name":"Description", "render":mmjs.renderDescription},
											 { "id": "quantity_used", "name":"# Used", "sortable":true, "searchType":"numeric","render": mmjs.renderQuantityUsed},
											 { "id": "start_date", "name":"Valid Dates", "searchType": "date", "sortable":true, "render":mmjs.renderValidDates },
											 { "id": "product_restrictions", "name":"Product Restrictions", "render":mmjs.renderProductRestrictions },
											 { "id": "is_archived", "name":"Status", "sortable":true ,"render":mmjs.renderCouponStatus },
											 { "id": "actions", "name":"Actions", "render":mmjs.renderActions }
											],
								"version"    : 1, //MUST increment this every time the column definitions change!
								"datasource" : mmjs.search.bind(mmjs) 
								};
			grid = new MM_EnhancedDatagridJS(document.getElementById("gridHolder"),"coupons",gridConfig);
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
		//action buttons
		mmjs.bindByClassName("#gridHolder","click",".mm-coupon-edit",['couponId'],(cpId) => {
			mmjs.edit.call(mmjs,'mm-coupons-dialog', cpId);
		});
		
		mmjs.bindByClassName("#gridHolder","click",".mm-coupon-show",['couponId','couponArchived'],mmjs.showCoupon.bind(mmjs));
		mmjs.bindByClassName("#gridHolder","click",".mm-coupon-hide",['couponId','couponArchived'],mmjs.hideCoupon.bind(mmjs));
		
		mmjs.bindByClassName("#gridHolder","click",".mm-coupon-delete",['couponId'],mmjs.remove.bind(mmjs));
		
		mmjs.bindByClassName("#gridHolder","click",".mm-coupon-duplicate",['couponId'],(cpId) => {
			mmjs.duplicate.call(mmjs,'mm-coupons-dialog', cpId);
		});
	}
	
});

var mmjs = new MM_CouponViewJS("MM_CouponView", "Coupon");