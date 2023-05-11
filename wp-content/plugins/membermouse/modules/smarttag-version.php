<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_smarttag_version"]))
{
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SMARTTAG_VERSION, $_POST["mm_smarttag_version"]);
}

$smartTagVersion = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SMARTTAG_VERSION);
?>

<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 

<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("SmartTag Version"); ?> <span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020473-set-the-smarttag-version" target="_blank"><?php echo _mmt("Learn more"); ?></a></span></p>
	
	<div style="margin-top:10px;">
		<input name="mm_smarttag_version" value='2.1' type="radio" <?php echo (($smartTagVersion=="2.1")?"checked":""); ?>  />
		<?php echo _mmt("SmartTags"); ?> 2.1 (<em><?php echo _mmt("recommended"); ?></em>)
		
		<input name="mm_smarttag_version" value='2.0' type="radio" <?php echo (($smartTagVersion!="2.1")?"checked":""); ?> style="margin-left:10px;" />
		<?php echo _mmt("SmartTags"); ?> 2.0
	</div>
</div>