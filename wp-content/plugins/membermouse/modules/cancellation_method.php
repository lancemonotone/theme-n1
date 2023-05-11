<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
if(isset($_POST["cancel_method"]))
{
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_DFLT_CANCELLATION_METHOD, $_POST["cancel_method"]);
}

$cancelChecked = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_DFLT_CANCELLATION_METHOD);
?>

<form method='post'>
<div class="mm-wrap" style='width: 600px;'> 
	
<p><?php echo _mmt("MemberMouse supports two different cancellation methods: a hard cancel and pause. With a hard cancel, the member won't be able to log in at all. With a pause, the member will be able to log in and access all the protected content they had access to up until the time their account was paused. With the paused status, their drip content schedule won't progress so they won't get access to any additional content unless their account is reactivated."); ?></p>

<p><?php echo _mmt("There are 3 ways a member's account can be canceled"); ?>:</p>

<ol>
<li><?php echo _mmt("By the member themselves through the <code>[MM_Member_Link]</code> SmartTag"); ?></li>
<li><?php echo sprintf(_mmt("By an administrator through the Member Details &gt; Manage Access Rights page by clicking %sCancel Membership%s or %sPause Membership%s"),"<em>","</em>","<em>","</em>"); ?></li>
<li><?php echo _mmt("By MemberMouse, when responding to an event that occurs in your payment service (i.e. stop recurring, void or refund)"); ?></li>
</ol>

<p><?php echo _mmt("It's the third case that you need to tell MemberMouse which method to use."); ?></p>

<p><?php echo _mmt("Which cancellation method do you want to use?"); ?><p>

<p><input type='radio' value='<?php echo MM_CancellationMethod::$CANCEL_HARD; ?>' id='cancel_method' name='cancel_method' <?php echo (($cancelChecked!="pause")?"checked":""); ?> /> <?php echo _mmt("Hard Cancel"); ?></p>

<p><input type='radio' value='<?php echo MM_CancellationMethod::$CANCEL_PAUSE; ?>' id='cancel_method' name='cancel_method' <?php echo (($cancelChecked=="pause")?"checked":""); ?>  /> <?php echo _mmt("Pause"); ?></p>

<input type='submit' value='<?php echo _mmt("Save Settings"); ?>' class="mm-ui-button blue" />
</div>
</form>

<?php if(isset($_POST["cancel_method"])) { ?>
<script>alert("<?php echo _mmt("Settings saved successfully"); ?>");</script>
<?php } ?>