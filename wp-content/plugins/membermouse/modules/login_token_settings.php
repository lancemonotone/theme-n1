<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$error = "";
if(isset($_POST[MM_OptionUtils::$OPTION_KEY_LOGIN_TOKEN_LIFESPAN]))
{
	if(!preg_match("/[0-9]+/", $_POST[MM_OptionUtils::$OPTION_KEY_LOGIN_TOKEN_LIFESPAN]) || intval($_POST[MM_OptionUtils::$OPTION_KEY_LOGIN_TOKEN_LIFESPAN]) <= 0)
	{
		$error = _mmt("Login token lifespan must be greater than 0.");
	}
	
	if(empty($error))
	{
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_LOGIN_TOKEN_LIFESPAN, $_POST[MM_OptionUtils::$OPTION_KEY_LOGIN_TOKEN_LIFESPAN]);
	}
}

$lifespan = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_LOGIN_TOKEN_LIFESPAN);

if(!preg_match("/[0-9]+/", $lifespan))
{
	$lifespan = MM_OptionUtils::$DEFAULT_LOGIN_TOKEN_LIFESPAN;
}

$loginTokenLifespanDesc = "When the auto-login attribute is set to true on certain [MM_..._Link] SmartTags, a login token is created that allows the customer to be automatically logged in when they click the link. ";
$loginTokenLifespanDesc .= "This setting indicates how long that login token will be valid.";
?>

<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 

<div class="mm-wrap">
	<p class="mm-header-text"><?php echo _mmt("Login Token Settings"); ?> <span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020549-auto-login-links-and-login-tokens" target="_blank"><?php echo _mmt("Learn more"); ?></a></span></p>
	
	<div style="margin-top:10px;">
		<?php echo sprintf(_mmt("Login tokens are valid for %s days"),"<input type='text' style='width: 50px;' name='". MM_OptionUtils::$OPTION_KEY_LOGIN_TOKEN_LIFESPAN."' value='".$lifespan."' />");?>
		<?php echo MM_Utils::getInfoIcon($loginTokenLifespanDesc, ""); ?>
	</div>
</div>