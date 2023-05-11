<?php 
	$order = MM_Order::create($p->orderId);
	
	if($order->isValid())
	{
		$orderItem = MM_OrderItem::create($p->orderItemId);
		
		if($orderItem->isValid())
		{
?>
<div id='mm-edit-transaction-div'>
<input type='hidden' id='order_id' value='<?php echo $p->orderId ?>' />
<input type='hidden' id='order_item_id' value='<?php echo $p->orderItemId ?>' />
<input type='hidden' id='transaction_id' value='<?php echo $p->transactionId ?>' />
<div style="font-size:11px;">
<?php 
if($orderItem->isRecurring())
{
	echo MM_Utils::getInfoIcon();
?>
	<em>This transaction is associated with a subscription. Any changes made here will be applied to this transaction and
	future recurring payments.</em> 
<?php 
}
?>
<table>
<tr>
	<td>Affiliate ID:</td>
	<td><input id="mm-affiliate-id" type="text" style="width: 152px" value="<?php echo $order->getAffiliateId(); ?>" /> </td>
</tr>
<tr>
	<td>Sub-Affiliate ID:</td>
	<td><input id="mm-sub-affiliate-id" type="text" style="width: 152px" value="<?php echo $order->getSubAffiliateId(); ?>" /> </td>
</tr>
</table>
</div>
</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.saveTransaction();" class="mm-ui-button blue">Save</a>
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
	Invalid order item ID
<?php } ?>

<?php } else { ?>
	Invalid order ID
<?php } ?>