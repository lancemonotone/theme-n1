<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

function isLocalInstall($specificServer="localhost")
{
	if(isset($_SERVER["SERVER_NAME"]) && strlen($_SERVER["SERVER_NAME"])>0)
	{
		if(preg_match("/(".$specificServer.")/", $_SERVER["SERVER_NAME"]))
		{
			return true;
		}
	}

	return false;
}

define("MM_PREFIX", "mm_");   
define("MM_LANGUAGE_DOMAIN", "membermouse"); 

$centralServer = "https://hub.membermouse.com/index.php?q=/";
$centralServerUrl = "https://hub.membermouse.com";
$centralServerPrettyUrl = "https://hub.membermouse.com";

$reservedGetParams = array(
	's'=>1,
	'p'=>1,
	'page_id'=>1,
	'name'=>1,
);

define("MM_CENTRAL_SERVER_URL", $centralServerUrl);
define("MM_PRETTY_CENTRAL_SERVER_URL", $centralServerPrettyUrl);
define("MM_CENTRAL_SERVER", $centralServer);
define("MM_PLUGIN_ABSPATH", dirname(dirname(__FILE__)));

$pluginDirArray = explode(DIRECTORY_SEPARATOR, dirname(dirname(__FILE__)));
define("MM_PLUGIN_NAME", array_pop($pluginDirArray));
define("MM_LIB_DIR", MM_PLUGIN_ABSPATH.DIRECTORY_SEPARATOR."lib");

define("MM_RESOURCES_URL", plugins_url(MM_PLUGIN_NAME."/resources/"));
define("MM_IMAGES_URL", MM_RESOURCES_URL."images/");

define("MM_NO_DATA", "&mdash;");
define("MM_GET_KEY", "345346539284890489234");

/** 
 * jQuery UI theme MemberMouse uses. Might want to think about 
 * making this an option at some point in the future 
 **/
define("MM_JQUERY_UI_THEME", "smoothness");

/** Member Mouse Session Lifespan (In seconds) **/
define("MM_SESSION_LIFESPAN", 86400); // 24 hours

/** Set this to true if the current version of the plugin is a beta version **/
define("MM_IS_BETA", false);

define("MM_TYPE_MEMBERSHIP_LEVEL", "member_type");
define("MM_TYPE_BUNDLE", "access_tag");
define("MM_TYPE_POST", "post");
define("MM_TYPE_PRODUCT", "product");
define("MM_TYPE_CUSTOM_FIELD", "custom_field");
define("MM_TYPE_EMPLOYEE_ACCOUNT", "employee_account");

/** PURCHASE LINK STYLES **/
define("MM_LINK_STYLE_EXPLICIT", "explicit-links");
define("MM_LINK_STYLE_REFERENCE", "reference-links");

/** DATABASE MYSQL DRIVERS **/
define("MM_MYSQL_DRIVER", "mysql");
define("MM_MYSQLI_DRIVER", "mysqli");

/** DATABASE TABLE NAMES **/
define("MM_TABLE_QUEUE", MM_PREFIX."queue");
define("MM_TABLE_SESSIONS", MM_PREFIX."sessions");
define("MM_TABLE_COUPONS", MM_PREFIX."coupons");
define("MM_TABLE_USER_DEFINED_PAGES", MM_PREFIX."user_defined_pages");
define("MM_TABLE_COUPON_RESTRICTIONS", MM_PREFIX."coupon_restrictions");
define("MM_TABLE_COUPON_USAGE", MM_PREFIX."coupon_usage");
define("MM_TABLE_BUNDLES", MM_PREFIX."bundles");
define("MM_TABLE_APPLIED_BUNDLES", MM_PREFIX."applied_bundles");
define("MM_TABLE_BUNDLE_PRODUCTS", MM_PREFIX."bundle_products");
define("MM_TABLE_BUNDLE_CATEGORIES", MM_PREFIX."bundle_categories");
define("MM_TABLE_MEMBERSHIP_LEVELS", MM_PREFIX."membership_levels");
define("MM_TABLE_MEMBERSHIP_LEVEL_PRODUCTS", MM_PREFIX."membership_level_products");
define("MM_TABLE_MEMBERSHIP_LEVEL_CATEGORIES", MM_PREFIX."membership_level_categories");
define("MM_TABLE_COMMISSION_PROFILES", MM_PREFIX."commission_profiles");
define("MM_TABLE_RETENTION_REPORTS", MM_PREFIX."retention_reports");
define("MM_TABLE_CORE_PAGES", MM_PREFIX."core_pages");
define("MM_TABLE_ORDERS", MM_PREFIX."orders");
define("MM_TABLE_CORE_PAGE_TYPES", MM_PREFIX."core_page_types");
define("MM_TABLE_POSTS_ACCESS", MM_PREFIX."posts_access");
define("MM_TABLE_SMARTTAGS", MM_PREFIX."smarttags");
define("MM_TABLE_SMARTTAG_GROUPS", MM_PREFIX."smarttag_groups");
define("MM_TABLE_PRODUCTS", MM_PREFIX."products");
define("MM_TABLE_EMPLOYEE_ACCOUNTS", MM_PREFIX."employee_accounts");
define("MM_TABLE_CONTAINER", MM_PREFIX."container");
define("MM_TABLE_EVENT_LOG", MM_PREFIX."log_events");
define("MM_TABLE_CAPTCHA_LOG", MM_PREFIX."log_captcha");
define("MM_TABLE_API_KEYS", MM_PREFIX."api_keys");
define("MM_TABLE_ACTIONS", MM_PREFIX."actions");
define("MM_TABLE_CUSTOM_FIELDS", MM_PREFIX."custom_fields");
define("MM_TABLE_CUSTOM_FIELD_OPTIONS", MM_PREFIX."custom_field_options");
define("MM_TABLE_CUSTOM_FIELD_DATA", MM_PREFIX."custom_field_data");
define("MM_TABLE_VERSION_RELEASES", MM_PREFIX."version_releases");
define("MM_TABLE_EMAIL_SERVICE_PROVIDERS", MM_PREFIX."email_service_providers");
define("MM_TABLE_EMAIL_PROVIDER_MAPPINGS", MM_PREFIX."email_provider_mappings");
define("MM_TABLE_EMAIL_PROVIDER_BUNDLE_MAPPINGS", MM_PREFIX."email_provider_bundle_mappings");
define("MM_TABLE_AFFILIATE_PROVIDERS", MM_PREFIX."affiliate_providers");
define("MM_TABLE_AFFILIATE_PROVIDER_MAPPINGS", MM_PREFIX."affiliate_provider_mappings");
define("MM_TABLE_AFFILIATE_REBILL_COMMISSIONS", MM_PREFIX."affiliate_rebill_commissions");
define("MM_TABLE_AFFILIATE_PARTNER_PAYOUTS", MM_PREFIX."affiliate_partner_payouts");
define("MM_TABLE_TRANSACTION_KEY", MM_PREFIX."transaction_key");
define("MM_TABLE_LOGIN_TOKEN", MM_PREFIX."login_token");
define("MM_TABLE_PAYMENT_SERVICES", MM_PREFIX."payment_services");
define("MM_TABLE_COUNTRIES", MM_PREFIX."countries");
define("MM_TABLE_COUNTRY_SUBDIVISIONS", MM_PREFIX."country_subdivisions");
define("MM_TABLE_ORDER_ITEMS", MM_PREFIX."order_items");
define("MM_TABLE_ORDER_ITEM_ACCESS", MM_PREFIX."order_item_access");
define("MM_TABLE_ORDER_COUPONS", MM_PREFIX."order_coupons");
define("MM_TABLE_SHIPPING_METHODS", MM_PREFIX."shipping_methods");
define("MM_TABLE_USER_DATA", MM_PREFIX."user_data");
define("MM_TABLE_SOCIAL_LOGIN_PROVIDERS", MM_PREFIX."social_login_providers");
define("MM_TABLE_SOCIAL_LOGIN_LINKED_PROFILES", MM_PREFIX."social_login_linked_profiles");

/** Scheduler **/
define("MM_TABLE_SCHEDULED_EVENTS", MM_PREFIX."scheduled_events");
define("MM_TABLE_SCHEDULED_PAYMENTS", MM_PREFIX."scheduled_payments");
define("MM_TABLE_QUEUED_SCHEDULED_EVENTS", MM_PREFIX."queued_scheduled_events");
define("MM_SCHEDULING_SERVER_URL", "{$centralServerUrl}/scheduler.php");

/** PAYMENT SERVICE TABLES **/
define("MM_TABLE_CARD_ON_FILE", MM_PREFIX."card_on_file");
define("MM_TABLE_TRANSACTION_LOG", MM_PREFIX."transaction_log");
define("MM_TABLE_AUTHNET_AIM_TRANSACTIONS", MM_PREFIX."authorizenet_aim_transactions");
define("MM_TABLE_AUTHNET_ARB_SUBSCRIPTIONS", MM_PREFIX."authorizenet_arb_subscriptions");
define("MM_TABLE_AUTHNET_ARB_SUBSCRIPTION_HISTORY", MM_PREFIX."authorizenet_arb_subscription_history");
define("MM_TABLE_AUTHNET_PENDING_OVERDUE_SUBSCRIPTIONS", MM_PREFIX."authorizenet_pending_overdue_subscriptions");
define("MM_TABLE_CHARGIFY_PRODUCT_LINKS", MM_PREFIX."chargify_product_links");
define("MM_TABLE_CHARGIFY_CUSTOMER_LINKS", MM_PREFIX."chargify_customer_links");
define("MM_TABLE_CHARGIFY_COUPON_LINKS", MM_PREFIX."chargify_coupon_links");
define("MM_TABLE_CHARGIFY_SUBSCRIPTION_LINKS", MM_PREFIX."chargify_subscription_links");
define("MM_TABLE_CHARGIFY_NOTIFICATIONS", MM_PREFIX."chargify_notifications");
define("MM_TABLE_PAYPAL_IPN_LOG", MM_PREFIX."paypal_ipn_log");
define("MM_TABLE_PAYPAL_SUBSCR_LINKS", MM_PREFIX."paypal_subscr_links");
define("MM_TABLE_COINBASE_IPN_LOG", MM_PREFIX."coinbase_ipn_log");
define("MM_TABLE_COINBASE_SUBSCR_LINKS", MM_PREFIX."coinbase_subscr_links");
define("MM_TABLE_COINBASE_BUTTON_LINKS", MM_PREFIX."coinbase_button_links");
define("MM_TABLE_COINBASEMINIMAL_IPN_LOG", MM_PREFIX."coinbaseminimal_ipn_log");
define("MM_TABLE_COINBASEMINIMAL_BUTTON_LINKS", MM_PREFIX."coinbaseminimal_button_links");
define("MM_TABLE_COINBASEMINIMAL_SCHEDULED_EVENTS", MM_PREFIX."coinbaseminimal_scheduled_events");
define("MM_TABLE_COINBASEMINIMAL_TRANSACTIONS", MM_PREFIX."coinbaseminimal_transactions");
define("MM_TABLE_STRIPE_CUSTOMER_LINKS", MM_PREFIX."stripe_customer_links");
define("MM_TABLE_STRIPE_CHARGES", MM_PREFIX."stripe_charges");
define("MM_TABLE_STRIPE_MEMBERSHIPS", MM_PREFIX."stripe_memberships");
define("MM_TABLE_STRIPE_SUBSCRIPTION_PAYMENTS", MM_PREFIX."stripe_subscription_payments");
define("MM_TABLE_STRIPE_WEBHOOKS", MM_PREFIX."stripe_webhooks");
define("MM_TABLE_STRIPE_PRODUCTS", MM_PREFIX."stripe_products");
define("MM_TABLE_STRIPE_COUPONS", MM_PREFIX."stripe_coupons");
define("MM_TABLE_TWOCHECKOUT_CHARGES", MM_PREFIX."twocheckout_charges");
define("MM_TABLE_TWOCHECKOUT_WEBHOOKS", MM_PREFIX."twocheckout_webhooks");
define("MM_TABLE_CLICKBANK_PRODUCT_LINKS", MM_PREFIX."clickbank_product_links");
define("MM_TABLE_CLICKBANK_IPN_LOG", MM_PREFIX."clickbank_ipn_log");
define("MM_TABLE_CLICKBANK_ORDER_ITEM_LINKS", MM_PREFIX."clickbank_order_item_links");
define("MM_TABLE_BRAINTREE_CUSTOMER_LINKS", MM_PREFIX."braintree_customer_links");
define("MM_TABLE_BRAINTREE_CHARGES", MM_PREFIX."braintree_charges");
define("MM_TABLE_AUTHNET_CIM_CUSTOMER_LINKS", MM_PREFIX."authorizenet_cim_customer_links");
define("MM_TABLE_AUTHNET_CIM_CHARGES", MM_PREFIX."authorizenet_cim_charges");
define("MM_TABLE_LIMELIGHT_PRODUCTS", MM_PREFIX."limelight_products");  
define("MM_TABLE_LIMELIGHT_SHIPPING_METHODS", MM_PREFIX."limelight_shipping_methods");
define("MM_TABLE_LIMELIGHT_SUBSCRIPTION_LINKS", MM_PREFIX."limelight_subscription_links");
define("MM_TABLE_LIMELIGHT_CHARGES", MM_PREFIX."limelight_charges");
define("MM_TABLE_LIMELIGHT_IPN_LOG", MM_PREFIX."limelight_ipn_log");

define("MM_TABLE_STICKYIO_PRODUCTS", MM_PREFIX."stickyio_products");
define("MM_TABLE_STICKYIO_SHIPPING_METHODS", MM_PREFIX."stickyio_shipping_methods");
define("MM_TABLE_STICKYIO_SUBSCRIPTION_LINKS", MM_PREFIX."stickyio_subscription_links");
define("MM_TABLE_STICKYIO_CHARGES", MM_PREFIX."stickyio_charges");
define("MM_TABLE_STICKYIO_IPN_LOG", MM_PREFIX."stickyio_ipn_log");


define("MM_TABLE_LITLE_CUSTOMER_LINKS", MM_PREFIX."litle_customer_links");
define("MM_TABLE_LITLE_CHARGES", MM_PREFIX."litle_charges");
define("MM_TABLE_TEST_CARDONFILE", MM_PREFIX."test_cardonfile");


/** SHIPPING METHOD TABLES **/
define("MM_TABLE_FLATRATE_SHIPPING_OPTIONS", MM_PREFIX."flatrate_shipping_options");

/** Reporting Data Tables **/
define("MM_TABLE_REPORT_DATA_CACHE", MM_PREFIX."report_data_cache");

define("MM_TABLE_DIAGNOSTIC_LOG",MM_PREFIX."diagnostic_log");

/** MODULE NAMES **/
define("MM_MODULE_DASHBOARD", "mmdashboard");
define("MM_MODULE_VERSION_HISTORY", "version_history");
define("MM_MODULE_REPAIR_MEMBERMOUSE", "repair_membermouse");
define("MM_MODULE_REPAIR_INSTALL", "repair_install");
define("MM_MODULE_DIAGNOSTICS", "diagnostics");
define("MM_MODULE_LICENSE", "license");
define("MM_MODULE_PRODUCT_SETTINGS", "product_settings");
define("MM_MODULE_CHECKOUT_SETTINGS", "checkout_settings");
define("MM_MODULE_MEMBERSHIP_LEVELS", "membership_levels");
define("MM_MODULE_BUNDLES", "bundles");
define("MM_MODULE_AFFILIATE_SETTINGS", "affiliate_settings");
define("MM_MODULE_AFFILIATE_INTEGRATION", "affiliate_integration");
define("MM_MODULE_COMMISSION_PROFILES", "commission_profiles");
define("MM_MODULE_AFFILIATE_TRACKING", "affiliate_tracking");
define("MM_MODULE_DEVELOPER_TOOLS", "developer_tools");
define("MM_MODULE_CANCELLATION_METHOD", "cancellation_method");
define("MM_MODULE_WEBFORMS", "webforms");
define("MM_MODULE_API", "api");
define("MM_MODULE_PUSH_NOTIFICATIONS", "push_notifications");
define("MM_MODULE_WORDPRESS_HOOKS", "wordpress_hooks");
define("MM_MODULE_CUSTOM_FIELDS", "custom_field");
define("MM_MODULE_EMPLOYEES", "employees");
define("MM_MODULE_EMAIL_INTEGRATION", "email_integration");
define("MM_MODULE_PRODUCTS", "products");
define("MM_MODULE_PURCHASE_LINKS", "purchaselinks");
define("MM_MODULE_SHIPPING", "shipping");
define("MM_MODULE_CAPTCHA", "captcha");
define("MM_MODULE_COUNTRIES", "countries");
define("MM_MODULE_CHECKOUT_OTHER_SETTINGS", "checkout_other_settings");
define("MM_MODULE_PAYMENT_METHODS", "payment_methods");
define("MM_MODULE_TEST_DATA", "test_data");
define("MM_MODULE_DRIP_CONTENT_SCHEDULE", "drip_content_schedule");
define("MM_MODULE_META", "page_meta");
define("MM_MODULE_MANAGE_MEMBERS", "manage_members");
define("MM_MODULE_BROWSE_MEMBERS", "members");
define("MM_MODULE_IMPORT_WIZARD", "import_wizard");
define("MM_MODULE_USER_DEFINED_PAGES", "user_defined_pages");
define("MM_MODULE_MEMBER_DETAILS", "member_details");
define("MM_MODULE_MEMBER_DETAILS_GENERAL", "details_general");
define("MM_MODULE_MEMBER_DETAILS_CUSTOM_FIELDS", "details_custom_fields");
define("MM_MODULE_MEMBER_DETAILS_ACCESS_RIGHTS", "details_access_rights");
define("MM_MODULE_MEMBER_DETAILS_USER_DEFINED", "details_user_defined");
define("MM_MODULE_MEMBER_DETAILS_TRANSACTION_HISTORY", "details_transaction_history");
define("MM_MODULE_MEMBER_DETAILS_SUBSCRIPTIONS", "details_subscriptions");
define("MM_MODULE_MEMBER_DETAILS_GIFT_HISTORY", "details_gift_history");
define("MM_MODULE_MEMBER_DETAILS_ACTIVITY_LOG", "details_activity_log");
define("MM_MODULE_MANAGE_TRANSACTIONS", "manage_transactions");
define("MM_MODULE_GENERAL_SETTINGS", "general_settings");
define("MM_MODULE_OTHER_SETTINGS", "other_settings");
define("MM_MODULE_SAFE_MODE", "safe_mode");
define("MM_MODULE_REPAIR_CORE_PAGES", "repair_core_pages");
define("MM_MODULE_SUPPORT", "support");
define("MM_MODULE_SMARTTAG_LIBRARY","smarttag.library");
define("MM_MODULE_SMARTTAG_LOOKUP", "smarttag.idlookup");
define("MM_EMAIL_TEMPLATES", "email_templates");
define("MM_MODULE_COUPONS", "coupons");
define("MM_MODULE_GIFTS", "gifts");
define("MM_MODULE_COUPON_LOG", "coupon_log");
define("MM_MODULE_PAYMENT_SETTINGS", "payment_settings");
define("MM_MODULE_EMAIL_SETTINGS", "email_settings");
define("MM_MODULE_CAPTCHA_IPN_LOG", "captcha_ipn_log");
define("MM_MODULE_ACTIVITY_LOG", "activity_log");
define("MM_MODULE_PAYPAL_IPN_LOG", "paypal_ipn_log");
define("MM_MODULE_CLICKBANK_IPN_LOG", "clickbank_ipn_log");
define("MM_MODULE_LIMELIGHT_LOG", "limelight_log");
define("MM_MODULE_STICKYIO_LOG", "stickyio_log");
define("MM_MODULE_LOGS", "logs");
define("MM_MODULE_FREE_MEMBER_FORM", "free_member_webform");
define("MM_MODULE_LOGIN_FORM", "login_webform");
define("MM_MODULE_PHP_INTERFACE", "php_interface");
define("MM_MODULE_DUPLICATE_SUBSCRIPTION_TOOL", "duplicate_subscriptions");
define("MM_MODULE_EXTENSIONS", "extensions");

define("MM_MODULE_STICKYIO_PRODUCTS", "stickyio_products");  
define("MM_MODULE_STICKYIO_SHIPPING_METHODS", "stickyio_shipping_methods");

define("MM_MODULE_LIMELIGHT_PRODUCTS", "limelight_products");
define("MM_MODULE_LIMELIGHTV2_PRODUCTS", "limelightv2_products");
define("MM_MODULE_LIMELIGHT_SHIPPING_METHODS", "limelight_shipping_methods");
define("MM_MODULE_LIMELIGHTV2_SHIPPING_METHODS", "limelightv2_shipping_methods"); 

//reporting
define("MM_MODULE_REPORTING", "reporting");
define("MM_MODULE_GET_REPORTING", "getreporting");
define("MM_MODULE_NEW_MEMBERS_REPORT", "new_members_report");
define("MM_MODULE_NEW_MEMBERS_COMPARISON_REPORT", "new_members_comparison_report");
define("MM_MODULE_TOTAL_VALUE_REPORT", "total_value_report");
define("MM_MODULE_CUSTOMER_VALUE_REPORT", "customer_value_report");
define("MM_MODULE_SIMPLE_AVERAGE_REPORT", "simple_average_report");
define("MM_MODULE_SALES_BY_PRODUCT_REPORT", "sales_by_product_report");
define("MM_MODULE_SALES_BY_PAYMENT_SERVICE_REPORT", "sales_by_payment_service_report");
define("MM_MODULE_SALES_BY_MEMBERSHIP_REPORT", "sales_by_membership_report");
define("MM_MODULE_AFFILIATE_REPORT", "affiliate_report");

define("MM_EXPORT_FILE_MEMBERS_IMPORT_TEMPLATE", "import_members_template");
?>