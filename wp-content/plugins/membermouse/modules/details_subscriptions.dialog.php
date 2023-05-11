<?php  
$orderItem = new MM_OrderItem($p->order_item_id);
	
	if($orderItem->isValid())
	{
		$scheduledPaymentEvent = MM_ScheduledPaymentEvent::findNextScheduledEventByOrderItemId($orderItem->getId(),false);
		$doNextRebillDisplay = false;
		$nextRebillDate = null;
		if($scheduledPaymentEvent instanceof MM_ScheduledPaymentEvent)
		{
			if ($scheduledPaymentEvent->isValid())
			{
				$doNextRebillDisplay = true;
				$nextRebillDate = $scheduledPaymentEvent->getScheduledDate();
			}
		}
		else if($scheduledPaymentEvent !== false)
		{
			$nextRebillDate = $scheduledPaymentEvent;
			$doNextRebillDisplay = true;
		}
		
		if ($doNextRebillDisplay)
		{
			$crntRebillDate = MM_Utils::dateToLocal($nextRebillDate, "m/d/Y");
?>
<div id='mm-edit-subscription-div'>
<input type='hidden' id='order_item_id' value='<?php echo $p->order_item_id ?>' />
<a href="#"></a>
<p>Next Rebill Date</p>
<p style="font-size:11px;">
	<input id="mm-next-rebill-date" type="text" style="width: 152px" value="<?php echo $crntRebillDate; ?>" /> 
	<a onClick="jQuery('#mm-next-rebill-date').focus();"><?php echo MM_Utils::getCalendarIcon(); ?></a>
</p>

</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.saveSubscription();" class="mm-ui-button blue">Save</a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button">Cancel</a>
</div>
</div>
<script type='text/javascript'>
jQuery(document).ready(function()
{	
	jQuery("#mm-next-rebill-date").datepicker({
		dateFormat: "mm/dd/yy"
	});
});
</script>
<?php } else { ?>
	Scheduled payment event is invalid
<?php } ?>
<?php } else { ?>
	Invalid order item ID
<?php } ?>