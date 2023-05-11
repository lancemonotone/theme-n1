/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_GiftViewJS = MM_Core.extend({
		storeSearchOptions: function()
		{
			this.module = "MM_GiftView";
			this.method = "performAction";
			this.action = "module-handle";
			  
			var values = {
				mm_action: "storeSearchOptions",
				mm_admin_id: jQuery("#mm-admin-id").val(),
				mm_gift_status: "all"
			}
			 
			if(jQuery('#mm_gift_status').val())
			{
				values.mm_gift_status = jQuery('#mm_gift_status').val();
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
				document.location.href = document.location.href;
			}
		},
	
	processForm: function(){
		
	},
	
	throwError: function(msg){
		alert(msg);
		return false;
	},
	
});

var mmjs = new MM_GiftViewJS("MM_GiftView", "Gift");