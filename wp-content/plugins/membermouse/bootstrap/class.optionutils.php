<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

class MM_OptionUtils
{
	public static $OPTION_KEY_LAST_PAGE_DENIED = 'mm-last-denied-page';
	public static $OPTION_KEY_DFLT_CANCELLATION_METHOD = "mm-dflt-cancellation-method";
	public static $OPTION_KEY_COUNTRY_SELECTIONS = "mm-option-country-selections";
	public static $OPTION_KEY_DFLT_COUNTRY_SELECTION = "mm-option-dflt-country-selection";
	public static $OPTION_KEY_CAPTCHA_ENABLED = "mm-option-captcha-enabled";
	public static $OPTION_KEY_CAPTCHA_KEY = "mm-option-captcha-key";
	public static $OPTION_KEY_CAPTCHA_VERSION = "mm-option-captcha-version";
	public static $OPTION_KEY_CAPTCHA_ERROR_MESSAGE = "mm-option-error-message";
	public static $OPTION_KEY_CAPTCHA_PRIVATE_KEY = "mm-option-captcha-private-key";
	public static $OPTION_KEY_HIDE_CAPTCHA_BADGE = "mm-option-hide-captcha-badge";
	public static $OPTION_KEY_CHECKOUT_PAID_MESSAGE = "mm-option-checkout-paid-message";
	public static $OPTION_KEY_CHECKOUT_FREE_MESSAGE = "mm-option-checkout-free-message";
	public static $OPTION_KEY_CHECKOUT_MESSAGE_CSS = "mm-option-checkout-message-css";
	public static $OPTION_KEY_ON_LOGIN_USE_WP_FRONTPAGE = "mm-option-homepage-setting";
	public static $OPTION_KEY_AFTER_LOGIN_USE_WP_FRONTPAGE = "mm-option-use-member-homepage";
	public static $OPTION_KEY_SHOW_LOGIN_LOGOUT_LINK = "mm-option-show-login-logout-link";
	public static $OPTION_KEY_HIDE_PROTECTED_MENU_ITEMS = "mm-option-hide-protected-menu-items";
	public static $OPTION_KEY_USE_MM_LOGIN_PAGE = "mm-option-use-mm-login-page";
	public static $OPTION_KEY_USE_MM_RESET_PASSWORD_PAGE = "mm-option-use-mm-reset-password-page";
	public static $OPTION_KEY_AFFILIATE = "mm-option-affiliate";
	public static $OPTION_KEY_AFFILIATE_ALIAS = "mm-option-affiliate-alias";
	public static $OPTION_KEY_SUB_AFFILIATE = "mm-option-sub-affiliate";
	public static $OPTION_KEY_SUB_AFFILIATE_ALIAS = "mm-option-sub-affiliate-alias";
	public static $OPTION_KEY_AFFILIATE_LIFESPAN = "mm-option-affiliate-lifespan";
	public static $OPTION_KEY_ACCT_SECURITY_ENABLED = "mm-option-acct-security";
	public static $OPTION_KEY_ACCT_SECURITY_MAX_IPS = "mm-option-acct-security-max-ips";
	public static $OPTION_KEY_ACTIVITY_LOG_CLEANUP_ENABLED = "mm-option-activity-log-cleanup-enabled";
	public static $OPTION_KEY_ACTIVITY_LOG_CLEANUP_INTERVAL = "mm-option-activity-log-cleanup-interval";
	public static $OPTION_KEY_FORGOT_PASSWORD_SUBJECT = "mm-forgot-password-email-subject";
	public static $OPTION_KEY_FORGOT_PASSWORD_BODY = "mm-forgot-password-email-body";
	public static $OPTION_KEY_LAST_CHECKIN = "mm-last-checkin";
	public static $OPTION_KEY_LAST_CODE_REFRESH = 'mm-last-code-refresh';
	public static $OPTION_KEY_LICENSE_DATA = "mm-license-data";
	public static $OPTION_KEY_MAJOR_VERSION = "mm-major-version";
	public static $OPTION_KEY_MINOR_VERSION = "mm-minor-version";
	public static $OPTION_KEY_UPGRADE_NOTICE = "mm-upgrade-notice";
	public static $OPTION_KEY_FORCE_USE_DB_CACHE = "mm-force-use-db-cache";
	public static $OPTION_KEY_USE_CHECKOUT_FORM_TEST_DATA = "mm-option-use-checkout-form-test-data";
	public static $OPTION_KEY_CHECKOUT_FORM_TEST_DATA = "mm-option-checkout-form-test-data";
	public static $OPTION_KEY_SHOW_MBRS_SEARCH = "mm-option-show-mbrs-search";
	public static $OPTION_KEY_SHOW_TRANSACTIONS_SEARCH = "mm-option-show-transactions-search";
	public static $OPTION_KEY_SHOW_TRAINING_VIDEOS = "mm-option-show-training-videos";
	public static $OPTION_KEY_LOGIN_TOKEN_LIFESPAN = "mm-option-login-token-lifespan";
	public static $OPTION_KEY_OVERDUE_PAYMENT_NOTIFICATION_INSTALLED = "mm-overdue-payment-notification-installed";
	public static $OPTION_KEY_PURCHASE_CONFIRMATION_DIALOG_WIDTH = "mm-option-purchase-confirmation-dialog-width";
	public static $OPTION_KEY_PURCHASE_CONFIRMATION_DIALOG_HEIGHT = "mm-option-purchase-confirmation-dialog-height";
	public static $OPTION_KEY_DEFAULT_CHECKOUT_ITEM_TYPE = "mm-option-dflt-checkout-item-type";
	public static $OPTION_KEY_DEFAULT_CHECKOUT_ITEM_ID = "mm-option-dflt-checkout-item-id";
	public static $OPTION_KEY_SHOW_PREVIEW_BAR = "mm-option-show-preview-bar";
	public static $OPTION_KEY_HIDE_ADMIN_BAR = "mm-option-hide-admin-bar";
	public static $OPTION_KEY_ENABLE_WP_AUTOP = "mm-option-enable-wp-autop";
	public static $OPTION_KEY_ENABLE_USERNAME_CHANGE = "mm-option-enable-username-change";
	public static $OPTION_KEY_USE_JQUERY_UI = "mm-option-use-jquery-ui";
	public static $OPTION_KEY_USE_MM_CSS_CHECKOUT = "mm-option-use-mm-css-checkout";
	public static $OPTION_KEY_USE_MM_CSS_MY_ACCOUNT = "mm-option-use-mm-css-my-account";
	public static $OPTION_KEY_USE_MM_CSS_LOGIN = "mm-option-use-mm-css-login";
	public static $OPTION_KEY_USE_MM_CSS_FORGOT_PASSWORD = "mm-option-use-mm-css-forgot-password";
	public static $OPTION_KEY_USE_MM_CSS_RESET_PASSWORD = "mm-option-use-mm-css-reset-password";
	public static $OPTION_KEY_ENABLE_MEMBERSHIP_PRORATION = "mm-option-enable-membership-proration";
	public static $OPTION_KEY_ALLOW_LOGGED_OUT_PURCHASES = "mm-option-allow-logged-out-purchases";
	public static $OPTION_KEY_ALLOW_DUPLICATE_SUBSCRIPTIONS = "mm-option-allow-duplicate-subscriptions";
	public static $OPTION_KEY_PURCHASE_LINK_STYLE = "mm-option-purchase-link-style";
	public static $OPTION_KEY_DISABLE_EXPLICIT_LINKS = "mm-option-disable-explicit-links";
	public static $OPTION_KEY_SMARTTAG_VERSION = "mm-option-smarttag-version";
	public static $OPTION_KEY_ALLOW_OVERDUE_ACCESS = "mm-option-allow-overdue-access";
	public static $OPTION_KEY_DRIP_CONTENT_TIME_SETTING = "mm-option-drip-content-time-setting";
	public static $OPTION_KEY_SITE_IN_TEST_MODE = "mm-site-in-test-mode";
	public static $OPTION_KEY_SMARTTAGS = "smarttags";

	public static $OPTION_KEY_CURRENCY = "mm-option-currency";
	public static $OPTION_KEY_CURRENCY_FORMAT_POSTFIX_ISO = "mm-option-currency-format-postfix-iso";
	
	public static $OPTION_KEY_ORIGIN_AFFILIATE_MIGRATED = "mm-option-origin-affiliate-migrated";
	public static $OPTION_KEY_DIAGNOSTIC_MODE = "mm-option-diagnostic-mode";
	public static $OPTION_KEY_SHOW_DIAGNOSTICS_LOG_FILTERS = "mm-option-show-diagnostics-log-filters";
	
	public static $OPTION_KEY_SAFE_MODE = "mm-option-safe-mode";
	public static $OPTION_KEY_SAFE_MODE_PLUGINS = "mm-option-safe-mode-plugins";
	public static $OPTION_KEY_SAFE_MODE_THEME = "mm-option-safe-mode-theme";
	public static $OPTION_KEY_SAFE_MODE_LOG = "mm-option-safe-mode-log";

	public static $OPTION_KEY_CORE_PAGE_CACHE = "mm-option-core-page-cache";
	
	public static $OPTION_KEY_FORGET_MEMBER_EMAIL_ADDRESS      = "mm-option-forget-member-email_address";
	public static $OPTION_KEY_FORGET_MEMBER_ADDRESS            = "mm-option-forget-member-address";
	public static $OPTION_KEY_FORGET_MEMBER_ADDRESS_COUNTRY    = "mm-option-forget-member-country";
	public static $OPTION_KEY_FORGET_MEMBER_ORDER_ADDRESS      = "mm-option-forget-member-order-address";
	public static $OPTION_KEY_FORGET_MEMBER_ORDER_COUNTRY      = "mm-option-forget-member-order-country";
	public static $OPTION_KEY_FORGET_MEMBER_ACTIVITY_LOG       = "mm-option-forget-member-activity-log";
	public static $OPTION_KEY_FORGET_MEMBER_CUSTOM_FIELDS      = "mm-option-forget-member-custom-fields";
	
	public static $OPTION_KEY_ASYNC_OPTIMIZE_TRANSPORT         = "mm-option-async-optimize-transport";
	public static $OPTION_KEY_ASYNC_ENABLE_PUSH_NOTIFICATIONS  = "mm-option-async-enable-push-notifications";
	
	public static $OPTION_KEY_ENABLE_LEGACY_EXPORT             = "mm-option-enable-legacy-export";
	
	public static $DEFAULT_ACCT_SECURITY_ENABLED = "1";
	public static $DEFAULT_ACCT_SECURITY_MAX_IPS = "5";
	public static $DEFAULT_ACTIVITY_LOG_CLEANUP_ENABLED = "1";
	public static $DEFAULT_ACTIVITY_LOG_CLEANUP_INTERVAL = "365";
	public static $DEFAULT_CURRENCY = "USD";
	public static $DEFAULT_SHOW_LOGIN_LOGOUT_LINK = "0";
	public static $DEFAULT_USE_MM_LOGIN_PAGE = "1";
	public static $DEFAULT_USE_MM_RESET_PASSWORD_PAGE = "1";
	public static $DEFAULT_USE_CHECKOUT_FORM_TEST_DATA = "0";
	public static $DEFAULT_HIDE_PROTECTED_MENU_ITEMS = "1";
	public static $DEFAULT_ON_LOGIN_USE_WP_FRONTPAGE = "0";
	public static $DEFAULT_AFTER_LOGIN_USE_WP_FRONTPAGE = "1";
	public static $DEFAULT_CAPTCHA_ENABLED = "0";
	public static $DEFAULT_CAPTCHA_VERSION = "3";
	public static $OPTION_KEY_CAPTCHA_LOG_CLEANUP_INTERVAL = "mm-option-captcha-log-cleanup-interval";
	public static $OPTION_KEY_CAPTCHA_SCORE_THRESHOLD = "mm-option-captcha-score-threshold";
	public static $OPTION_KEY_CAPTCHA_ACTION_NAME = "mm-option-captcha-action-name";
	public static $DEFAULT_CHECKOUT_PAID_MESSAGE = "Please wait while we process your order...";
	public static $DEFAULT_CHECKOUT_FREE_MESSAGE = "Please wait while we create your account...";
	public static $DEFAULT_CHECKOUT_MESSAGE_CSS = "mm-checkout-processing-message";
	public static $DEFAULT_AFFILIATE = "affid";
	public static $DEFAULT_SUB_AFFILIATE = "sid";
	public static $DEFAULT_AFFILIATE_LIFESPAN = "30";
	public static $DEFAULT_LOGIN_TOKEN_LIFESPAN = "15";
	public static $DEFAULT_PURCHASE_CONFIRMATION_DIALOG_WIDTH = "450";
	public static $DEFAULT_PURCHASE_CONFIRMATION_DIALOG_HEIGHT = "200";
	public static $DEFAULT_SHOW_PREVIEW_BAR = "1";
	public static $DEFAULT_HIDE_ADMIN_BAR = "1";
	public static $DEFAULT_ENABLE_WP_AUTOP = "1";
	public static $DEFAULT_ENABLE_USERNAME_CHANGE = "0";
	public static $DEFAULT_CHECKOUT_ITEM_TYPE = "membership_level";
	public static $DEFAULT_CHECKOUT_ITEM_ID = "-1";
	public static $DEFAULT_COUNTRY_SELECTIONS = array("US"=>"US");
	public static $DEFAULT_ENABLE_MEMBERSHIP_PRORATION = "1";
	public static $DEFAULT_ALLOW_LOGGED_OUT_PURCHASES = "1";
	public static $DEFAULT_ALLOW_DUPLICATE_SUBSCRIPTIONS = "0";
	public static $DEFAULT_DISABLE_EXPLICIT_LINKS = "1";
	public static $DEFAULT_PURCHASE_LINK_STYLE = MM_LINK_STYLE_REFERENCE;
	public static $DEFAULT_SMARTTAG_VERSION = "2.1";
	public static $DEFAULT_USE_JQUERY_UI = "1";
	public static $DEFAULT_USE_MM_CSS = "1";
	public static $DEFAULT_ALLOW_OVERDUE_ACCESS = "0";
	public static $DEFAULT_DRIP_CONTENT_TIME_SETTING = "local";
	public static $DEFAULT_FORGOT_PASSWORD_SUBJECT = "Reset your password";
	public static $DEFAULT_FORGOT_PASSWORD_BODY = "Hi [MM_Member_Data name='firstName'],

Click the link below to reset your account password:
	
<a href=\"[MM_CorePage_Link type='resetpassword']\">[MM_CorePage_Link type='resetpassword']</a>
	
If you have any questions, please contact us at [MM_Employee_Data name='email'].
	
Thanks,
[MM_Employee_Data name='displayName']";
	
	public static $DEFAULT_OVERDUE_PAYMENT_SUBJECT = "Your Account Is Past Due";
	public static $DEFAULT_OVERDUE_PAYMENT_BODY = "[MM_Member_Data name='firstName'],
	
Your recent payment was declined.
	
Please update your billing information to reactivate your account.
To update your credit card details, please click the link below:
			
[MM_CorePage_Link type='myaccount' autoLogin='true']
	
Thank you for your prompt attention to this matter.";
	
	
	public static function setDefaultValue($optionName, $defaultValue)
	{
		$crntValue = self::getOption($optionName);
		if($crntValue === false || $crntValue === "")
		{
			self::setOption($optionName, $defaultValue);
		}
	}
	
	public static function setOption($optionName, $value = "") 
	{
		update_option($optionName, $value);
	}
	
	public static function getOption($optionName,$defaultValue=false)
	{
		if($optionName == "siteurl")
		{
			$optionValue = site_url();
		}
		else 
		{
			$optionValue = get_option($optionName,$defaultValue);
		}
		
		if(!empty($optionValue) && is_string($optionValue))
		{
			return stripslashes($optionValue);
		}
		return $optionValue;
	}
}
?>