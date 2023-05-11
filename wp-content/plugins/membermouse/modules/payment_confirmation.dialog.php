<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
?>
<style>
    #mm-dialog-container { height:100%; width:100%; border-collapse:collapse; }
    .mm-dialog-button-bar { width:100%; height:40px; text-align:right; }
</style>

<table id="mm-dialog-container">
<tr><td valign="top">
<div>
<?php 
	$product = new MM_Product($p->productId);
	
	if($product->isValid())
	{
		$context = new MM_Context();
		$context->setProduct($product);
		echo MM_SmartTagUtil::processContent($product->getPurchaseConfirmationMessage(), $context);
	}
	else
	{
		echo sprintf(_mmt("Invalid product ID %s"),$p->productId);
	}
?>
</div>
</td></tr>

<tr><td valign="bottom" class="mm-dialog-button-bar">
	<a href="javascript:pymtutils_js.placeOrderCardOnFile(<?php echo $p->userId; ?>, <?php echo $p->productId; ?>, 'user', '<?php echo $p->isGift; ?>');" class="mm-button orange"><?php echo _mmt("Confirm"); ?></a>
	<a href="<?php echo MM_CorePageEngine::getCheckoutPageStaticLink($p->productId); ?>" class="mm-button"><?php echo _mmt("Use Different Card"); ?></a>
	<a href="javascript:pymtutils_js.closeDialog(mm_pymtdialog);" class="mm-button"><?php echo _mmt("Cancel"); ?></a>
</td></tr>
</table>