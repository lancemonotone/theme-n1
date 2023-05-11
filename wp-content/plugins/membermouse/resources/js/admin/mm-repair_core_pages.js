/*!
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_RepairCorePagesViewJS = MM_Core.extend({
  
	repairCorePageProblems: function()
	{
        var values = {
            mm_action: "repairCorePages"
        };

        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs',this.updateHandler); 
	}
});

var mmjs = new MM_RepairCorePagesViewJS("MM_RepairCorePagesView", "");