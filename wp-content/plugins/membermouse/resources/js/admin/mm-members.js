/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_MembersViewJS = MM_EnhancedDatagridView.extend({
  
	statusImageMap: false,
	memberDetailsLink: false,
	bundlesLink: false,
	mm_member_custom_field_visible: false,
	mm_member_custom_field2_visible: false,
	
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
		this.module = "MM_MembersView";
		this.method = "performAction";
		this.action = "module-handle";
		  
		var values = {
			mm_action: "storeSearchState",
			mm_admin_id: jQuery("#mm-admin-id").val(),
			mm_show_search: "0"
		}
		
		if(jQuery('#mm-advanced-search').is(':visible'))
		{
			values.mm_show_search = "1";
		}
		 
		 var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		 ajax.useLoader = false;
		 ajax.send(values, false, 'mmjs', "storeSearchStateCallback"); 
	},
	
	storeSearchStateCallback: function(data)
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
			// do nothing
		}
	},
	
	createMember: function()
	{	
		if(this.validateForm()) 
		{
			var form_obj = new MM_Form('mm-new-member-form-container');
		    lastActionValues = form_obj.getFields();
		    lastActionValues.mm_id = -1;
		    lastActionValues.mm_new_membership = jQuery("#mm-new-membership-selector").val();
		    lastActionValues.mm_action = "createMember";
		    lastActionValues.mm_jshandle = "mmjs";
		    lastActionValues.mm_compfunction = "compAccess";
			
			pymtutils_js.checkIfPaymentRequired('membership', lastActionValues.mm_new_membership, 'paymentRequirementHandler', 'mmjs');
		}
	},
	
	paymentRequirementHandler: function(result)
	{
		if(typeof result === 'object')
		{
			alert("Error checking payment requirements: " + result.message);
		}
		else if(result == true)
		{
			if(lastActionValues.mm_action == "createMember")
			{
				pymtutils_js.showPaymentOptions(lastActionValues.mm_id, 'membership', lastActionValues.mm_new_membership, JSON.stringify(lastActionValues));
			}
		}
		else
		{
			mmjs.executeAction(lastActionValues, false);
		}
	},
	
	compAccess: function(actionValues)
	{
		mmjs.executeAction(JSON.parse(actionValues), true);
	},
	
	executeAction: function(actionValues, doComp)
	{
		var msg = "";
		var doContinue = true;
		
		if(actionValues.mm_action == "createMember" && doComp)
		{
			msg = "Are you sure you want to create a '" + jQuery("#mm-new-membership-selector :selected").text() + "' member for free?";
			doContinue = confirm(msg);
		}
		
		if(doContinue)
		{
			pymtutils_js.closeDialog(mm_pymtdialog);
			mmjs.closeDialog();
			var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(actionValues, false, 'mmjs', "createMemberCallbackHandler"); 
		}
	},
	
	createMemberCallbackHandler: function(data)
	{
		lastActionValues = {};
		
		if(data.type == "error")
		{
			if(data.message.length > 0)
			{  
				alert(data.message);
				return false;
			}
			return false;
		}
		else 
		{
			this.search();
			alert("Member created successfully");
		}
	},
	
	validateForm: function()
	{	
		if(jQuery('#mm-new-first-name').val() == "") 
		{
			alert("Please enter the member's first name");
			return false;
		}
		
		if(jQuery('#mm-new-last-name').val() == "") 
		{
			alert("Please enter the member's last name");
			return false;
		}
		
		if(jQuery('#mm-new-email').val() == "") 
		{
			alert("Please enter the member's email address");
			return false;
		}
	   
		if(!this.validateEmail(jQuery('#mm-new-email').val())) 
		{
			alert("Please enter a valid email address");
			return false;
		}
		
		if(jQuery('#mm-new-password').val() == "")
		{
			alert("Please enter a password");
			return false;
		}
		
		return true;
	},
	  
	  resetForm: function()
	  {
		  var form_obj = new MM_Form('mm-form-container');
		  form_obj.clearCachedFields(this.module);

		  let form = document.getElementById("manage-members-advanced-search-form");
		  if (form)
		  {
		  	form.reset();
			//todo: reset grid
		  }
	  },
  
	  resetFormHandler: function(data)
	  {
		  if(data) {
			  jQuery("#mm-advanced-search-container").html(data);
		  }
	  },
  
	  sort: function(columnName) 
	  {
		  var newSortDir = "asc";
		  
		  if(columnName == this.sortBy)
		  {
			  if(this.sortDir=="asc") {
				  newSortDir = "desc";
			  }
		  }
		  
		  this.sortBy = columnName;
		  this.sortDir = newSortDir;
		  
		  this.search();
	  },
	  
	  dgPreviousPage: function(crntPage)
	  {	
		  if(parseInt(crntPage) != 0) {
			  this.crntPage = parseInt(crntPage) - 1;
			  this.search();
		  }
	  },
	  
	  dgNextPage: function(crntPage, totalPages)
	  {
		  if(crntPage != (parseInt(totalPages) - 1)) {
			  this.crntPage = parseInt(crntPage) + 1;
			  this.search();
		  }
	  },
	  
	  dgSetResultSize: function(pageControl)
	  {
		  if(jQuery(pageControl).val() != undefined)
		  {
			  this.crntPage = 0;
			  this.resultSize = jQuery(pageControl).val();
			  this.search();
		  }
	  },
	  
	  changeCustomField: function(field){
		var customField = jQuery("#"+field).val();
		if(customField==''){
			jQuery("#"+field+"-value").hide();
		}
		else{
			jQuery("#"+field+"-value").show();
		}
	  },
  
	  oldSearch: function(crntPage) 
	  { 
		  var form_obj = new MM_Form('mm-form-container');
		  var values = form_obj.getFields();
		  form_obj.saveCachedFields(this.module, values);
		  
		  if(crntPage != undefined) {
			  this.crntPage = crntPage;
		  }
      
		  values.sortBy = this.sortBy;
		  values.sortDir = this.sortDir;
		  values.crntPage = this.crntPage;
		  values.resultSize = this.resultSize;
		  values.mm_action = "search";
	  
		  var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		  ajax.send(values, false, "mmjs", "resetGridHandler"); 
	  },
	  
	  remove: function(id, memberEmail)
	  { 
	    var doRemove = confirm("Are you sure you want to delete the member '" + memberEmail + "'?");
	    
	    if(doRemove)
	    {
	        var values = {
	            id: id,
	            mm_action: "remove"
	        };
	        
	        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	        ajax.send(values, false, 'mmjs', this.updateHandler); 
	    }
	  },
	  
	  getSearchFormValues:function()
	  {
		  var form_obj = new MM_Form('mm-form-container');
		  var values = form_obj.getFields();
		  
		  values.sortBy = this.sortBy;
		  values.sortDir = this.sortDir;
		  values.crntPage = this.crntPage;
		  values.resultSize = this.resultSize;
		  values.csv = 1;
		  values.mm_action = "search";
		  return values;
	  },
	  
	  legacyCsvExport:function(crntPage)
	  {
		  if(crntPage != undefined) 
		  {
			  this.crntPage = crntPage;
		  }
		  
		  var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		  ajax.send(this.getSearchFormValues(), false, "mmjs", "legacyCsvExportCallback"); 
	  },
  
	  legacyCsvExportCallback: function(data)
	  {
	    jQuery("#mm_members_csv").append('<form id="mm_exportform" method="post" target="_blank"><input type="hidden" id="mm_exportdata" name="exportdata" /></form>');
	    jQuery("#mm_exportform").submit().remove();
	    
	    this.search();
	    return true; 
	  },
	  
	  resetGridHandler: function(data)
	  {
		  if(data) {
			  jQuery("#mm-grid-container").html(data);
		  }
	  },  
	  
	  csvExportCallback: function()
	  { 
		jQuery.unblockUI();
	    jQuery("#mm_members_csv").append('<form id="mm_exportform" method="post" target="_blank"><input type="hidden" id="mm_exportdata" name="exportdata" /></form>');
	    jQuery("#mm_exportform").submit().remove();
	    
	    this.search();
	    return true; 
	  },
	  
	  cancelExport: function()
	  {
		  this.mmBatch.cancel();
	  },
	  
	  csvExport: function(crntPage)
	  {   
		  if(crntPage != undefined) 
		  {
			  this.crntPage = crntPage;
		  }
		  
		  var payload = this.getSearchFormValues();   

		  //// We need to make these key value pairs normalized for use
		  //// on the server side and with the batch transfer libs
		  if(payload["mm_memberships[]"] !== undefined )
		  { 
			  payload["mm_memberships"] = payload["mm_memberships[]"];
			  delete payload["mm_memberships[]"]
		  }
		  if(payload["mm_bundles[]"] !== undefined )
		  {
			  payload["mm_bundles"] = payload["mm_bundles[]"];
			  delete payload["mm_bundles[]"]
		  }
		  if(payload["mm_member_status_types[]"] !== undefined )
		  {
			  payload["mm_member_status_types"] = payload["mm_member_status_types[]"];
			  delete payload["mm_member_status_types[]"]
		  }

		  var postvars = [];
		  postvars["module"] = this.module;
		  postvars["action"] = this.action;
		  postvars["method"] = this.method;
		  postvars["mm_action"] = "csvBatchExport";  
		  
		  jQuery.blockUI({  css: { width: '700px' }, message: jQuery('#export_status_dialog') });
		  
		  var callbackFunc = function(status, msg){
			  var myResponseMsg = null;
			  if(status == this.STATUS_FAILED)
		      {
				  myResponseMsg = "Export has failed with error: "+msg;
		      }
			  else if(status == this.STATUS_CANCELLED)
		      {
				  myResponseMsg = "Export has been cancelled.";
		      }
			  
			  jQuery.unblockUI({ onUnblock: function(data) { 
				 if(myResponseMsg!=null)
				 {
					 alert(myResponseMsg);
				 }
			  }});
		  }
		  
		  this.mmBatch = new MembermouseBatchTransfer(50, MemberMouseGlobal.adminUrl+"admin-ajax.php", MemberMouseGlobal.adminUrl+"admin-ajax.php", postvars, callbackFunc);
		  this.mmBatch.updateStatus = (currentChunk, totalChunks, currentRecord, totalRecords) => {
				jQuery("#exportProgress").attr("max", totalRecords)
				jQuery("#exportProgress").attr("value",currentRecord)
				jQuery("#exportProgressText").html(`${currentRecord}/${totalRecords}`);
		  }; 
		  this.mmBatch.initializeBatchReceive(payload);  
	  },

	processSearchForm: function() 
	{
		let queryStruct = { conditions: [] };
		let form_obj = new MM_Form('mm-form-container');
		let values = form_obj.getFields();
		let dumpCache = false;
		let dateField = (values['mm_member_search_by_date'] == "status_updated") ? "mud.status_updated" : "u.user_registered";
		let tog = (dateField == "mud.status_updated"); //the column selected in the search changes the visible grid columns
		this.grid.setColumnHidden.call(this.grid,"user_registered",tog);
		this.grid.setColumnHidden.call(this.grid,"status_updated",!tog);
		
		if (this.mm_member_custom_field_visible && (values.mm_member_custom_field == ""))
		{
			this.grid.setColumnHidden.call(this.grid,"mm_member_custom_field",true);
			this.mm_member_custom_field_visible = false;
		}
		
		if (this.mm_member_custom_field2_visible && (values.mm_member_custom_field2 == ""))
		{
			this.grid.setColumnHidden.call(this.grid,"mm_member_custom_field2",true);
			this.mm_member_custom_field2_visible = false;
		}
		
		for (key in values) {
			if ((key != "") &&
				((Array.isArray(values[key]) && (values[key].length > 0)) ||
					(!Array.isArray(values[key]) && (values[key] != "")))) {
				
					switch (key) 
					{
					case 'mm_from_date':
						let fromDate = values[key] + " 00:00:00";
						queryStruct.conditions.push({ "name": dateField, "value": fromDate, "rel": "gteq" });
						break;
					case 'mm_to_date':
						let toDate = values[key] + " 23:59:59";
						queryStruct.conditions.push({ "name": dateField, "value": toDate, "rel": "lteq" });
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
					case 'mm_memberships[]':
						queryStruct.conditions.push({ "name": "mud.membership_level_id", "value": values[key].join(","), "rel": "in" });
						break;
					case 'mm_bundles[]':
						queryStruct.bundles = values[key];
						break;
					case 'mm_member_status_types[]':
						queryStruct.conditions.push({ "name": "mud.status", "value": values[key].join(","), "rel": "in" });
						break;
					case 'mm_member_custom_field':
					case 'mm_member_custom_field2':
						if (!queryStruct.customFields)
						{
							queryStruct.customFields = [];
						}
						queryStruct.customFields.push({"custom_field_id":values[key], "value":values[`${key}_value`], "column":key});
						let elName = (key == "mm_member_custom_field") ? "mm-member-custom-field" : "mm-member-custom-field2";
						let el = document.getElementById(elName);
						if (el.selectedIndex)
						{
							let newName = el.options[el.selectedIndex].text;
							this[`${key}_visible`] = true;
							this.grid.alterColumn(key,newName,false);
						}
						dumpCache = true;
						break;
					case 'mm_notes':
						queryStruct.conditions.push({ "name": "mud.notes", "value": values[key], "rel": "LIKE" });
						break;
					case 'mm_country':
						queryStruct.conditions.push({ "name": "mud.billing_country", "value": values[key].join(","), "rel": "in" });
						break;
				}
			}
		}
		if (dumpCache)
		{
			mmjs.grid.dumpCacheAndRefresh(queryStruct);
		}
		else
		{
			mmjs.grid.customSearch(queryStruct);
		}
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
				bundleLink.href = `${mmjs.bundlesLink}${bundle.id}`;
				bundleLink.title = `${bundle.name} [ID:${bundle.id}]`;
				let bundleLinkText = document.createTextNode(mmjs.abbrevString(bundle.name,30));
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
	
	
	renderEmail: function(value, dataCell, dataObj, grid)
	{
		return { "link": mmjs.memberDetailsLink + dataObj.id, "title":value, "value": mmjs.abbrevString(value) };
	},


	renderStatus: function(value, dataCell, dataObj, grid) 
	{
		if (dataObj.status)
		{
			return { "images":[mmjs.statusImageMap[dataObj.status]]};
		}
		else
		{
			return "&mdash;";
		}
	},
	
	renderRegisteredStatusChanged: function(value,dataCell,dataObj,grid)
	{
		if (value)
		{
			let theDate = grid.isJSDate(value) ? value.toLocaleString(undefined,{"dateStyle":"medium","timeStyle":"short"}) : value;
			return theDate;
		}
		else
		{
			return "&mdash;";
		}
	},
	
	
	renderEngagement: function(value, dataCell, dataObj, grid) 
	{
		let frag = document.createDocumentFragment();
		let span = document.createElement("span");
		frag.appendChild(span);
		
		let llDate = "";
		if (dataObj.last_login_date)
		{
			lldDate = grid.isJSDate(dataObj.last_login_date) ? dataObj.last_login_date.toLocaleString(undefined,{"dateStyle":"medium","timeStyle":"short"}) : dataObj.last_login_date;
			let lldImage = grid.getImage("last-login-date").replace("[lldplaceholder]",lldDate);
			span.innerHTML = lldImage;
		}
		else
		{
			let lldImage = grid.getImage("never-logged-in");
			span.innerHTML = lldImage;
		}
		
		let lc = parseInt(dataObj.login_count);
		lc = isNaN(lc) ? 0 : lc;
		let lcImage = grid.getImage("login-count").replace("[lcplaceholder]",lc);
		span.innerHTML += lcImage + ` <span style='font-family:courier; font-size:12px; position:relative; top:1px; margin-right:8px;'>${lc}</span>`;
		
		let pa = parseInt(dataObj.pages_accessed);
		pa = isNaN(pa) ? 0 : pa;
		let paImage = grid.getImage("pages-accessed").replace("[paplaceholder]",pa);
		span.innerHTML += paImage + ` <span style='font-family:courier; font-size:12px; position:relative; top:1px;'>${pa}</span>`;
		dataCell.appendChild(frag);
	},
	
	
	renderActions: function(value,dataCell,dataObj,grid)
	{
		let editAction = mmjs.createImageButton(grid.getImage("edit"),"mm-member-edit",null,mmjs.memberDetailsLink + dataObj.id);
		let deleteAction = "";
		let hasActiveSubscriptions = (Number(dataObj.has_active_subscriptions) == 0) ? false : true;
		if (!hasActiveSubscriptions)
		{
			//can delete
			deleteAction = mmjs.createImageButton(grid.getImage("delete"),"mm-member-delete",{"data-member-id" : dataObj.id, "data-member-email" : dataObj.user_email});
		}
		else
		{
			//can't delete
			deleteAction = document.createElement("span");
			deleteAction.innerHTML = grid.getImage("cant-delete");
		}
		dataCell.appendChild(editAction);
		dataCell.appendChild(deleteAction);
	},
	
	
	bindEventListeners: function()
	{
		mmjs.bindByClassName("#gridHolder","click",".mm-member-delete",['memberId','memberEmail'],mmjs.remove.bind(mmjs));
	},


	renderGrid: function(imageList, translationList) 
	{
		mmjs.statusImageMap = {"1" : "active_status",
				               "2":"canceled_status",
				               "3":"locked_status",
				               "4":"paused_status",
				               "5":"overdue_status",
							   "6":"pending_activation_status",
				               "7":"error_status",
				               "8":"expired_status",
				               "9":"pending_cancellation_status"};

		//TODO: user_registered date is not sorting correctly
		jQuery(document).ready(function() {
			let gridConfig = {
				"columns": [{ "id": "name", "name": "Name", "sortable": true, "searchType": "text","render": mmjs.renderName },
				{ "id": "user_email", "name": "Email", "sortable": true, "searchType": "text", "render":mmjs.renderEmail},
				{ "id": "phone", "name": "Phone", "sortable": true, "searchType": "text" },
				{ "id": "membership_level_name", "name": "Membership Level", "sortable": true, "render": mmjs.renderMembershipLevel },
				{ "id": "bundles", "name": "Bundles", "render": mmjs.renderBundles },
				{ "id": "mm_member_custom_field", "name": "Custom Field 1", "hidden":true },
				{ "id": "mm_member_custom_field2", "name": "Custom Field 2", "hidden": true },
				{ "id": "user_registered", "name": "Registered", "sortable": true, "searchType": "date", "defaultSortType":"DESC","render": mmjs.renderDateColumn },
				{ "id": "status_updated", "name": "Status Changed", "sortable": true, "searchType": "date", "defaultSortType":"DESC", "hidden":true, "render": mmjs.renderDateColumn },
				{ "id": "last_login_date", "name": "Engagement", "sortable": true, "searchType": "date", "render": mmjs.renderEngagement },
				{ "id": "status", "name": "Status", "sortable": true, "render": mmjs.renderStatus },
				{ "id": "actions", "name": "Actions","render": mmjs.renderActions }
				],
				"version": 2, //MUST increment this every time the column definitions change!
				"datasource": mmjs.search.bind(mmjs)
			};
			grid = new MM_EnhancedDatagridJS(document.getElementById("gridHolder"), "members", gridConfig);
			grid.bulkOperationsEnabled = true;
			grid.supportedBulkOperations = [{ "id":"delete", "display_name": "Delete", "inProgress":"deleting", "completed":"deleted"}];
			grid.bulkAdminFunction = mmjs.bulkopAdminister.bind(mmjs);
			grid.useCustomSearch();
			
			if (imageList) 
			{
				grid.setImageReferences(imageList);
			}
			
			if (translationList) 
			{
				grid.setTranslationObject(translationList);
			}
			grid.render();
			mmjs.setGrid(grid);
		});
	}
});
 
var lastActionValues = {};
var mmjs = new MM_MembersViewJS("MM_MembersView", "Member");

/**
 * When doocument is ready, check for any cached form fields and place values accordingly.
 * Also if browser back button is pressed, there is a chance you will visit the cached version of the page 
 * so no server side calls will be made.   In this case, we check to see if the members cookie is set and if it's
 * increment is beyond 1. If so, we know that the server side script did not run and we need to account for previously 
 * set values from within the form.
 */
jQuery(document).ready(function() {     

	/////  Set the form fields based on last search.
	var form_obj = new MM_Form('mm-form-container');
	form_obj.setCachedFormFields("MM_MembersView"); 

	///// Check the members cookie counter.   
	if (mmjs.getCookie('members') !== '')
	{
		var originalValue = parseInt(mmjs.getCookie('members'));
		cookieValue = originalValue + 1;
		if(cookieValue<=1)
		{
			////// If <=1 simply store the state
			mmjs.setCookie("members",cookieValue);	
		}
		else
		{ 
			///// if >1, know the server side members script didn't run so we need to ensure the search params are adhered to.
			mmjs.search(0);
		}
	}  
}); 