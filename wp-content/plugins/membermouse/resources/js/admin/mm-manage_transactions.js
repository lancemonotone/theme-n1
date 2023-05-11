/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_ManageTransactionsViewJS = MM_Core.extend({
  
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
			alert("Please enter a valid email address");
			return false;
		}
		
		return true;
	},
	  
	  resetForm: function()
	  {
		  var values = {
				  mm_action: "resetForm"
		  };
	  
		  var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		  ajax.send(values, false, "mmjs", "resetFormHandler");
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
	  
	  search: function(crntPage) 
	  {
		  var form_obj = new MM_Form('mm-form-container');
		  var values = form_obj.getFields();
		  
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
	  
	  csvExport:function(crntPage)
	  {
		  var form_obj = new MM_Form('mm-form-container');
		  var values = form_obj.getFields();
		  
		  if(crntPage != undefined) {
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
			  jQuery('<input>').attr({ type: 'hidden',
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
	  
	  resetGridHandler: function(data)
	  {
		  if(data) {
			  jQuery("#mm-grid-container").html(data);
		  }
	  }
});

var lastActionValues = {};
var mmjs = new MM_ManageTransactionsViewJS("MM_ManageTransactionsView", "Transaction");