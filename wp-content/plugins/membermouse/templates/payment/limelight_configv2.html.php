<script type="text/javascript">
function limelightv2Callback(data)
{ 
	console.log(data);
	if(data.type!="success")
	{
 		alert(data.message);
	}
	else{
		jQuery("#limelightv2_default_shipping_id").empty();
		jQuery("#limelightv2_default_shipping_id").append(data.message);
		jQuery("#mm-limelightv2-step2").show();
	}
}
 
function doGoToNextStep()
{
	values = jQuery("#mm-form-container :input").serializeArray();
	values.push({name:"payment_service[limelightv2][command]", value:"save_api_info"});
	pymtSettings_js.performIntermediateAction(values,'limelightv2','limelightv2Callback');
}

function doSaveDefaultShippingMethod()
{
	var defaultShippingID = jQuery("#limelightv2_default_shipping_id").val();
	if(defaultShippingID<=0)
	{ 
		alert("You must select a valid default shipping method.");
		return false;
	}
	return true;
}

function validateLimeLightConfigButtons()
{
	var configButtonDisabled = (jQuery("#limelightv2_configure_products[disabled]").length > 0);
	var shouldDisableConfigButton = false;

	jQuery(".limelightv2_required_for_product_config").each(function() {
		if (jQuery(this).val() == "")
		{
			shouldDisableConfigButton = true;
		}
	});
	
	if (!configButtonDisabled && shouldDisableConfigButton)
	{
		jQuery("#limelightv2_configure_products").attr("disabled","disabled");
		jQuery("#limelightv2_configure_shipping_methods").attr("disabled","disabled");
	}
	else if (!shouldDisableConfigButton && configButtonDisabled)
	{
		jQuery("#limelightv2_configure_products").removeAttr("disabled");
		jQuery("#limelightv2_configure_shipping_methods").removeAttr("disabled");
	}
}

jQuery(function($){
	//set initial button state
	validateLimeLightConfigButtons();

	//dynamically modify button state based on field contents
	$(".limelightv2_required_for_product_config").keyup(validateLimeLightConfigButtons);

	$("#limelightv2_configure_products").click(function(e) {
		e.preventDefault();
		window.location.href = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_LIMELIGHTV2_PRODUCTS); ?>";
	});

	$("#limelightv2_configure_shipping_methods").click(function(e) {
		e.preventDefault();
		window.location.href = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_LIMELIGHTV2_SHIPPING_METHODS); ?>";
	});
});
</script>

<div style="padding:10px;">
<img src='https://membermouse.com/assets/plugin_images/logos/limelight.png' />

<div style="margin-top:5px; margin-bottom:10px;">
<a href='https://support.membermouse.com/support/solutions/articles/9000179162-configuring-limelight-v2-bundles-offers' target='_blank'>Need help configuring Lime Light?</a>
</div>
 
<div style="margin-top:5px; margin-bottom:10px;">
<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_LOGS, MM_MODULE_LIMELIGHT_LOG); ?>" class='mm-ui-button blue'>
	View Lime Light IPN Log
</a>
</div>

<div style="margin-bottom:10px;">
	Lime Light URL
	
	<p style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getURL(); ?>' id='limelightv2_url' class='limelightv2_required_for_product_config' name='payment_service[limelightv2][url]' style='width: 275px;' />
	</p>
</div>

<div style="margin-bottom:10px;">
	API Username
	
	<p style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getAPIUsername(); ?>' id='limelightv2_api_username' class='limelightv2_required_for_product_config' name='payment_service[limelightv2][api_username]' style='width: 275px;' />
	</p>
</div> 

<div style="margin-bottom:10px;">
	API Password
	
	<p style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getAPIPassword(); ?>' id='limelightv2_api_password' class='limelightv2_required_for_product_config' name='payment_service[limelightv2][api_password]' style='width: 275px;' />
	</p>
</div>

<p style="margin-left:10px;">
	<button type='button' id='limelightv2_configure_api' onclick="doGoToNextStep();" class="mm-ui-button">Setup API & Continue</button>
</p>

<div id="mm-limelightv2-step2" style="<?php if(is_null($p->getAPIPassword()) || $p->getAPIPassword()==""){ echo "display:none"; } ?>">
    <div style="margin-bottom:10px; margin-top:30px;">
    	Membership Provider Postback URL
    	
    	<p style="margin-left:10px;">
    		<?php $postbackUrl = MM_PLUGIN_URL."/x.php?service=limelight"; ?>
    		
    		<span style="font-family:courier; font-size:11px; margin-top:5px;">
    			 <input id="mm-limelight-postback-url" type="text" value="<?php echo $postbackUrl; ?>" style="width:550px; background-color:#fff;" onclick="jQuery('#mm-limelight-postback-url').focus(); jQuery('#mm-limelight-postback-url').select();" readonly />
    		</span>
    	</p>
    </div>
    
    <div style="margin-bottom:10px;">
    	Campaign Success Location (<em>Webform Integration Only</em>)
    	
    	<p style="margin-left:10px;">
    		<?php $confirmationUrl = MM_PLUGIN_URL."/x.php?service=limelight&op=conf"; ?>
    		
    		<span style="font-family:courier; font-size:11px; margin-top:5px;">
    			 <input id="mm-limelight-confirmation-page-url" type="text" value="<?php echo $confirmationUrl; ?>" style="width:550px; background-color:#fff;" onclick="jQuery('#mm-limelight-confirmation-page-url').focus(); jQuery('#mm-limelight-confirmation-page-url').select();" readonly />
    		</span>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	For each campaign in Lime Light with a webform integration you need to specify a success location to redirect to following a successful purchase. To ensure that customers are redirected to the 
    	the appropriate confirmation page within your membership site, set the success location URL for each campaign in Lime Light to the URL above.</p>
    </div>
    
    <div style="margin-bottom:10px; margin-top:30px;">
    	Product Mappings
    	
    	<p style="margin-left:10px;">
    		<button id='limelightv2_configure_products' class="mm-ui-button">Configure Lime Light Products</button>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	Lime Light and MemberMouse both define products. Products in MemberMouse need to be mapped to products in Lime Light so that when a product is
    	purchased in Lime Light, it can be mapped to a MemberMouse product and the appropriate actions can be taken. It also makes it so that when a
    	product is purchased in MemberMouse, it can be mapped to the appropriate Lime Light product so the appropriate amount is charged to the customer.</p>
    </div>
    
    <div style="margin-bottom:10px;">
    	Default Shipping Method 
    	
    	<p style="margin-left:10px;">
    		<select name="payment_service[limelightv2][default_shipping_id]"  id="limelightv2_default_shipping_id" onchange="doSaveDefaultShippingMethod();">
    			<?php echo $p->getDefaultShippingMethodsSelect(); ?>
    		</select>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	Lime Light requires a shipping ID for all purchases whether the product is shippable or not.  Since this is the case, you must define a $0.00 shipping method (in your Lime Light portal) to successfully use Lime Light.</p>
    	
    	Shipping Method Mappings
    	<p style="margin-left:10px;">
    		<button id='limelightv2_configure_shipping_methods' class="mm-ui-button">Configure Lime Light Shipping Methods</button>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	Lime Light and MemberMouse both define shipping methods. Shipping methods in MemberMouse need to be mapped to shipping methods in Lime Light 
    	so that when a shippable product is purchased in Lime Light, the shipping method can be mapped to a MemberMouse shipping method. 
    	It also makes it so that when a shippable product is purchased in MemberMouse, the shipping method can be mapped to the appropriate Lime Light shipping method 
    	so the appropriate amount is charged to the customer for shipping.</p>
    </div>
	</div>
</div>
