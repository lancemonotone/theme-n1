<?php 
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

MM_MemberMouseService::validateLicense(new MM_License());

$crntPage = MM_ModuleUtils::getPage();
$primaryTab = MM_ModuleUtils::getPrimaryTab();
$module = MM_ModuleUtils::getModule();

if(isset($_REQUEST[MM_Session::$PARAM_USER_ID]))
{
	$user = new MM_User($_REQUEST[MM_Session::$PARAM_USER_ID]);
}
else
{
	$user = new MM_User();
}

$resourceUrl = MM_RESOURCES_URL;

if(MM_Utils::isSSL())
{
	$resourceUrl = preg_replace("/(http\:)/", "https:", MM_RESOURCES_URL);
}

if(version_compare(get_bloginfo('version'), "3.8", ">="))
{
?>
<!-- override WordPress 3.8 styles -->
<style>
#wpwrap
{
	background-color: #fff;
}
.ui-widget 
{
	font-size:1em;
}
textarea, input, select
{
	font-size:11px;
}
</style>
<?php } ?>

<?php if ($primaryTab == MM_MODULE_MEMBER_DETAILS) { ?>
<div class="mm-navbar" style="margin-bottom:10px;">	
<ul>
	<li> 
		<?php if($module == MM_MODULE_USER_DEFINED_PAGES && $user->isValid()) { ?>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_GENERAL); ?>&user_id=<?php echo $user->getId(); ?>">
			<i class="fa fa-chevron-left"></i> 
			<?php echo _mmt("Back to member details");?>
		</a>
		<?php } else { ?>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_BROWSE_MEMBERS); ?>">
			<i class="fa fa-chevron-left"></i> 
		</a>
		<?php } ?>
	</li>
	
	<?php if($module != MM_MODULE_USER_DEFINED_PAGES && $user->isValid()) { ?>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_GENERAL); ?>&user_id=<?php echo $user->getId(); ?>" class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_GENERAL ? "active":""); ?>'>
			<i class="fa fa-user"></i>
			<?php echo _mmt("General");?>
		</a>
	</li>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_ACCESS_RIGHTS); ?>&user_id=<?php echo $user->getId(); ?>" class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_ACCESS_RIGHTS ? "active":""); ?>'>
			<i class="fa fa-key"></i>
			<?php echo _mmt("Access Rights");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_SUBSCRIPTIONS); ?>&user_id=<?php echo $user->getId(); ?>" class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_SUBSCRIPTIONS ? "active":""); ?>'>
			<i class="fa fa-refresh"></i>
			<?php echo _mmt("Subscriptions");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_TRANSACTION_HISTORY); ?>&user_id=<?php echo $user->getId(); ?>" class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_TRANSACTION_HISTORY ? "active":""); ?>'>
			<i class="fa fa-credit-card"></i>
			<?php echo _mmt("Transactions");?>
		</a>
	</li>
	<?php if($user->hasGifts()) { ?>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_GIFT_HISTORY); ?>&user_id=<?php echo $user->getId(); ?>" class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_GIFT_HISTORY ? "active":""); ?>'>
			<i class="fa fa-gift"></i>
			<?php echo _mmt("Gifts");?>
		</a>
	</li>
	<?php } ?>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_ACTIVITY_LOG); ?>&user_id=<?php echo $user->getId(); ?>" class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_ACTIVITY_LOG ? "active":""); ?>'>
			<i class="fa fa-list"></i>
			<?php echo _mmt("Activity Log");?>
		</a>
	</li>
	<?php if(MM_CustomField::hasCustomFields()) { ?>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_CUSTOM_FIELDS); ?>&user_id=<?php echo $user->getId(); ?>" class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_CUSTOM_FIELDS ? "active":""); ?>'>
			<i class="fa fa-edit"></i>
			<?php echo _mmt("Custom Fields");?>
		</a>
	</li>
	<?php } ?>
	
	<!-- custom menu items -->
	<li class="dropdown">
		<a class='<?php echo ($module == MM_MODULE_MEMBER_DETAILS_USER_DEFINED ? "active":""); ?>'>
			<i class="fa fa-plus-square"></i>
		</a>
		
		<ul>
			<li><a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_USER_DEFINED_PAGES); ?>&user_id=<?php echo $user->getId(); ?>"><i class="fa fa-cog"></i> <em><?php echo _mmt("Manage User-Defined Pages");?></em></a></li>
			
			<?php 
				$udPages = MM_UserDefinedPage::getPageList();
				
				foreach($udPages as $udPage) {
			?>
			<li><a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBER_DETAILS_USER_DEFINED); ?>&user_id=<?php echo $user->getId(); ?>&page_id=<?php echo $udPage->getId(); ?>"><i class="fa fa-file"></i>  <?php echo $udPage->getName(); ?></a></li>
			<?php } ?>
		</ul>
	</li>
	<?php } ?>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>

<?php if($crntPage == MM_MODULE_PRODUCT_SETTINGS) { ?>
<div class="mm-navbar">	
<ul>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_PRODUCTS); ?>" class='<?php echo ($module == MM_MODULE_PRODUCTS ? "active":""); ?>'>
			<i class="fa fa-shopping-cart"></i> 
			<?php echo _mmt("Products");?>
		</a>
	</li>
	<?php  
		$activePymtProvider = MM_PaymentServiceFactory::getOnsitePaymentService();
		$activePymtProvider = MM_PaymentServiceFactory::getOnsitePaymentService();
		if(!is_null($activePymtProvider) && $activePymtProvider->isActive() && 
		        $activePymtProvider->getToken() == MM_PaymentService::$STICKYIO_SERVICE_TOKEN ) {
		            $productsCONST =  MM_MODULE_STICKYIO_PRODUCTS;
		            $shippingCONST =  MM_MODULE_STICKYIO_SHIPPING_METHODS;
		            ?>
		        <li class="dropdown">
		<a href=""><i class="fa fa-exchange"></i> <?php echo _mmt("Sticky.io Mappings");?></a>
		<ul>
			<li> 
				<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, $productsCONST); ?>" class='<?php echo ($module == $productsCONST ? "active":""); ?>'>
					<i class="fa fa-shopping-cart"></i> 
					<?php echo _mmt("Product Mappings");?>
				</a>
			</li>
			<li> 
				<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, $shippingCONST); ?>" class='<?php echo ($module == $shippingCONST ? "active":""); ?>'>
					<i class="fa fa-truck"></i>
					<?php echo _mmt("Shipping Method Mappings");?>
				</a>
			</li>
		</ul>
	</li>    
		            
		            
		            
		<?php }else if(!is_null($activePymtProvider) && $activePymtProvider->isActive() && 
		    ($activePymtProvider->getToken() == MM_PaymentService::$LIMELIGHT_SERVICE_TOKEN  )) {
              $productsCONST =  MM_MODULE_LIMELIGHT_PRODUCTS;
              $shippingCONST =  MM_MODULE_LIMELIGHT_SHIPPING_METHODS;
	?>
	<li class="dropdown">
		<a href=""><i class="fa fa-exchange"></i> <?php echo _mmt("Lime Light Mappings");?></a>
		<ul>
			<li> 
				<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, $productsCONST); ?>" class='<?php echo ($module == $productsCONST ? "active":""); ?>'>
					<i class="fa fa-shopping-cart"></i> 
					<?php echo _mmt("Product Mappings");?>
				</a>
			</li>
			<li> 
				<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, $shippingCONST); ?>" class='<?php echo ($module == $shippingCONST ? "active":""); ?>'>
					<i class="fa fa-truck"></i>
					<?php echo _mmt("Shipping Method Mappings");?>
				</a>
			</li>
		</ul>
	</li>
	<?php } ?>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_MEMBERSHIP_LEVELS); ?>" class='<?php echo ($module == MM_MODULE_MEMBERSHIP_LEVELS ? "active":""); ?>'>
			<i class="fa fa-users"></i>
			<?php echo _mmt("Membership Levels");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_BUNDLES); ?>" class='<?php echo ($module == MM_MODULE_BUNDLES ? "active":""); ?>'>
			<i class="fa fa-cubes"></i>
			<?php echo _mmt("Bundles");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_COUPONS); ?>" class='<?php echo ($module == MM_MODULE_COUPONS ? "active":""); ?>'>
			<i class="fa fa-ticket" style="font-size:1.1em"></i>
			<?php echo _mmt("Coupons");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DRIP_CONTENT_SCHEDULE); ?>" class='<?php echo ($module == MM_MODULE_DRIP_CONTENT_SCHEDULE ? "active":""); ?>'>
			<i class="fa fa-calendar"></i>
			<?php echo _mmt("Drip Content Schedule");?>
		</a>
	</li>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>


<?php if($crntPage == MM_MODULE_CHECKOUT_SETTINGS) { ?>
<div class="mm-navbar">	
<ul>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_CUSTOM_FIELDS); ?>" class='<?php echo ($module == MM_MODULE_CUSTOM_FIELDS ? "active":""); ?>'>
			<i class="fa fa-edit"></i>
			<?php echo _mmt("Custom Fields");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_COUNTRIES); ?>" class='<?php echo ($module == MM_MODULE_COUNTRIES ? "active":""); ?>'>
			<i class="fa fa-globe"></i>
			<?php echo _mmt("Countries");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_SHIPPING); ?>" class='<?php echo ($module == MM_MODULE_SHIPPING ? "active":""); ?>'>
			<i class="fa fa-truck"></i>
			<?php echo _mmt("Shipping Methods");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_CAPTCHA); ?>" class='<?php echo ($module == MM_MODULE_CAPTCHA ? "active":""); ?>'>
			<i class="fa fa-shield"></i>
			<?php echo _mmt("reCAPTCHA");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_CHECKOUT_OTHER_SETTINGS); ?>" class='<?php echo ($module == MM_MODULE_CHECKOUT_OTHER_SETTINGS ? "active":""); ?>'>
			<i class="fa fa-cogs"></i>
			<?php echo _mmt("Other Settings");?>
		</a>
	</li>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>


<?php if($crntPage == MM_MODULE_PAYMENT_SETTINGS) { ?>
<div class="mm-navbar">	
<ul>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_PAYMENT_METHODS); ?>" class='<?php echo ($module == MM_MODULE_PAYMENT_METHODS ? "active":""); ?>'>
			<i class="fa fa-credit-card"></i> 
			<?php echo _mmt("Payment Methods");?>
		</a>
	</li>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_TEST_DATA); ?>" class='<?php echo ($module == MM_MODULE_TEST_DATA ? "active":""); ?>'>
			<i class="fa fa-flask"></i>
			<?php echo _mmt("Test Data");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_CANCELLATION_METHOD); ?>" class='<?php echo ($module == MM_MODULE_CANCELLATION_METHOD ? "active":""); ?>'>
			<i class="fa fa-ban"></i>
			<?php echo _mmt("Cancellation Method");?>
		</a>
	</li>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>


<?php if($crntPage == MM_MODULE_EMAIL_SETTINGS) { ?>
<div class="mm-navbar">	
<ul>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_EMAIL_INTEGRATION); ?>" class='<?php echo ($module == MM_MODULE_EMAIL_INTEGRATION ? "active":""); ?>'>
			<i class="fa fa-envelope"></i>
			<?php echo _mmt("Email Integration");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_EMAIL_TEMPLATES); ?>" class='<?php echo ($module == MM_EMAIL_TEMPLATES ? "active":""); ?>'>
			<i class="fa fa-file-text-o"></i>
			<?php echo _mmt("Email Templates");?>
		</a>
	</li>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>


<?php if($crntPage == MM_MODULE_AFFILIATE_SETTINGS) { ?>
<div class="mm-navbar">	
<ul>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_AFFILIATE_INTEGRATION); ?>" class='<?php echo ($module == MM_MODULE_AFFILIATE_INTEGRATION ? "active":""); ?>'>
			<i class="fa fa-bullhorn"></i> 
			<?php echo _mmt("Affiliate Integration");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_COMMISSION_PROFILES); ?>" class='<?php echo ($module == MM_MODULE_COMMISSION_PROFILES ? "active":""); ?>'>
			<i class="fa fa-money"></i>
			<?php echo _mmt("Commission Profiles");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_AFFILIATE_TRACKING); ?>" class='<?php echo ($module == MM_MODULE_AFFILIATE_TRACKING ? "active":""); ?>'>
			<i class="fa fa-cogs"></i>
			<?php echo _mmt("Tracking Settings");?>
		</a>
	</li>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>


<?php if($crntPage == MM_MODULE_DEVELOPER_TOOLS) { ?>
<div class="mm-navbar">	
<ul>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_PUSH_NOTIFICATIONS); ?>" class='<?php echo ($module == MM_MODULE_PUSH_NOTIFICATIONS ? "active":""); ?>'>
			<i class="fa fa-send"></i>
			<?php echo _mmt("Push Notifications");?>
		</a>
	</li>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_API); ?>" class='<?php echo ($module == MM_MODULE_API ? "active":""); ?>'>
			<i class="fa fa-key"></i> 
			<?php echo _mmt("API Credentials");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_WORDPRESS_HOOKS); ?>" class='<?php echo ($module == MM_MODULE_WORDPRESS_HOOKS ? "active":""); ?>'>
			<i class="fa fa-wordpress"></i>
			<?php echo _mmt("WordPress Hooks/Filters");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_PHP_INTERFACE); ?>" class='<?php echo ($module == MM_MODULE_PHP_INTERFACE ? "active":""); ?>'>
			<i class="fa fa-code"></i>
			<?php echo _mmt("PHP Interface");?>
		</a>
	</li>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>


<?php if($crntPage == MM_MODULE_LOGS) { ?>
<div class="mm-navbar">	
<ul>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_ACTIVITY_LOG); ?>" class='<?php echo ($module == MM_MODULE_ACTIVITY_LOG ? "active":""); ?>'>
			<i class="fa fa-list"></i>
			<?php echo _mmt("Activity Log");?>
		</a>
	</li>
	
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_COUPON_LOG); ?>" class='<?php echo ($module == MM_MODULE_COUPON_LOG ? "active":""); ?>'>
			<i class="fa fa-ticket"></i>
			<?php echo _mmt("Coupon Log");?>
		</a>
	</li>
	
	<?php if(MM_GiftView::hasGifts()) { ?>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_GIFTS); ?>" class='<?php echo ($module == MM_MODULE_GIFTS ? "active":""); ?>'>
			<i class="fa fa-gift"></i>
			<?php echo _mmt("Gifts Log");?>
		</a>
	</li>
	<?php } ?>
	
	<?php 
	   if(MM_CaptchaUtils::isCaptchaEnabled() && MM_CaptchaUtils::getCaptchaVersion() == MM_CaptchaUtils::$CAPTCHA_VERSION_3)
		{
	?>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_CAPTCHA_IPN_LOG); ?>" class='<?php echo ($module == MM_MODULE_CAPTCHA_IPN_LOG ? "active":""); ?>'>
			<i class="fa fa-shield"></i>
			<?php echo _mmt("reCAPTCHA Log");?>
		</a>
	</li>
	<?php } ?>
	
	<?php 
		$paypalService = MM_PaymentServiceFactory::getPaymentService(MM_PaymentService::$PAYPAL_SERVICE_TOKEN);
		
		if($paypalService->isActive())
		{
	?>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_PAYPAL_IPN_LOG); ?>" class='<?php echo ($module == MM_MODULE_PAYPAL_IPN_LOG ? "active":""); ?>'>
			<i class="fa fa-paypal"></i>
			<?php echo _mmt("PayPal IPN Log");?>
		</a>
	</li>
	<?php } ?>
	<?php
		$clickbankService = MM_PaymentServiceFactory::getPaymentService(MM_PaymentService::$CLICKBANK_SERVICE_TOKEN);

		if($clickbankService->isActive())
		{
	?>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_CLICKBANK_IPN_LOG); ?>" class='<?php echo ($module == MM_MODULE_CLICKBANK_IPN_LOG ? "active":""); ?>'>
			<i class="fa fa-list"></i>
			<?php echo _mmt("ClickBank IPN Log");?>
		</a>
	</li>
	<?php } ?>
	
	<?php 
	$limelightService = MM_PaymentServiceFactory::getPaymentService(MM_PaymentService::$LIMELIGHT_SERVICE_TOKEN);
	$stickyIO = MM_PaymentServiceFactory::getPaymentService(MM_PaymentService::$STICKYIO_SERVICE_TOKEN);
		
		if($limelightService->isActive())
		{
	?>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_LIMELIGHT_LOG); ?>" class='<?php echo ($module == MM_MODULE_LIMELIGHT_LOG ? "active":""); ?>'>
			<i class="fa fa-list"></i>
			<?php echo _mmt("Lime Light IPN Log");?>
		</a>
	</li>
	<?php }else if($stickyIO->isActive()){
	?>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_STICKYIO_LOG); ?>" class='<?php echo ($module == MM_MODULE_LIMELIGHT_LOG ? "active":""); ?>'>
			<i class="fa fa-list"></i>
			<?php echo _mmt("Sticky.io IPN Log");?>
		</a>
	</li>
	<?php } ?>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>	


<?php if($crntPage == MM_MODULE_WEBFORMS) { ?>
<div class="mm-navbar">	
<ul>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_FREE_MEMBER_FORM); ?>" class='<?php echo ($module == MM_MODULE_FREE_MEMBER_FORM ? "active":""); ?>'>
			<i class="fa fa-list-alt"></i>
			<?php echo _mmt("Free Member Webform");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_LOGIN_FORM); ?>" class='<?php echo ($module == MM_MODULE_LOGIN_FORM ? "active":""); ?>'>
			<i class="fa fa-list-alt"></i>
			<?php echo _mmt("Login Webform");?>
		</a>
	</li>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>	


<?php  if($crntPage == MM_MODULE_GENERAL_SETTINGS) {  ?>
<div class="mm-navbar">	
<ul>
	<?php if(empty($_GET[MM_Session::$PARAM_SUBMODULE])) { ?>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_EMPLOYEES); ?>" class='<?php echo ($module == MM_MODULE_EMPLOYEES ? "active":""); ?>'>
			<i class="fa fa-users"></i>
			<?php echo _mmt("Employees");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_EXTENSIONS); ?>" class='<?php echo ($module == MM_MODULE_EXTENSIONS ? "active":""); ?>'>
			<i class="fa fa-puzzle-piece"></i>
			<?php echo _mmt("Extensions");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_SAFE_MODE); ?>" class='<?php echo ($module == MM_MODULE_SAFE_MODE ? "active":""); ?>'>
			<i class="fa fa-life-saver"></i>
			<?php echo _mmt("Safe Mode");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_OTHER_SETTINGS); ?>" class='<?php echo ($module == MM_MODULE_OTHER_SETTINGS ? "active":""); ?>'>
			<i class="fa fa-cogs"></i>
			<?php echo _mmt("Other Settings");?>
		</a>
	</li>
	<li>
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DUPLICATE_SUBSCRIPTION_TOOL); ?>" class='<?php echo ($module == MM_MODULE_DUPLICATE_SUBSCRIPTION_TOOL ? "active":""); ?>'>
			<i class="fa fa-copy"></i>
			<?php echo _mmt("Duplicate Subscriptions");?>
		</a>
	</li>
	<li class="dropdown">
		<a href=""><i class="fa fa-tasks"></i> <?php echo _mmt("Manage Install");?></a>
		<ul>
			<li><a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_VERSION_HISTORY); ?>"><i class="fa fa-history"></i> <?php echo _mmt("Version History");?></a></li>
			<li><a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_REPAIR_INSTALL); ?>"><i class="fa fa-wrench"></i> <?php echo _mmt("Repair Install");?></a></li>
			<li><a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_REPAIR_CORE_PAGES); ?>"><i class="fa fa-wrench"></i> <?php echo _mmt("Repair Core Pages");?></a></li>
			<?php if (isset($_GET['enable_diagnostic']) && ($_GET['enable_diagnostic'] == "true")) { ?>
			<li><a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_DIAGNOSTICS); ?>"><i class="fa fa-wrench"></i> <?php echo _mmt("Diagnostic Log");?></a></li>
			<?php } ?>
			<?php if (isLocalInstall()) { ?>
			<li><a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_REPAIR_MEMBERMOUSE); ?>"><i class="fa fa-wrench"></i> <?php echo _mmt("Repair MemberMouse (dev)");?></a></li>
			<?php } ?>
		</ul>
	</li>
	<?php } else { ?>
	<li> 
		<a href="<?php echo MM_ModuleUtils::getUrl($crntPage, MM_MODULE_EXTENSIONS); ?>">
			<i class="fa fa-chevron-left"></i>
		</a>
	</li>
	<?php } ?>
	
	<?php echo MM_SupportUtils::supportMenuItem($module); ?>
</ul>
</div>
<?php } ?>	

<?php if($crntPage == MM_MODULE_ACCOUNT_LOGIN) { 
	$account_email = MM_OptionUtils::getOption( MM_OptionUtils::$OPTION_KEY_AUTH_ACCOUNT_EMAIL );
	$secret = MM_OptionUtils::getOption( MM_OptionUtils::$OPTION_KEY_AUTH_ACCOUNT_SECRET );
	$site_uuid = MM_OptionUtils::getOption( MM_OptionUtils::$OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );
?>
<div class="mm-navbar">	
	<div class="mm-account-login">
	  <h3><?php esc_html_e('MemberMouse.com Account Login', 'membermouse'); ?></h3>

	  <?php if ( $site_uuid && $account_email && $secret ) : ?>

	    <div class="mm-account-connected">

	      <h3><?php esc_html_e( 'Connected to MemberMouse.com', 'membermouse' ); ?></h3>

	      <table class="form-table">
	        <tr>
	          <th><?php esc_html_e( 'Account Email', 'membermouse' ); ?></th>
	          <td><?php echo esc_html($account_email); ?></td>
	        </tr>
	        <tr>
	          <th><?php esc_html_e( 'Site ID', 'membermouse' ); ?></th>
	          <td><?php echo esc_html($site_uuid); ?></td>
	        </tr>
	      </table>

	      <?php

	        $disconnect_url = add_query_arg(array(
	          'mm-disconnect' => 'true',
	          'nonce' => wp_create_nonce( 'mm-disconnect' )
	        ));

	      ?>

	      <p>
	        <a href="<?php echo esc_url($disconnect_url); ?>" class="button-primary mm-confirm" data-message="<?php esc_attr_e('Are you sure? This action will disconnect any of your Stripe payment methods, block webhooks from being processed, and prevent you from charging Credit Cards with and being notified of automatic rebills from Stripe.', 'membermouse'); ?>"><?php esc_html_e( 'Disconnect from MemberMouse.com', 'membermouse' ); ?></a>
	      </p>

	    </div>

	  <?php else : ?>

	    <div class="mm-account-not-connected">

	      <p class="description"><?php esc_html_e( 'Connect your site to MemberMouse.com to enable MemberMouse Cloud Services!', 'membermouse' ); ?></p>

	      <p>
	        <a href="<?php echo esc_url(MM_AuthenticatorService::get_auth_connect_url()); ?>" class="button-primary"><?php esc_html_e( 'Connect to MemberMouse.com', 'membermouse' ); ?></a>
	      </p>

	    </div>

	  <?php endif; ?>

	  <?php do_action('mm_account_login_page'); ?>

	</div>
</div>
<?php } ?>	

<div style="clear: both"></div>

<div id="mm-view-container">
	<?php 
		if ($module == MM_MODULE_REPORTING)
		{
			echo MM_TEMPLATE::generate(MM_MODULES."/reports/{$crntPage}.php");
		}
		else 
		{
			echo MM_TEMPLATE::generate(MM_MODULES."/{$module}.php"); 
		}
	?>
</div>
	
<?php 
	if (($module != MM_MODULE_REPORTING) &&  file_exists(MM_MODULES."/{$module}.firstrun.php")) 
	{
		echo MM_TEMPLATE::generate(MM_MODULES."/{$module}.firstrun.php"); 
	}
?>