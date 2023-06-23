<?php

/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$accessRights = new MM_AccessRightsView();

if (!isset($p->day))
    $p = $accessRights->getViewData();
?> 
<style>
.tab {
  overflow: hidden; 
  cursor: pointer;
  background-color: #fff;
  font-size:1.1em;
  font-weight: 300;
  transition: 0.3s;
  border: 1px solid #066cd2;
  border-radius: 5px 5px 0px 0px;
  padding: 10px 20px;
  color: #066cd2;
  text-decoration: none;
} 

/* Create an active/current tablink class */
.active {
  background: #46a0f9;
  color: #fff;
  font-weight:700;
  border: 2px solid #066cd2;
}
 
</style>
<script type='text/javascript'>  
function openTab(tabName) { 
    if(tabName=="mm_access_rights_div")
    {
        jQuery("#tab1").addClass("active");
        jQuery("#tab2").removeClass("active");
        jQuery("#mm_access_rights_div").show();
        jQuery("#mm_access_rights_div2").hide();
        accessrights_js.showOptionsOnPageList('<?php echo $p->access_id; ?>', '<?php echo $p->access_type; ?>','#mm_access_rights_div');
    } 
    else if(tabName=="mm_access_rights_div2")
    {   
        accessrights_js.getAccessRightsForPosts(false);
        jQuery("#tab2").addClass("active");
        jQuery("#tab1").removeClass("active");
        jQuery("#mm_access_rights_div2").show();
        jQuery("#mm_access_rights_div").hide(); 
    } 
  return false;
}
</script>
<div style="margin-top:10px" >
  <button id="tab1" class="tab" onclick="return openTab('mm_access_rights_div')">Add Access</button>
  <button id="tab2"  class="tab" onclick="return openTab('mm_access_rights_div2')">Remove Access</button> 
</div>
<div id='mm_access_rights_div2' class="tabcontent" style='height: 358px; border: 1px solid #eee; width: 500px; padding: 0px 10px 10px 10px; float: left;display:none'>
    <div style="clear:both; margin-left:5px; padding-top:10px"> 
        <span style="font-size:1.2em; font-weight:700;">Step 1: Select Access Rights to Remove</span>
        <div class="inline-edit-group wp-clearfix" id="mm_remove_div" style="margin-top:10px; overflow:auto; height: 200px"></div>

        <div style='clear:both; width: 7px; height: 10px'></div>   
        <div style="width: 650px;"> 
            <span style="font-size:1.2em; font-weight:700;">Step 2: Click the Update Button</span>
        </div>
    </div>
</div>
<div id='mm_access_rights_div' class="tabcontent" style='height: 358px; border: 1px solid #eee; width: 500px; padding: 0px 10px 10px 10px; float: left;display:none'>
 
    <div style="clear:both; margin-left:5px; padding-top:10px;">  
        <span style="font-size:1.2em; font-weight:700;">Step 1: Select Who Gets Access</span>
        <div style="margin-left:10px;" class="inline-edit-group wp-clearfix">
            <label class="inline-edit-grant-access-by">Grant access to members with ...</label> 
            <select id="access_rights_choice" name="access_rights_choice" onchange="accessrights_js.showOptionsOnPageList('<?php echo $p->access_id; ?>', '','#mm_access_rights_div')">
                <?php echo $p->access_rights_choice; ?>
            </select>
            <select id='mm_access_tags_opt' name='mm_access_tags_opt' style="width: 150px"><?php echo ((isset($p->options)) ? $p->options : ""); ?></select>
            <select id='mm_member_types_opt' name='mm_member_types_opt' style="width: 150px"><?php echo ((isset($p->options)) ? $p->options : ""); ?></select>
            <div style="height: 2px;"></div>
            <label class="inline-edit-grant-access-by" >Grant access on day
                <input type='text' id='at_day' name='at_day' style="width:40px;" value='<?php echo $p->day; ?>' />
                <input type='text' name='mt_day' id='mt_day' style="width:40px;" value='<?php echo $p->day; ?>' />
            </label> 
            <input type='button' style="margin-top:10px" class="mm-ui-button green" name='add' value='Add Access Rights' onclick="accessrights_js.addPermission('#mm_access_rights_div')" />
        </div>
        <div style='clear:both; width: 7px; height: 10px'></div> 
        <input type='hidden' id='edit_id' name='edit_id' value='<?php echo ((isset($p->access_id)) ? $p->access_id : ''); ?>' />
        <div style="width: 650px;"> 
            <span style="font-size:1.2em; font-weight:700;">Step 2: Review Access Rights to Add</span>
            <div style="height: 2px;"></div>
            <div id='mm-access-description' style="margin-left:10px; overflow: auto; width: 320px; height: 108px">
                <span id="mm_default_msg">As you add access rights above, they will be listed here.</span>
            </div> 
        </div>
        <div style='clear:both; width: 7px; height: 10px'></div>   
        <div style="width: 650px; margin-bottom:5px;"> 
            <span style="font-size:1.2em; font-weight:700;">Step 3: Click the Update Button</span>
        </div>
    </div>
</div>
<div style='clear:both; height: 20px;'></div>
<input type='hidden' id="mm_tab_choice" value="1" />
<script type='text/javascript'>
    jQuery(document).ready(function() {
        jQuery("#mm_access_rights_div2 #mm_access_tags_opt").hide();
        jQuery("#mm_access_rights_div #mm_access_tags_opt").hide();
        openTab("mm_access_rights_div");
    });
</script>