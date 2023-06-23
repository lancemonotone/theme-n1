/*!
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
var MM_AccessRightsView = MM_Core.extend({
  
  /** DATA GRID FUNCTIONS **/
  refreshGrid: function(sortBy, sortDir)
  {
    var values = {
      post_ID: jQuery("#post_ID").val(),
      post_type: jQuery("#post_type").val(),
      mm_action : 'refreshMetaBox'
    };
    
    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
    ajax.send(values, false, 'accessrights_js','listCallback'); 
  },
  
  showError: function(str)
  {
      alert(str);
  },
  
  validate: function()
  {
    var day = "";
    var type_dd = "";
    var type_error = "";
    if(jQuery("#access_rights_container_at_table").is(':hidden')) 
    {
        type_dd =jQuery("#mm_member_types_opt").val();
        day = jQuery("#mt_day").val();
         type_error = "Membership Levels not defined.";
    }
    else
    {
         type_error = "Bundles not defined.";
        type_dd =jQuery("#mm_access_tags_opt").val(); 
        day = jQuery("#at_day").val();
    }
     if(type_dd =="" || type_dd<=0)
     {
        this.showError(type_error);
        return false;
    }
      var reg = new RegExp("^[0-9]+$");
      if(!reg.test(day))
      {
        this.showError("Days field must be greater than or equal to 0");
        return false;
      }    
      return true;
  },
  
  /** DATABASE FUNCTIONS **/
  save: function() 
  {
      var form_obj = new MM_Form('mm-access_container_div');
      var values = form_obj.getFields();
      values['post_ID'] = jQuery("#post_ID").val();
      values['type'] = 'access_tag';

      if(jQuery("#access_rights_container_at_table").is(':hidden')) 
      {
          values['day'] = jQuery("#mt_day").val();
          values['type'] = 'member_type';
      }
      else
      {
        values['day'] = jQuery("#at_day").val();
      }
      
      values.mm_action = "save";
      if(!this.validate()) 
      {
        return false;
      }
      
      var module = "MM_AccessRightsView";
      var method = "performAction";
      var action = 'module-handle';
      var ajax = new MM_Ajax(false, module, action, method);
      ajax.send(values, false, 'accessrights_js','saveCallback'); 
  },
  
  edit: function(dialogId, access_id, access_type)
  {
	var values = {};
	values.post_ID=jQuery("#post_ID").val();
	values.access_type = access_type;
	values.access_id = access_id;
	values.mm_action = 'editAccessRight';
	mmdialog_js.method = 'performAction';
	mmdialog_js.showDialog(dialogId, this.module, 420, 270, "Edit "+this.entityName,values);
  },
  
  remove: function(access_id, access_type)
  {
    var removeOk = confirm("Are you sure you want to remove this access right?");
    if(removeOk)
    {
        var values = {
            access_id: access_id,
            access_type: access_type,
            post_ID: jQuery("#post_ID").val(),
            mm_action: "removeAccessRights"
        };
        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'accessrights_js','removeCallback'); 
    }
  },
  
  removeCallback: function(data)
  {
    if(data.type=='error')
    {
    	alert(data.message);
        return false;
    }
    this.refreshGrid();
  },

  listCallback: function(data)
  {
	  if(data.type=='error')
	  {
		  alert(data.message);  
	  }
	  else
	  {
		  jQuery("#mm_publish_box").html(data.message);
    } 
  },
  
  saveCallback: function(data)
  { 
    this.closeDialog();

	  if(data.type=='error')
	  {
		  alert(data.message);  
	  }
	  else
	  {
          this.refreshGrid(); 
	  }
   },

   /**
    * BULK-EDIT Function
    * This is a helper function to append checkboxes for access removal on the bulk edit screen.
    * @param {*} access is the object referring to the specific access right to be displayed.
    */
    addRemoveAccessCheckbox: function(access)
    { 
        var icon = '<i class="fa fa-user mm-icon blue" style=" font-size:1.3em; position:relative; top:2px;"></i>';
        if(access.access_type != "member_type")
        {
            var icon = '<i class="fa fa-cube mm-icon yellow" style=" font-size:1.3em; position:relative; top:2px;"></i>';
        }
    	jQuery('#mm_remove_div').append(jQuery(document.createElement('input')).prop({
    		id: 'access_'+access.access_id,
    		name: 'access_rights_removed[]',
    		value: access.access_type+"-"+access.access_id,
    		type: 'checkbox',
            style: "margin:0px"
    	})).append(" ").append(
    		jQuery(document.createElement('label')).prop({
    			for: 'access_'+access.access_id,
                style: "vertical-align:baseline; display:inline;"
    		}).html("&nbsp;&nbsp;"+icon+" "+access.access_name)
    		).append("<div style='clear:both;'></div>")
    },

    /**
     * BULK-EDIT Function
     * This is the callback function for when we get the updated list of access rights for a specific post.
     * This builds a list of checkboxes to allow end-user to select which access rights to be potentially removed.
     */
   getAccessRightsForPostsCallback: function(data)
   { 
        jQuery("#mm_remove_div").empty();

        var cnt=0;
        var rights = data.message.rights;
        for(var id in rights)
        {
            var access = rights[id];
            accessrights_js.addRemoveAccessCheckbox(access);
            cnt++;
        }  

        if(cnt<=0)
        {
            jQuery("#mm_remove_div").html("There are no access rights to remove on the selected items.");
        }

        if(data.message.display!=undefined && data.message.display!=null && data.message.display.length>0)
        { 
            if(jQuery("#mm-custom-bulk-id").length)
            {
                jQuery("#mm-custom-bulk-id").show();
            }
            else
            {
                jQuery("<fieldset id='mm-custom-bulk-id' class='inline-edit-col-right'>"+data.message.display+"</fieldset>").insertAfter(jQuery("#bulk-edit td fieldset").last());
            } 
        }
   },

   /**
    * BULK-EDIT Function
    * This does the initial query to the accessrightsview to obtain the rights for the given selected posts/pages
    * @param {*} reloadFormData true if you want to return the access rights form data along with the rights themselves, otherwise false and just return the access rights.
    */
   getAccessRightsForPosts: function(reloadFormData)
   {  
        var postIds= [];
        jQuery('input[name="post[]"]:checked').each(function () {  
            postIds.push(jQuery(this).val());
        });

        var values = { 
            post_ids: postIds,
            reload: (reloadFormData)?1:0,
            mm_action : 'getAccessRightsForPosts'
        };   
        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'accessrights_js','getAccessRightsForPostsCallback'); 
   },
 
    /**
     * BULK-EDIT Function
     * This function is leveraged on the posts and pages lists within WP.
     * The separation is clear between the in-page edit access and this list as the form
     * layout is significantly different.  
     */
   optionsCallbackFromPageList: function(data)
   {
       var divId = jQuery("#mm_tab_choice").val(); 
	   if(data.type=='error')
	   {
		   alert(data.message);
	   }
	   else
	   { 
			if(jQuery(divId+" #mm_access_tags_opt").is(':hidden')) 
			{
			    jQuery(divId+" #mm_member_types_opt").find('option').remove().end().append(data.message);
			}
			else
			{
			    jQuery(divId+" #mm_access_tags_opt").find('option').remove().end().append(data.message);
			}
	   }
   },
   
   /**
    * BULK-EDIT Function
    * @param {*} id is the id of the given access right
    * @param {*} access_type is the type of access (member|access tag)
    * @param {*} divId This is to identify which div the options are in to ensure we are updating appropriately.
    */
   showOptionsOnPageList: function(id, access_type, divId)
   {    
        jQuery("#mm_tab_choice").val(divId);

        var rights = ""; 
        if(access_type=="member_type")   
            rights = 'mt';
        else if(access_type == "access_tag")
            rights = 'at';
        else
            rights = jQuery(divId+" #access_rights_choice").val();
            
        var values = {
            id:id,
            type:rights, 
            post_ID:jQuery("#post_ID").val(),
            mm_action : 'getAccessRightsOptions'
        };  
        
        if(rights=='mt')
        {
            jQuery(divId+" #mm_access_tags_opt").hide();
            jQuery(divId+" #at_day").hide();
            jQuery(divId+" #mm_member_types_opt").show();
            jQuery(divId+" #mt_day").show();
        }   
        else
        {
            jQuery(divId+" #mm_access_tags_opt").show();
            jQuery(divId+" #at_day").show();
            jQuery(divId+" #mm_member_types_opt").hide();
            jQuery(divId+" #mt_day").hide();
        }   
        
        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'accessrights_js','optionsCallbackFromPageList'); 
   }, 

   removeAccessRightFromList: function(divId)
   { 
       if(jQuery("#"+divId).length)
       {
            jQuery("#"+divId).remove();
       }
   },

   /**
    * This is a utility function to dynamically add access permissions to selected posts.
    * It grabs the existing selections and appends them to a div.
    */
   addPermission: function(parentDiv)
   { 
       jQuery(parentDiv+" #mm_default_msg").replaceWith("");
       var accessType = jQuery(parentDiv+" #access_rights_choice option:selected").val();
       var accessVal = jQuery(parentDiv+" #mm_member_types_opt option:selected").val();
       var days = jQuery(parentDiv+" #mt_day").val();
       if(accessType=="at")
       {
           days = jQuery(parentDiv+" #at_day").val();
           accessVal = jQuery(parentDiv+" #mm_access_tags_opt option:selected").val();
       }
       var key = "mm-"+accessType+"_"+accessVal; 
       key = key.trim();
       
       var accessTypeHidden = "<input type='hidden' name='access_rights_choice_val[]' value='"+accessType+"' />";
       var accessValHidden = "<input type='hidden' name='mm_member_types_opt_val[]' value='"+accessVal+"' />";
       var accessDaysHidden = (parentDiv.indexOf("mm_access_rights_div2")<0)?"<input type='hidden' name='mt_day_val[]' value='"+days+"' />":"";
       var accessFields = accessDaysHidden+accessTypeHidden+accessValHidden
       var memberIcon = '<i class="fa fa-user mm-icon blue" style=" font-size:1.3em; position:relative; top:2px;"></i>';
       var accessTagIcon = '<i class="fa fa-cube mm-icon yellow" style=" font-size:1.3em; position:relative; top:2px;"></i>';

       var accessRm = "<a onclick='accessrights_js.removeAccessRightFromList(\""+key+"\")' style='cursor:pointer'><i class='fa fa-trash mm-icon red' style=' font-size:1.3em; position:relative; top:2px; left:5px;'></i></a>";
       var accessLbl = memberIcon+" "+jQuery(parentDiv+" #mm_member_types_opt option:selected").text();
       if(parentDiv.indexOf("mm_access_rights_div2")<0)
       {
            accessLbl+="  on  day "+jQuery(parentDiv+" #mt_day").val()+" ";
       }
       if(accessType == "at")
       {
            accessLbl = accessTagIcon+" "+jQuery(parentDiv+" #mm_access_tags_opt option:selected").text();
            if(parentDiv.indexOf("mm_access_rights_div2")<0)
            {
                accessLbl+="  on day "+jQuery(parentDiv+" #at_day").val()+" ";
            }
       }  

       if(jQuery(parentDiv+" #"+key).length) 
       {
           jQuery(parentDiv+" #"+key).html(accessFields+accessLbl+accessRm);
       }
       else
       {
           var div = "<div style='margin-bottom:2px;' id='"+key+"'>"+accessFields+accessLbl+accessRm+"</div>";
           jQuery(parentDiv+" #mm-access-description").append(div);
       } 
   }, 

   optionsCallback: function(data)
   {
	   if(data.type=='error')
	   {
		   alert(data.message);
	   }
	   else
	   {
			if(jQuery("#access_rights_container_at_table").is(':hidden')) 
			{
			    jQuery("#mm_member_types_opt").find('option').remove().end().append(data.message);
			}
			else
			{
			    jQuery("#mm_access_tags_opt").find('option').remove().end().append(data.message);
			}
	   }
   },

   showOptions: function(id, access_type)
   {    
        var rights = ""; //(access_type!='')?access_type:jQuery("#access_rights_choice").val();
        if(access_type=="member_type")   
            rights = 'mt';
        else if(access_type == "access_tag")
            rights = 'at';
        else
            rights = jQuery("#access_rights_choice").val();
            
        var values = {
            id:id,
            type:rights, 
            post_ID:jQuery("#post_ID").val(),
            mm_action : 'getAccessRightsOptions'
        };  
        
        if(rights=='mt')
        {
            jQuery("#access_rights_container_at_table").hide();
            jQuery("#access_rights_container_mt_table").show();
        }   
        else
        {
            jQuery("#access_rights_container_at_table").show();
            jQuery("#access_rights_container_mt_table").hide();
        }   
        
        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'accessrights_js','optionsCallback'); 
   }
});

var accessrights_js = new MM_AccessRightsView("MM_AccessRightsView", "Access Rights");

