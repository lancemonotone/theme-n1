<?php 
$error = "";
$settingsSaved = false;

// handle forgot password email
$forgotPasswordEmail = new stdClass();

$forgotPasswordEmail->subject = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_SUBJECT);
$forgotPasswordEmail->body = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_BODY);

if(!isset($forgotPasswordEmail->subject))
{
	$forgotPasswordEmail->subject = "";
}

if(!isset($forgotPasswordEmail->body))
{
	$forgotPasswordEmail->body = "";
}

if(isset($_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_SUBJECT]) && $_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_SUBJECT] != $forgotPasswordEmail->subject)
{
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_SUBJECT, $_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_SUBJECT]);
	$forgotPasswordEmail->subject = stripslashes($_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_SUBJECT]);
	$settingsSaved = true;
}

if(isset($_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_BODY]) && $_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_BODY] != $forgotPasswordEmail->body)
{
	$pattern = "\[mm_corepage_link type=(['|\"]{1})resetpassword(['|\"]{1})\]";
	
	if(!preg_match("/{$pattern}/i", stripslashes($_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_BODY])))
	{
		$error = _mmt("This template requires the following SmartTag:\\n[MM_CorePage_Link type=\'resetpassword\']\\n\\nYou\'ll need to include this SmartTag somewhere in the template before the template can be saved.");
	}
	else
	{
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_BODY, preg_replace("/{$pattern}/i", "[MM_CorePage_Link type='resetpassword']", stripslashes($_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_BODY])));
		$forgotPasswordEmail->body = preg_replace("/{$pattern}/i", "[MM_CorePage_Link type='resetpassword']", stripslashes($_POST[MM_OptionUtils::$OPTION_KEY_FORGOT_PASSWORD_BODY]));
		$settingsSaved = true;
	}
}
?>
<form name='configure_site_text' method='post' >
<div class="mm-wrap">
    <span class="mm-section-header"><?php echo _mmt("Forgot Password Email"); ?></span>
	<div id="mm-form-container" style="margin-top: 10px; margin-bottom: 15px;">	
		<div style="margin-top:5px">
			<?php echo _mmt("Subject"); ?>*
			<input name="mm-forgot-password-email-subject" type="text" style="width:454px; font-family:courier; font-size: 11px;" value="<?php echo $forgotPasswordEmail->subject ?>"/>
		</div>
		
		<div style="margin-top:5px">
			<?php echo _mmt("Body"); ?>* <?php echo MM_SmartTagLibraryView::smartTagLibraryButtons("mm-forgot-password-email-body"); ?>
			<?php 
				$validSmartTags = _mmt("Only the following SmartTags can be used here").":\n";
				$validSmartTags .= "[MM_Access_Decision] ("._mmt("you must provide an ID").")\n";
				$validSmartTags .= "[MM_Content_Data] ("._mmt("you must provide an ID").")\n";
				$validSmartTags .= "[MM_Content_Link] ("._mmt("you must provide an ID").")\n";
				$validSmartTags .= "[MM_CorePage_Link]\n";
				$validSmartTags .= "[MM_CustomField_Data]\n";
				$validSmartTags .= "[MM_Employee_Data]\n";
				$validSmartTags .= "[MM_Member_Data]\n";
				$validSmartTags .= "[MM_Member_Decision]\n";
				$validSmartTags .= "[MM_Member_Link]\n";
				$validSmartTags .= "[MM_Purchase_Link]";
			?>
			<span style="font-size:11px; color:#666666; margin-left: 5px;"><em><?php echo _mmt("Note: Only certain SmartTags can be used here"); ?></em></span>
			<?php echo MM_Utils::getInfoIcon($validSmartTags); ?>
			<div style="font-size:11px; color:#666666; margin-top:5px;"><em><?php echo _mmt("The following SmartTag is required"); ?>:</em> <strong><code>[MM_CorePage_Link type='resetpassword']</code></strong></div>
		</div>
		
		<div style="margin-top:5px">
			<textarea id='mm-forgot-password-email-body' name='mm-forgot-password-email-body' style="width:500px; height:180px; font-family:courier; font-size: 11px;"><?php echo htmlentities($forgotPasswordEmail->body, ENT_QUOTES, 'UTF-8', true); ?></textarea>
		</div>
	</div>
</div>

<input type='submit' name='submit' value='<?php echo _mmt("Save Settings"); ?>' class="mm-ui-button blue" />
</form>

<script type='text/javascript'>
<?php if(!empty($error)){ ?>
alert('<?php echo $error; ?>');
<?php  } ?>
<?php if($settingsSaved){ ?>
alert('<?php echo _mmt("Settings saved successfully"); ?>');
<?php  } ?>
</script>