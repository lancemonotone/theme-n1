var MM_ResetPasswordFormViewJS = MM_Core.extend({
  
  resetPassword: function(form)
  {
    this.form = form;
    
    jQuery("input[type='submit']", this.form).val("Sending...").attr("disabled", true);
        
    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
    
    var values = {
      mm_action: "resetPassword",
      k: jQuery('#k', this.form).val(),
      e: jQuery('#e', this.form).val(),
      pwd: jQuery('#password', this.form).val(),
      pwd_confirm: jQuery('#password_confirm', this.form).val(),
      referer: jQuery("input[name='_wp_http_referer']", this.form).val(),
    };
    
    values[mm_nonce_name_resetpassword_form] = jQuery("input[name='"+mm_nonce_name_resetpassword_form+"']", this.form).val();
    
    ajax.send(values, false, 'mmjs', 'resetPasswordCallback');
  },
  
  resetPasswordCallback: function(data)
  {
    console.log(data);
    
    if(data.data.redirect_to != undefined)
		{
			window.location.href = data.data.redirect_to;
		}
		else
		{
  		alert(data.message);
  		jQuery("input[type='submit']", this.form).val("Submit").attr("disabled", false);
		}	
  }
  
});

jQuery(document).ready(function(){
  jQuery("#mm-resetpassword-form").on("submit", function(){
    mmjs.resetPassword(jQuery(this));
    return false;
  });
});

var mmjs = new MM_ResetPasswordFormViewJS("MM_ResetPasswordFormView", "Reset Password Form");