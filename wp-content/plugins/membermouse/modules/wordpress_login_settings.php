<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_login_page"]))
{
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_USE_MM_LOGIN_PAGE, $_POST["mm_login_page"]);
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_USE_MM_RESET_PASSWORD_PAGE, $_POST["mm_reset_password_page"]);
}

$useMMLoginPage = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_USE_MM_LOGIN_PAGE);
$useMMResetPasswordPage = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_USE_MM_RESET_PASSWORD_PAGE);
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 

<script>
function updateWPLoginPageForm()
{	
	if(jQuery("#mm_login_page_cb").is(":checked")) 
	{
		jQuery("#mm_login_page").val("1");
	} 
	else 
	{
		jQuery("#mm_login_page").val("0");
	}

	if(jQuery("#mm_reset_password_page_cb").is(":checked")) 
	{
		jQuery("#mm_reset_password_page").val("1");
	} 
	else 
	{
		jQuery("#mm_reset_password_page").val("0");
	}
}
</script>

<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("WordPress Login Settings"); ?></p>
    
	<div style="margin-top:10px;">
		<input id="mm_login_page_cb" type="checkbox" <?php echo (($useMMLoginPage=="1")?"checked":""); ?> onchange="updateWPLoginPageForm();" />
		<?php echo _mmt("Use the MemberMouse Login Page as the Default"); ?>
		<input id="mm_login_page" name="mm_login_page" type="hidden" value="<?php echo $useMMLoginPage; ?>" />
		
		<span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020527-configure-the-default-login-page" target="_blank"><?php echo _mmt("Learn more"); ?></a></span>
	</div>
	
	<div style="margin-top:10px;">
		<input id="mm_reset_password_page_cb" type="checkbox" <?php echo (($useMMResetPasswordPage=="1")?"checked":""); ?> onchange="updateWPLoginPageForm();" />
		<?php echo _mmt("Use the MemberMouse Reset Password Page as the Default"); ?>
		<input id="mm_reset_password_page" name="mm_reset_password_page" type="hidden" value="<?php echo $useMMResetPasswordPage; ?>" />
		
		<span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020539-use-wordpress-reset-password-page" target="_blank"><?php echo _mmt("Learn more"); ?></a></span>
	</div>
</div>