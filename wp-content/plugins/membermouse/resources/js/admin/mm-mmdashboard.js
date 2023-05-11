/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_DashboardViewJS = MM_Core.extend({
	toggleTrainingVideos: function()
	{	
		if(jQuery('#mm-training-videos').is(':visible'))
		{
			this.hideTrainingVideos();
		}
		else
		{
			this.showTrainingVideos();
		}
	},
	
	showTrainingVideos: function()
	{	
		jQuery("#show-training-videos-btn").hide();
		jQuery("#hide-training-videos-btn").show();
		jQuery("#mm-training-videos").slideDown(300, this.storeTrainingVideoState);
	},
	
	hideTrainingVideos: function()
	{	
		jQuery("#show-training-videos-btn").show();
		jQuery("#hide-training-videos-btn").hide();
		jQuery("#mm-training-videos").slideUp(300, this.storeTrainingVideoState);
	},
	
	storeTrainingVideoState: function()
	{
		this.module = "MM_DashboardView";
		this.method = "performAction";
		this.action = "module-handle";
		  
		var values = {
			mm_action: "storeTrainingVideoState",
			mm_admin_id: jQuery("#mm-admin-id").val(),
			mm_show_training_videos: "0"
		}
		
		if(jQuery('#mm-training-videos').is(':visible'))
		{
			values.mm_show_training_videos = "1";
		}
		 
		 var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		 ajax.useLoader = false;
		 ajax.send(values, false, 'mmjs', "storeStateCallback"); 
	},
	
	storeStateCallback: function(data)
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
});

var mmjs = new MM_DashboardViewJS("MM_DashboardView", "");