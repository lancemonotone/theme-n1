<?php 
if(isset($_POST["mm_captcha_public_key"]))
{
	MM_CaptchaUtils::setSiteKey($_POST["mm_captcha_public_key"]);
	MM_CaptchaUtils::setSecretKey($_POST["mm_captcha_private_key"]);
	MM_CaptchaUtils::setCaptchaVersion($_POST["mm_captcha_version"]);
	MM_CaptchaUtils::setErrorMessage($_POST["mm_error_message"]);
	
	$scoreThreshold = floatval($_POST["mm_score_threshold"]);
	
	if(is_numeric($_POST["mm_cleanup_interval"]) && intval($_POST["mm_cleanup_interval"]) > 0)
	{
	   MM_CaptchaUtils::setCaptchaLogCleanupInterval($_POST["mm_cleanup_interval"]);
	}
	else 
	{
	    $error = _mmt("reCAPTCHA log cleanup interval must be a number greater than 0.");
	}
	
	if(is_numeric($_POST["mm_score_threshold"]) && $scoreThreshold >= 0 && $scoreThreshold <= 1)
	{
	    MM_CaptchaUtils::setScoreThreshold($scoreThreshold);
	}
	else
	{
	    $error = _mmt("reCAPTCHA score threshold must be a number between 0 and 1.");
	}
	
	if(preg_match("/^[a-zA-Z0-9\\/]+$/", $_POST["mm_action_name"]))
	{
	    MM_CaptchaUtils::setActionName($_POST["mm_action_name"]);
	}
	else
	{
	    $error = _mmt("reCAPTCHA action name can only contain letters, numbers and slashes.");
	}
	
	if($_POST["mm_enable_captcha"] == "on")
	{
	    MM_CaptchaUtils::enableCaptcha(true);
	}
	else
	{
	    MM_CaptchaUtils::enableCaptcha(false);
	}
	
	if($_POST["mm_hide_captcha_badge"] == "on")
	{
	    MM_CaptchaUtils::setHideCaptchaBadge(true);
	}
	else
	{
	    MM_CaptchaUtils::setHideCaptchaBadge(false);
	}
}

$captchaVersion = MM_CaptchaUtils::getCaptchaVersion();
$siteKey = MM_CaptchaUtils::getSiteKey();
$privateKey = MM_CaptchaUtils::getSecretKey();
$captchaEnabled = (MM_CaptchaUtils::isCaptchaEnabled() == true)?1:0;
$cleanupInterval = MM_CaptchaUtils::getCaptchaLogCleanupInterval();
$hideCaptchaBadge = MM_CaptchaUtils::hideCaptchaBadge();
$scoreThreshold = MM_CaptchaUtils::getScoreThreshold();
$actionName = MM_CaptchaUtils::getActionName();
$errorMessage = MM_CaptchaUtils::getErrorMessage();
?>
<script>
function showCaptchaSettings()
{
	if(jQuery("#mm-cb-enable-captcha").is(":checked")) 
	{
		jQuery("#mm-captcha-settings").show();
	} 
	else 
	{
		jQuery("#mm-captcha-settings").hide();
	}
}

function toggleCaptchaVersion()
{
	if(jQuery("input:radio[name=mm_captcha_version]:checked").val() == "<?php echo MM_CaptchaUtils::$CAPTCHA_VERSION_3; ?>") 
	{
		jQuery("#captcha_v2_settings").hide();
		jQuery("#captcha_v3_settings").show();
	} 
	else 
	{
		jQuery("#captcha_v2_settings").show();
		jQuery("#captcha_v3_settings").hide();
	}
}

function showHideCaptchaInfo()
{
	if(jQuery("#mm-cb-hide-captcha-badge").is(":checked")) 
	{
		jQuery("#mm-hide-captcha-info").show();
	} 
	else 
	{
		jQuery("#mm-hide-captcha-info").hide();
	}
}
</script>
<form method='post'>
<div class="mm-wrap">
    <div style="margin-bottom:10px;">
		<img src="https://membermouse.com/assets/plugin_images/logos/recaptcha_v2.png" style="vertical-align:middle; margin-right:10px;" />
		<a target="_blank" href="https://www.google.com/recaptcha/admin/create#createsite" class="mm-ui-button green"><?php echo _mmt("Create a Free Account"); ?></a> 
		<a target="_blank" href="https://www.google.com/recaptcha/admin" class="mm-ui-button"><?php echo _mmt("Google reCAPTCHA Admin"); ?></a>
	</div>
	
	<div style="margin-top:10px;margin-left:10px;">
		<input onchange="showCaptchaSettings()" id="mm-cb-enable-captcha" name="mm_enable_captcha" type="checkbox"  <?php echo (($captchaEnabled == "0")?"":"checked"); ?>  />
		<?php echo _mmt("Enable reCAPTCHA on All Checkout Forms"); ?>
		
		<div id="mm-captcha-settings" style="margin-left:25px; margin-top:15px;">
			<label>
    			<input onchange="toggleCaptchaVersion()" name="mm_captcha_version" value='<?php echo MM_CaptchaUtils::$CAPTCHA_VERSION_3; ?>' type="radio" <?php echo (($captchaVersion == MM_CaptchaUtils::$CAPTCHA_VERSION_3)?"checked":""); ?>  />
    			<?php echo _mmt("reCAPTCHA v3"); ?> (<i>recommended</i>)
    		</label>
    		
    		&nbsp;&nbsp;
    		
    		<label>
    			<input onchange="toggleCaptchaVersion()" name="mm_captcha_version" value='<?php echo MM_CaptchaUtils::$CAPTCHA_VERSION_2; ?>' type="radio" <?php echo (($captchaVersion == MM_CaptchaUtils::$CAPTCHA_VERSION_2)?"checked":""); ?>  />
    			<?php echo _mmt("reCAPTCHA v2"); ?>
    		</label>
    		
    		<div style="margin-top:15px; margin-left:15px;">
            	<table>
            		<tr>
            			<td width='70'><?php echo _mmt("Site Key"); ?> </td>
            			<td>
            				<span style="font-family: courier; font-size: 11px;">
            				<input type='text' id='mm_captcha_public_key' name='mm_captcha_public_key' value='<?php echo $siteKey; ?>' size="45" />
            				</span>
            			</td>
            		</tr>
            		<tr>
            			<td width='70'><?php echo _mmt("Secret Key"); ?></td>
            			<td>
            				<span style="font-family: courier; font-size: 11px;">
            				<input type='text' id='mm_captcha_private_key' name='mm_captcha_private_key' value='<?php echo $privateKey; ?>' size="45" />
            				</span>
            			</td>
            		</tr>
            	</table>
            </div>
    		
    		<div id="captcha_v3_settings" style="margin-top:20px; margin-left:15px;">
    			
    			<p>
    			<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_LOGS, MM_MODULE_CAPTCHA_IPN_LOG); ?>" class='mm-ui-button'>
                	<i class="fa fa-shield"></i> <?php echo _mmt("View reCAPTCHA Log"); ?>
                </a>
    			</p>
    			
    			<div style="margin-top:20px;">
        			<i class="fa fa-trash"></i> <strong><?php echo _mmt("reCAPTCHA Log Cleanup"); ?></strong>
        			<div id="mm-captcha-log-cleanup" style="margin-left:25px; margin-top:5px; padding-top:5px;">
                		<?php echo sprintf(_mmt("Delete reCAPTCHA log entries older than %s days"),'<input id="mm-cleanup-interval" name="mm_cleanup_interval" type="text" size="5" value="'.$cleanupInterval.'" /> '); ?>		
                	</div>
            	</div>
    			
    			<div style="margin-top:20px;">
        			<i class="fa fa-star"></i> <strong><?php echo _mmt("Score Threshold"); ?></strong>
        			<div style="margin-left:25px; width:600px; margin-top:10px;">
        			<p>
        			<div class="updated" style="padding:10px; border-left-color:#999">reCAPTCHA v3 returns a score for each request (1.0 is very likely a good interaction, 0.0 is very likely a bot). 
        			Below you can set the score threshold to use for free signups and paid signups. Any requests resulting in a score 
        			less than the thresholds set below will be blocked so be cautious how you set this.</p>
        			<p>
        			reCAPTCHA learns by seeing real traffic on your site. To start, it's safe to use a threshold of 0.5. 
        			Over time you can adjust these thresholds based on reviewing traffic in the <a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_LOGS, MM_MODULE_CAPTCHA_IPN_LOG); ?>">MemberMouse reCAPTCHA log</a> 
        			or the <a href="https://g.co/recaptcha/admin" target="_blank">Google reCAPTCHA admin console</a>.</p>
        			
        			</div>
        			</p>
        			<p>
        				<?php 
        				   
            				$scoreValuesList = [];
            				$scoreValuesList[""] = "";
            				$scoreValuesList["0.0"] = "0.0";
            				$scoreValuesList["0.1"] = "0.1";
            				$scoreValuesList["0.2"] = "0.2";
            				$scoreValuesList["0.3"] = "0.3";
            				$scoreValuesList["0.4"] = "0.4";
            				$scoreValuesList["0.5"] = "0.5";
            				$scoreValuesList["0.6"] = "0.6";
            				$scoreValuesList["0.7"] = "0.7";
            				$scoreValuesList["0.8"] = "0.8";
            				$scoreValuesList["0.9"] = "0.9";
            				$scoreValuesList["1.0"] = "1.0";
            				
            				$scoreValues = MM_HtmlUtils::generateSelectionsList($scoreValuesList, $scoreThreshold);
            				
            				echo _mmt("Block requests when score is less than ");
        				?>
        				<select id='mm-score-threshold' name='mm_score_threshold'><?php echo $scoreValues; ?></select>
        			</p>
    				</div>
            	</div>
            	
            	<div style="margin-top:20px;">
        			<i class="fa fa-pie-chart"></i> <strong><?php echo _mmt("Action Name for Segmentation"); ?></strong>
        			<div style="margin-left:25px; width:600px; margin-top:10px;">
        			
        			<div class="updated" style="padding:10px; border-left-color:#999">reCAPTCHA v3 introduces a new concept: actions. When you specify an action name in each place you 
        			execute reCAPTCHA, you enable the following new features:
					<ul>
					<li>- A detailed break-down of data for your top ten actions in the <a href="https://g.co/recaptcha/admin" target="_blank">admin console</a></li>
					<li>- Adaptive risk analysis based on the context of the action, because abusive behavior can vary.</li>
					</ul>
					</div>
					
        			<p><?php echo _mmt("Use the following action name for all requests coming from MemberMouse checkout forms:"); ?></p>
        			<p><input id="mm-action-name" name="mm_action_name" type="text" size="50" value="<?php echo $actionName; ?>" /><br/>
        			<span style="font-size:smaller;"><i>Note: Only alphanumeric characters and slashes allowed</i></span></p>
    				</div>
            	</div>
            	
    			<div style="margin-top:20px;">
        			<i class="fa fa-exclamation-circle"></i> <strong><?php echo _mmt("Error Message"); ?></strong>
        			<div style="margin-left:25px; width:600px; margin-top:10px;">
					
        			<p><?php echo _mmt("Display the following message when reCAPTCHA check fails:"); ?></p>
        			<p><input id="mm-error-message" name="mm_error_message" type="text" size="50" value="<?php echo $errorMessage; ?>" /></p>
    				</div>
            	</div>
    			
    			<div style="margin-top:20px;">
            		<i class="fa fa-eye"></i> <strong><?php echo _mmt("reCAPTCHA Badge Visibility"); ?></strong>
            		<p style="margin-left:25px; width:600px;">
            			<input onchange="showHideCaptchaInfo()" id="mm-cb-hide-captcha-badge" name="mm_hide_captcha_badge" type="checkbox"  <?php echo (($hideCaptchaBadge==false)?"":"checked"); ?>  />
            			<?php echo _mmt("Hide the reCAPTCHA badge"); ?>
            		</p>
            	</div>
            	<div id="mm-hide-captcha-info" style="margin-left:25px; width:600px;" class="error">
            		<p><i style="color:#dc3232" class="fa fa-exclamation-triangle"></i> <strong style="color:#dc3232">ACTION REQUIRED</strong></p>
            		<p>According to Google, you are allowed to hide the badge as long as you include the reCAPTCHA branding visibly in the user flow. 
Google recommends including the following text somewhere in the user flow. See Google's FAQ for <a href="https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge.-what-is-allowed" target="_blank">more information</a>.</p>
<pre style="font-size:small">
This site is protected by reCAPTCHA and the Google
    <a href="https://policies.google.com/privacy">Privacy Policy</a> and
    <a href="https://policies.google.com/terms">Terms of Service</a> apply.</pre>
    </p>
            	</div>
    		</div>
    		
    		<div id="captcha_v2_settings" style="margin-top:20px; margin-left:15px;">
    			<p><i style="color:#dc3232" class="fa fa-exclamation-triangle"></i> <strong style="color:#dc3232">ACTION REQUIRED</strong></p>
    			<p style="margin-left:10px; width:600px;">
    			When captcha is enabled, customers will verify they're not a robot by clicking on a checkbox in the captcha field.
    			In order to include the captcha field you need to place a SmartTag on the default checkout core page and any 
    			other pages where you are using a checkout form. 
    			</p>
    			
    			<p style="margin-left:10px;">
    				<strong>Step 1:</strong> Copy the SmartTag below:<br/><br/>
        			<span style="font-family:courier; font-size:11px;">
        				<input id="mm-captcha-smarttag" type="text" readonly value="[MM_Form_Field type='input' name='captcha']" style="width:350px" onclick="jQuery('#mm-captcha-smarttag').focus(); jQuery('#mm-captcha-smarttag').select();" />
        			</span>
        		</p>
        		<?php 
    			     $checkoutCorePage = MM_CorePage::getDefaultCorePageByType(MM_CorePageType::$CHECKOUT);
    			?>
        		<p style="margin-left:10px; margin-top:20px; width:600px;">
        			<strong>Step 2:</strong> Place it somewhere between the <code>[MM_Form type='checkout']</code> and <code>[/MM_Form]</code> SmartTags on the default checkout page and all other pages that have a checkout form.
        			<br/><br/><a href="<?php echo get_edit_post_link($checkoutCorePage->page_id); ?>" target="_blank" class='mm-ui-button'>
                    	<?php echo _mmt("Edit Default Checkout Page"); ?>
                    </a>
        		</p>
        		
    		</div>
		</div>
	</div>
</div>
<script>
	showCaptchaSettings();
	toggleCaptchaVersion();
	showHideCaptchaInfo();
</script>
<input type='submit' value='<?php echo _mmt("Save Settings"); ?>' class="mm-ui-button blue" />
</form>

<script type='text/javascript'>
<?php if(!empty($error)){ ?>
alert('<?php echo $error; ?>');
<?php  } else if(isset($_POST["mm_captcha_public_key"])) { ?>
alert("<?php echo _mmt("Settings saved successfully"); ?>");
<?php } ?>
</script>
