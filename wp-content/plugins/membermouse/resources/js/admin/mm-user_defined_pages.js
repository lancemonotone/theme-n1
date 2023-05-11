/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_UserDefinedPageViewJS = MM_Core.extend({
	createPage: function()
	{
		this.processForm();
		if(this.validateForm()) 
		{
			var form_obj = new MM_Form('mm-pages-container');
		    var values = form_obj.getFields();
		    values.mm_action = "save";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', this.updateHandler); 
		}
	},
	
	processForm: function()
	{
		
	},
	
	validateForm: function()
	{
		if(jQuery("#mm_page_name").val()==""){
			return this.throwError("Please enter a name for the page");
		}
		else if(jQuery("#mm_page_url").val()==""){
			return this.throwError("Please enter a URL for the page");
		}
		return true;
	},
	
	throwError: function(msg){
		alert(msg);
		return false;
	},
	
});

var mmjs = new MM_UserDefinedPageViewJS("MM_UserDefinedPageView", "User-Defined Page");