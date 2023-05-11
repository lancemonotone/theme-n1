<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

global $current_user;

$user = MM_User::getCurrentWPUser();

$fieldInfo = array();

$field = new stdClass();
$field->fieldId = "mm_field_shipping_address";
$field->fieldName = "shippingaddress";
$field->label = "Address";
$fieldInfo[] = $field;

$field = new stdClass();
$field->fieldId = "mm_field_shipping_city";
$field->fieldName = "shippingcity";
$field->label = "City";
$fieldInfo[] = $field;

$field = new stdClass();
$field->fieldId = "mm_field_shipping_state";
$field->fieldName = "shippingstate";
$field->label = "State";
$fieldInfo[] = $field;

$field = new stdClass();
$field->fieldId = "mm_field_shipping_zip";
$field->fieldName = "shippingzipcode";
$field->label = "Zip Code";
$fieldInfo[] = $field;

$field = new stdClass();
$field->fieldId = "mm_field_shipping_country";
$field->fieldName = "shippingcountry";
$field->label = "Country";
$fieldInfo[] = $field;

$data = array();
	
$data["mm_field_shipping_address"] = $user->getShippingAddress();
$data["mm_field_shipping_city"] = $user->getShippingCity();
$data["mm_field_shipping_state"] = $user->getShippingState();
$data["mm_field_shipping_zip"] = $user->getShippingZipCode();
$data["mm_field_shipping_country"] = $user->getShippingCountry();

$form = new MM_CheckoutForm();

if(!isset($data["mm_field_shipping_country"]))
{
	$data["mm_field_shipping_country"] = "";
}
$form->shippingCountryList = MM_HtmlUtils::getCountryList($data["mm_field_shipping_country"]);

echo $form->getJavascriptIncludes(false);

?>
<div id="mm-form-container">
	<table>
		<?php foreach($fieldInfo as $field): ?>
			<tr>
				<td><label for="<?php echo (($field->fieldId=="mm_field_shipping_state")?$field->fieldId."_dd":$field->fieldId); ?>" class="mm-myaccount-dialog-label"><?php echo $field->label; ?></label></td>
				<td>
				<span style="font-family:courier; font-size:11px; margin-top:5px;">
					<?php 
						if(!isset($data[$field->fieldId]))
						{
							$data[$field->fieldId] = "";
						}
						echo MM_CheckoutForm::generateInputFormField($field->fieldName, $data, $form, "mm-myaccount-form-field");
					?>
				</span>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>

<div class="mm-dialog-footer-container">
    <div class="mm-dialog-button-container">
        <button onclick="javascript:myaccount_js.updateMemberData(<?php echo $user->getId(); ?>, 'shipping-info');" class="mm-ui-button blue">Update</button>
        <button onclick="javascript:myaccount_js.closeDialog();" class="mm-ui-button blue">Cancel</button>
    </div>
</div>
<?php echo $form->getInitJavascript($data); ?>