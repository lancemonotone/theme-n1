/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_ManageTransactionsViewJS = MM_EnhancedDatagridView.extend(
{

	ttm: "", //transaction type map

	showSearch: function() 
	{
		jQuery("#mm-show-search-btn").hide();
		jQuery("#mm-hide-search-btn").show();
		jQuery("#mm-advanced-search").slideDown(300, this.storeSearchState);
	},


	hideSearch: function() 
	{
		jQuery("#mm-show-search-btn").show();
		jQuery("#mm-hide-search-btn").hide();
		jQuery("#mm-advanced-search").slideUp(300, this.storeSearchState);
	},


	storeSearchState: function() 
	{
		this.module = "MM_ManageTransactionsView";
		this.method = "performAction";
		this.action = "module-handle";

		var values = {
			mm_action: "storeSearchState",
			mm_admin_id: jQuery("#mm-admin-id").val(),
			mm_show_search: "0"
		}

		if (jQuery('#mm-advanced-search').is(':visible')) {
			values.mm_show_search = "1";
		}

		var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		ajax.useLoader = false;
		ajax.send(values, false, 'mmjs', "storeSearchStateCallback");
	},


	storeSearchStateCallback: function(data) 
	{
		if (data == undefined) {
			alert("No response received");
		}
		else if (data.type == "error") {
			alert(data.message);
		}
		else {
			// do nothing
		}
	},


	validateForm: function() 
	{
		if (jQuery('#mm-new-first-name').val() == "") {
			alert("Please enter the member's first name");
			return false;
		}

		if (jQuery('#mm-new-last-name').val() == "") {
			alert("Please enter the member's last name");
			return false;
		}

		if (jQuery('#mm-new-email').val() == "") {
			alert("Please enter the member's email address");
			return false;
		}

		if (!this.validateEmail(jQuery('#mm-new-email').val())) {
			alert("Please enter a valid email address");
			return false;
		}

		if (jQuery('#mm-new-password').val() == "") {
			alert("Please enter a valid email address");
			return false;
		}

		return true;
	},


	resetForm: function() 
	{
		let form = document.getElementById("manage-transactions-search-form");
		if (form)
		{
			form.reset();
			//todo: reset grid
		}
	},


	sort: function(columnName) 
	{
		var newSortDir = "asc";

		if (columnName == this.sortBy) 
		{
			if (this.sortDir == "asc") 
			{
				newSortDir = "desc";
			}
		}

		this.sortBy = columnName;
		this.sortDir = newSortDir;

		this.search();
	},


	dgPreviousPage: function(crntPage) 
	{
		if (parseInt(crntPage) != 0) 
		{
			this.crntPage = parseInt(crntPage) - 1;
			this.search();
		}
	},


	dgNextPage: function(crntPage, totalPages) 
	{
		if (crntPage != (parseInt(totalPages) - 1)) 
		{
			this.crntPage = parseInt(crntPage) + 1;
			this.search();
		}
	},


	dgSetResultSize: function(pageControl) 
	{
		if (jQuery(pageControl).val() != undefined) 
		{
			this.crntPage = 0;
			this.resultSize = jQuery(pageControl).val();
			this.search();
		}
	},


	processSearchForm: function() 
	{
		let queryStruct = { conditions: [] };
		let form_obj = new MM_Form('mm-form-container');
		let values = form_obj.getFields();
		for (key in values) {
			if ((key != "") &&
				((Array.isArray(values[key]) && (values[key].length > 0)) ||
					(!Array.isArray(values[key]) && (values[key] != "")))) {
				
					switch (key) 
					{
					case 'mm_from_date':
						let fromDate = values[key] + " 00:00:00";
						queryStruct.conditions.push({ "name": "t.transaction_date", "value": fromDate, "rel": "gteq" });
						break;
					case 'mm_to_date':
						let toDate = values[key] + " 23:59:59";
						queryStruct.conditions.push({ "name": "t.transaction_date", "value": toDate, "rel": "lteq" });
						break;
					case 'mm_order_number':
						queryStruct.conditions.push({ "name": "o.order_number", "value": values[key], "rel": "like" });
						break;
					case 'mm_member_id':
						queryStruct.conditions.push({ "name": "mud.wp_user_id", "value": values[key], "rel": "eq" });
						break;
					case 'mm_first_name':
						queryStruct.conditions.push({ "name": "mud.first_name", "value": values[key], "rel": "like" });
						break;
					case 'mm_last_name':
						queryStruct.conditions.push({ "name": "mud.last_name", "value": values[key], "rel": "like" });
						break;
					case 'mm_email':
						queryStruct.conditions.push({ "name": "u.user_email", "value": values[key], "rel": "like" });
						break;
					case 'mm_transaction_types[]':
						queryStruct.conditions.push({ "name": "t.transaction_type", "value": values[key].join(","), "rel": "in" });
						break;
					case 'mm_product_ids[]':
						queryStruct.products = values[key];
						break;
					case 'mm_coupon_ids[]':
						queryStruct.coupons = values[key];
						break;
					case 'mm_affiliate_id':
						queryStruct.conditions.push({ "name": "o.affiliate_id", "value": values[key], "rel": "like" });
						break;
					case 'mm_sub_affiliate_id':
						queryStruct.conditions.push({ "name": "o.sub_affiliate_id", "value": values[key], "rel": "like" });
						break;
					case 'mm_payment_service':
						queryStruct.conditions.push({ "name": "p.id", "value": values[key], "rel": "in" });
						break;
				}
			}
		}
		mmjs.grid.customSearch(queryStruct);
	},


	csvExport: function(crntPage) 
	{
		var form_obj = new MM_Form('mm-form-container');
		var values = form_obj.getFields();

		if (crntPage != undefined) {
			this.crntPage = crntPage;
		}

		values.sortBy = this.sortBy;
		values.sortDir = this.sortDir;
		values.crntPage = this.crntPage;
		values.resultSize = this.resultSize;
		values.csv = 1;
		values.mm_action = "csvExport";
		values.module = this.module;

		//export transactions url is set in manage_transactions.php
		var tmpForm = jQuery("<form id='export_transactions_form' action='" + export_transactions_url + "' method='post' target='_export_transactions_window' style='display:none'></form>");
		jQuery("body").append(tmpForm);
		for (formVal in values) 
		{
			jQuery('<input>').attr({
				type: 'hidden',
				id: formVal,
				name: formVal,
			}).val(values[formVal]).appendTo("#export_transactions_form");
		}

		jQuery("#export_transactions_form").submit();
		jQuery("#export_transactions_form").remove();
	},


	csvExportCallback: function(data) 
	{
		jQuery("#mm_manage_transactions_csv").append('<form id="mm_exportform" method="post" target="_blank"><input type="hidden" id="mm_exportdata" name="exportdata" /></form>');
		jQuery("#mm_exportform").submit().remove();
		this.search();
		return true;
	},


	renderTransactionAmount: function(value, dataCell, dataObj, grid) 
	{
		return dataObj.formatted_amount;
	},


	renderFullname: function(value, dataCell, dataObj, grid) 
	{
		let last = dataObj.last_name ? dataObj.last_name : "";
		let first = dataObj.first_name ? dataObj.first_name : "";
		return `${last},${first}`;
	},


	renderAffiliate: function(value, dataCell, dataObj, grid) 
	{
		if (value.trim() == "")
		{
			return "&mdash;"
		}
		return { "title": value, "value": value.substring(0, 15) };
	},


	renderType: function(value, dataCell, dataObj, grid) 
	{
		let icons = "";
		let clearImage = grid.getImage('clear');

		if ((dataObj.transaction_type) && (this.ttm != "")) 
		{
			if ((this.ttm.payment) && (this.ttm.payment == value))
				icons += grid.getImage('type-payment');
			else if ((this.ttm.recurring_payment) && (this.ttm.recurring_payment == value))
				icons += grid.getImage('type-recurring');
			else if ((this.ttm.refund) && (this.ttm.refund == value))
				icons += grid.getImage('type-refund');
			else
				icons += grid.getImage('clear');
		}

		icons += (Number(dataObj.is_test) == 1) ? grid.getImage('type-test') : grid.getImage('clear');
		dataCell.innerHTML = icons;
	},


	renderOrderNumber: function(value, dataCell, dataObj, grid) 
	{
		return { "link": this.orderLink + dataObj.user_id, "value": value };
	},


	renderEmail: function(value, dataCell, dataObj, grid) 
	{
		return { "link": this.memberLink + dataObj.user_id, "value": value };
	},


	renderDescription: function(value, dataCell, dataObj, grid) 
	{
		return { "title": value, "value": mmjs.abbrevString(value,30) };
	},
	
	
	renderTransactionDate: function(value, dataCell, dataObj, grid)
	{
		return grid.isJSDate(value) ? value.toLocaleString(undefined,{"dateStyle":"medium","timeStyle":"short"}) : grid.dateFromMysqlDate(value).toLocaleString(undefined,{"dateStyle":"medium","timeStyle":"short"});	
	},


	resetGridHandler: function(data) 
	{
		if (data) 
		{
			jQuery("#mm-grid-container").html(data);
		}
	},


	renderGrid: function(imageList, translationList, transactionTypeMap) 
	{
		jQuery(document).ready(function() {
			let gridConfig = {
				"columns": [{ "id": "transaction_type", "name": "Type", "render": mmjs.renderType.bind(mmjs) },
				{ "id": "transaction_date", "name": "Date", "sortable": true, "searchType": "date", "defaultSortType": "DESC", "defaultSearchField": true, "render":mmjs.renderTransactionDate.bind(mmjs) },
				{ "id": "order_number", "name": "Order #", "sortable": true, "searchType": "text", "render": mmjs.renderOrderNumber.bind(mmjs) },
				{ "id": "transaction_amount", "name": "Amount", "sortable": true, "searchType": "numeric", "render": mmjs.renderTransactionAmount },
				{ "id": "last_name", "name": "Name", "sortable": true, "searchType": "text", "render": mmjs.renderFullname },
				{ "id": "user_email", "name": "Email", "sortable": true, "searchType": "text", "render": mmjs.renderEmail.bind(mmjs) },
				{ "id": "affiliate_id", "name": "Affiliate", "sortable": true, "render": mmjs.renderAffiliate },
				{ "id": "sub_affiliate_id", "name": "Sub-Affiliate", "sortable": true, "render": mmjs.renderAffiliate },
				{ "id": "description", "name": "Description", "sortable": true, "render": mmjs.renderDescription },
				{ "id": "payment_service", "name": "Service Provider", "sortable": true }
				],
				"version": 1, //MUST increment this every time the column definitions change!
				"datasource": mmjs.search.bind(mmjs)
			};
			grid = new MM_EnhancedDatagridJS(document.getElementById("gridHolder"), "transactions", gridConfig);
			grid.bulkOperationsEnabled = false;
			grid.useCustomSearch();
			
			if (imageList) 
			{
				grid.setImageReferences(imageList);
			}
			
			if (translationList) 
			{
				grid.setTranslationObject(translationList);
			}
			
			if (transactionTypeMap) 
			{
				mmjs.ttm = transactionTypeMap;
			}
			grid.render();
			mmjs.setGrid(grid);
		});
	}
});

var lastActionValues = {};
var mmjs = new MM_ManageTransactionsViewJS("MM_ManageTransactionsView", "Transaction");