<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

if(isset($_POST["mm_selected_currency"]))
{
	//update default currency
	if (in_array($_POST['mm_selected_currency'],array_keys(MM_CurrencyUtil::getSupportedCurrencies())))
	{
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_CURRENCY,$_POST["mm_selected_currency"]);
	}
	
	if (isset($_POST["mm_postfix_iso_to_currency"]) && $_POST["mm_postfix_iso_to_currency"] == "1")
	{
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_CURRENCY_FORMAT_POSTFIX_ISO,true);
	}
	else
	{
		MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_CURRENCY_FORMAT_POSTFIX_ISO,false);
	}
}

$currentCurrency      = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_CURRENCY);
$postfixIsoToCurrency = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_CURRENCY_FORMAT_POSTFIX_ISO);
$postfixIsoToCurrency = (empty($postfixIsoToCurrency))?false:$postfixIsoToCurrency;

//check the support of the active payment services for the currently selected currency
$activePaymentServices = MM_PaymentServiceFactory::getAvailablePaymentServices();
$unsupportedPaymentServices = array();
foreach ($activePaymentServices as $aps)
{
	if (!$aps->isSupportedCurrency($currentCurrency))
	{
		$unsupportedPaymentServices[] = $aps->getName();
	}
}

$warningMsg = "";
$warningBox = "";
$numUnsupported = count($unsupportedPaymentServices);
if ($numUnsupported > 0)
{
	$lastService = "";
	if ($numUnsupported > 1)
	{
		$lastService = array_pop($unsupportedPaymentServices);
		$lastService = " and {$lastService}";
	}
	$warningMsg = "The currently selected currency is not supported by the ".implode(", ",$unsupportedPaymentServices)."{$lastService} payment services";
}

if (!empty($warningMsg))
{
	$warningBox = MM_Utils::getIcon('warning', 'yellow', '1.3em', '1px', '', 'margin-left:5px;')." {$warningMsg}";
}
?>
<script>
function showCurrencyFormat()
{
	jQuery("#mm-currency-format").show();
	jQuery("#mm-currency-format").dialog({autoOpen: true, width: "600", height: "350", modal: true});
}
</script>
<div class="mm-wrap">
    <p class="mm-header-text">Currency <span style="font-size:12px;"><a href="http://support.membermouse.com/support/solutions/articles/9000020239-currency-settings" target="_blank">Learn more</a></span></p>
    <div style="clear:both; height: 10px;"></div>
    <div style="margin-bottom:10px; width:550px;">
    	<?php echo _mmt("Setting the currency indicates which currency customers will pay in and also determines how all product and coupon prices are formatted across the site. If you're changing the currency and you've already defined product and coupon prices, you'll need to go through each product or coupon and update the prices to the new currency. MemberMouse does NOT perform automatic currency conversion"); ?>.
    </div>
	
	<div style="margin-top:10px;">
		<select id="mm_currency_selector" name="mm_selected_currency">
		<?php echo MM_HtmlUtils::getSupportedCurrenciesList($currentCurrency); ?>
		</select>
		<?php echo $warningBox; ?>
	</div>
	
	<div style='margin:10px 0px 5px;'>
		<label>
			<input id="mm_postfix_iso_to_currency" name="mm_postfix_iso_to_currency" value='1' type="checkbox" <?php echo ($postfixIsoToCurrency == "1") ? "checked":""; ?> />
			<?php echo _mmt("Append the currency code after the amount");?> (ie. $100.00 becomes $100.00 USD)
		</label>
	</div>
	
	<div style="margin-top:15px;">
		<?php echo _mmt("Sample format");?>: <span style="font-family:courier; font-size:14px; margin-right:5px;"><?php echo _mmf(12345.67); ?></span>
		
		<a href="#" onclick="showCurrencyFormat()" style="font-size:11px;"><?php echo _mmt("incorrect format");?>?</a>
	</div>
	<div id="mm-currency-format" style="display:none;" title="Currency Format">
		<p style="font-size:14px; line-height:20px;">
		<?php echo _mmt("We do our best to ensure that every currency uses the most accepted format. If you find that your currency isn't formatted in the most accepted way, please let us know and we'll update it");?>. 
		</p>
		
		<p style="font-size:14px; line-height:20px;">
		<?php echo sprintf(_mmt("Just %s send us an email %s and let us know the %s name of your currency %s and how your currency should be formatted by sending us a sample %s formatted number %s"),'<strong><a href="https://membermouse.com/contact-support" target="_blank">','</a></strong>','<strong>','</strong>','<strong>','</strong>');?> (i.e. $12,345.67 or &pound;12,345.67).
		</p>
		
		<p style="font-size:14px; line-height:20px;">
			<?php echo _mmt("Make sure the sample formatted number demonstrates the following");?>:
		</p>
		
		<p style="font-size:14px; line-height:20px;">
			- <?php echo _mmt("The currency symbol to use"); ?><br/>
			- <?php echo _mmt("Whether the currency symbol is at the beginning or the end"); ?><br/>
			- <?php echo _mmt("The punctuation mark to use for the thousands separator"); ?><br/>
			- <?php echo _mmt("The punctuation mark to use for the decimal point"); ?><br/>
		</p>
	</div>
</div>