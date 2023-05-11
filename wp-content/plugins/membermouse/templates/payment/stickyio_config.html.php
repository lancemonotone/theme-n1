<script type="text/javascript">
function stickyIoDisableButtons()
{ 
    $("#stickyio_configure_products").attr("class","mm-ui-button disabled");
    $("#stickyio_configure_products").attr("disabled","disabled");
    $("#stickyio_configure_shipping_methods").attr("class","mm-ui-button disabled");
    $("#stickyio_configure_shipping_methods").attr("disabled","disabled");
    $(".mm-stickyio-info").show();
}
function stickyIoEnableButtons()
{ 
    $("#stickyio_configure_products").attr("class","mm-ui-button");
    $("#stickyio_configure_products").removeAttr("disabled");
    $("#stickyio_configure_shipping_methods").attr("class","mm-ui-button");
    $("#stickyio_configure_shipping_methods").removeAttr("disabled");
    $(".mm-stickyio-info").hide();
}
function stickyioCallback(data)
{ 
	console.log(data);
	if(data.type!="success")
	{
 		alert(data.message);
	}
	else{
		jQuery("#stickyio_default_shipping_id").empty();
		jQuery("#stickyio_default_shipping_id").append(data.message);
		jQuery("#mm-stickyio-step2").show();
	}
}
 
function doGoToNextStep()
{ 
	values = jQuery("#mm-form-container :input").serializeArray();
	values.push({name:"payment_service[stickyio][command]", value:"save_api_info"});
	pymtSettings_js.performIntermediateAction(values,'stickyio','stickyioCallback');
}

function doSaveDefaultShippingMethod()
{
	var defaultShippingID = jQuery("#stickyio_default_shipping_id").val();
	if(defaultShippingID<=0)
	{ 
		alert("You must select a valid default shipping method.");
		return false;
	}
	return true;
}

function validateStickyioConfigButtons()
{
	var configButtonDisabled = (jQuery("#stickyio_configure_products[disabled]").length > 0);
	var shouldDisableConfigButton = false;

	jQuery(".stickyio_required_for_product_config").each(function() {
		if (jQuery(this).val() == "")
		{
			shouldDisableConfigButton = true;
		}
	});
	
	if (!configButtonDisabled && shouldDisableConfigButton)
	{
		jQuery("#stickyio_configure_products").attr("disabled","disabled");
		jQuery("#stickyio_configure_shipping_methods").attr("disabled","disabled");
	}
	else if (!shouldDisableConfigButton && configButtonDisabled)
	{
		jQuery("#stickyio_configure_products").removeAttr("disabled");
		jQuery("#stickyio_configure_shipping_methods").removeAttr("disabled");
	}
}

jQuery(function($){
	//set initial button state
	validateStickyioConfigButtons();

	//dynamically modify button state based on field contents
	$(".stickyio_required_for_product_config").keyup(validateStickyioConfigButtons);

	$("#stickyio_configure_products").click(function(e) {
		e.preventDefault();
		window.location.href = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_STICKYIO_PRODUCTS); ?>";
	});

	$("#stickyio_configure_shipping_methods").click(function(e) {
		e.preventDefault();
		window.location.href = "<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_STICKYIO_SHIPPING_METHODS); ?>";
	});
});
</script>

<div style="padding:10px;">
<img src='<?php echo MM_RESOURCES_URL;?>/images/stickyio.png' style="width: 300px" />

<div style="margin-top:5px; margin-bottom:10px;">
<a href='https://support.membermouse.com/support/solutions/articles/9000179162-configuring-limelight-v2-bundles-offers' target='_blank'>Need help configuring Sticky.io?</a>
</div>
 
<div style="margin-top:5px; margin-bottom:10px;">
<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_LOGS, MM_MODULE_STICKYIO_LOG); ?>" class='mm-ui-button blue'>
	View Sticky.io IPN Log
</a>
</div>

<div style="margin-bottom:10px;">
	Sticky.io URL
	
	<p style="margin-left:10px; font-family:courier; font-size:11px;">
				<input type='text' value='<?php echo $p->getURL(); ?>' id='stickyio_url' class='stickyio_required_for_product_config' name='payment_service[stickyio][url]' style='width: 275px;' />
	</p>
</div>

<div style="margin-bottom:10px;">
	API Username
	
	<p style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getAPIUsername(); ?>' id='stickyio_api_username' class='stickyio_required_for_product_config' name='payment_service[stickyio][api_username]' style='width: 275px;' />
	</p>
</div> 

<div style="margin-bottom:10px;">
	API Password
	
	<p style="margin-left:10px; font-family:courier; font-size:11px;">
		<input type='text' value='<?php echo $p->getAPIPassword(); ?>' id='stickyio_api_password' class='stickyio_required_for_product_config' name='payment_service[stickyio][api_password]' style='width: 275px;' />
	</p>
</div>

<p style="margin-left:10px;">
	<button type='button' id='stickyio_configure_api' onclick="doGoToNextStep();" class="mm-ui-button">Setup API & Continue</button>
</p>

<div id="mm-stickyio-step2" style="<?php if(is_null($p->getAPIPassword()) || $p->getAPIPassword()==""){ echo "display:none"; } ?>">
    <div style="margin-bottom:10px; margin-top:30px;">
    	Membership Provider Postback URL
    	
    	<p style="margin-left:10px;">
    		<?php $postbackUrl = MM_PLUGIN_URL."/x.php?service=stickyio"; ?>
    		
    		<span style="font-family:courier; font-size:11px; margin-top:5px;">
    			 <input id="mm-stickyio-postback-url" type="text" value="<?php echo $postbackUrl; ?>" style="width:550px; background-color:#fff;" onclick="jQuery('#mm-stickyio-postback-url').focus(); jQuery('#mm-stickyio-postback-url').select();" readonly />
    		</span>
    	</p>
    </div>
    
    <div style="margin-bottom:10px;">
    	Campaign Success Location (<em>Webform Integration Only</em>)
    	
    	<p style="margin-left:10px;">
    		<?php $confirmationUrl = MM_PLUGIN_URL."/x.php?service=stickyio&op=conf"; ?>
    		
    		<span style="font-family:courier; font-size:11px; margin-top:5px;">
    			 <input id="mm-stickyio-confirmation-page-url" type="text" value="<?php echo $confirmationUrl; ?>" style="width:550px; background-color:#fff;" onclick="jQuery('#mm-stickyio-confirmation-page-url').focus(); jQuery('#mm-stickyio-confirmation-page-url').select();" readonly />
    		</span>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	For each campaign in Sticky.io with a webform integration you need to specify a success location to redirect to following a successful purchase. To ensure that customers are redirected to the 
    	the appropriate confirmation page within your membership site, set the success location URL for each campaign in Sticky.io to the URL above.</p>
    </div>
    
    
    <div style="margin-bottom:10px;margin-top:30px;">
    	Default Shipping Method 
    	
    	<p style="margin-left:10px;">
    		<select name="payment_service[stickyio][default_shipping_id]"  id="stickyio_default_shipping_id" onchange="doSaveDefaultShippingMethod();">
    			<?php echo $p->getDefaultShippingMethodsSelect(); ?>
    		</select>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	Sticky.io requires a shipping ID for all purchases whether the product is shippable or not.  Since this is the case, you must define a $0.00 shipping method (in your Sticky.io portal) to successfully use Sticky.io.</p>
     
    </div>
    <div style="margin-bottom:10px; ">
    	Product Mappings  
    	<p style="margin-left:10px;">
    		<button id='stickyio_configure_products' class="mm-ui-button" >Configure Sticky.io Products</button> <span class="mm-stickyio-info">Please save payment method settings before configuring your products.</span>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	Sticky.io and MemberMouse both define products. Products in MemberMouse need to be mapped to products in Sticky.io so that when a product is
    	purchased in Sticky.io, it can be mapped to a MemberMouse product and the appropriate actions can be taken. It also makes it so that when a
    	product is purchased in MemberMouse, it can be mapped to the appropriate Sticky.io product so the appropriate amount is charged to the customer.</p>
    </div>
    
    <div style="margin-bottom:10px;">
    	 
    	Shipping Method Mappings
    	<p style="margin-left:10px;">
    		<button id='stickyio_configure_shipping_methods' class="mm-ui-button">Configure Sticky.io Shipping Methods</button> <span class="mm-stickyio-info">Please save payment method settings before configuring your shipping methods.</span>
    	</p>
    	
    	<p style="font-size:11px; margin-left:10px; padding-right:20px;">
    	<?php echo MM_Utils::getInfoIcon("", ""); ?>
    	Sticky.io and MemberMouse both define shipping methods. Shipping methods in MemberMouse need to be mapped to shipping methods in Sticky.io 
    	so that when a shippable product is purchased in Sticky.io, the shipping method can be mapped to a MemberMouse shipping method. 
    	It also makes it so that when a shippable product is purchased in MemberMouse, the shipping method can be mapped to the appropriate Sticky.io shipping method 
    	so the appropriate amount is charged to the customer for shipping.</p>
    </div>
	</div>
</div>
<script type='text/javascript'>

<?php if($p->getActive()=="0"){
?>
jQuery( document ).ready(function() {
    stickyIoDisableButtons();
});
<?php 
}else{
 ?>
 stickyIoEnableButtons();
 <?php   
}?>
jQuery( "body" ).on( "payment_methods-save", function() {
	if(jQuery("#onsite_payment_service_<?php echo strtolower($p->getToken());?>").is(":checked"))
	{
		stickyIoEnableButtons(); 
	}
	else{
		stickyIoDisableButtons(); 
	}
});
</script>