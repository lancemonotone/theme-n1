/*!
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_SafeModeViewJS = MM_Core.extend({
  
	setSafeModeStatus: function(safeModeStatus)
	{
		var doSet = true;
		
		if (safeModeStatus != "off")
		{
			doSet = confirm("Are you sure you want to enable Safe Mode? \n\nBy clicking OK MemberMouse will do the following:\n" +
							"- deactivate all plugins besides MemberMouse\n" +
							"- activate the default WordPress theme (if available)\n" +
							"- store the list of currently active plugins & theme so that\nthey can be reactivated when Safe Mode is disabled\n\n" +
					        "Click OK to enable Safe Mode, or Cancel to abort");
		}
			
	    if(doSet)
	    {
	        var values = {
	            newStatus: safeModeStatus,
	            mm_action: "setSafeModeStatus"
	        };

	        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	        ajax.send(values, false, 'mmjs',this.updateHandler); 
	    }
	},
	
	clearLog: function()
	{
		var doSet = true;
		
		doSet = confirm("Are you sure you want to clear the safe mode log?");
			
	    if(doSet)
	    {
	        var values = {
	            mm_action: "clearSafeModeLog"
	        };

	        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	        ajax.send(values, false, 'mmjs',this.updateHandler); 
	    }
	},
});

var mmjs = new MM_SafeModeViewJS("MM_SafeModeView", "");