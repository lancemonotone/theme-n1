<?php 
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
$shippingMethod = new MM_FlatRateShippingMethod();
$shippingOptionResponse = $shippingMethod->getShippingOptionById($p->id);
if ($shippingOptionResponse->type == MM_Response::$SUCCESS)
{
	$shippingOption = $shippingOptionResponse->message;
	$p->name = $shippingOption->getName();
	$p->rate = $shippingOption->getRate();
}
?>
<div id="mm-form-container">
<input type='hidden' id='mm_id' value='<?php echo $p->id; ?>' />
<table>
<tr>
	<td><?php echo _mmt("Name"); ?></td>
	<td><input type='text' id='mm-name' value='<?php echo ((isset($p->name))?htmlentities($p->name,ENT_QUOTES, "UTF-8"):''); ?>' style='width: 225px;'/></td>
</tr>
<tr>
	<td><?php echo _mmt("Rate"); ?></td>
	<td><input type='text' id='mm-rate' value='<?php echo ((isset($p->rate))?$p->rate:''); ?>' style='width: 65px;'/></td>
</tr>
</table>
</div>

<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.save();" class="mm-ui-button blue"><?php echo _mmt("Save Shipping Method"); ?></a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button"><?php echo _mmt("Cancel"); ?></a>
</div>
</div>