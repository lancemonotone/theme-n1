var MM_LoginFormViewJS = MM_Core.extend({
  
  login: function(form)
  {
    this.form = form;
    
    jQuery("input[type='submit']", this.form).val("Sending...").attr("disabled", true);
        
    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
    
    var values = {
      mm_action: "login",
      log: jQuery('#log', this.form).val(),
      pwd: jQuery('#pwd', this.form).val(),
      rememberme: jQuery('#rememberme', this.form).is(":checked"),
      referer: jQuery("input[name='_wp_http_referer']", this.form).val(),
    };
    
    values[mm_nonce_name_login_form] = jQuery("input[name='"+mm_nonce_name_login_form+"']", this.form).val();
    
    ajax.send(values, false, 'mmjs', 'loginCallback');
  },
  
  loginCallback: function(data)
  {
    if(data.data.redirect_to != undefined)
		{
			window.location.href = data.data.redirect_to;
		}
		else
		{
  		alert(data.message);
  		jQuery("input[type='submit']", this.form).val("Login").attr("disabled", false);
		}	
  }
  
});

jQuery(document).ready(function(){
  jQuery("#mm-login-form").on("submit", function(){
    mmjs.login(jQuery(this));
    return false;
  });
});

var mmjs = new MM_LoginFormViewJS("MM_LoginFormView", "Login Form");