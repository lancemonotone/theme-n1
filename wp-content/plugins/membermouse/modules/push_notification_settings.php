<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_push_notification_dispatch_setting"]))
{
    $mmPushNotificationDispatchAsync = (isset($_POST['mm_push_notification_dispatch_async']) && ($_POST['mm_push_notification_dispatch_async'] == "true")); 
    MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ASYNC_ENABLE_PUSH_NOTIFICATIONS, $mmPushNotificationDispatchAsync);
    
}
else
{
    $mmPushNotificationDispatchAsync = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ASYNC_ENABLE_PUSH_NOTIFICATIONS);
}
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 
<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("Push Notification Dispatch Settings"); ?></p>
	<div style='margin-top: 10px;'>
		<input type='hidden' name='mm_push_notification_dispatch_setting' value='true' />
		<input name="mm_push_notification_dispatch_async" value='true' type="checkbox" <?php echo $mmPushNotificationDispatchAsync?"checked":""; ?>  />
		<?php echo _mmt("Enable asynchronous dispatching of push notifications"); ?>
	</div>
</div>