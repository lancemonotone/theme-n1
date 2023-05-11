<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */ 

/**
 * Stands for MemberMouse Translate - wraps __() functionality for translating text within the plugin.
 *
 * @param string $str The string of text to be translated.
 * @param string $domain The text domain for the translation.
 *
 * @return string value of the translated text or null if function not defined.
 */
function _mmt($str, $domain=MM_LANGUAGE_DOMAIN)
{
	if (function_exists("__"))
 	{
 		return __($str, $domain);
 	} 
 	return null;	
}


/**
 * Stands for MemberMouse Format - formats a currency value into a locale appropriate string
 * i.e. An amount of 100.00 and a currencyCode of USD will yield "$100.00"
 * 
 * @param float $amount The currency value to format
 * @param string $currencyCode The iso code of the currency
 * 
 * @return string The formatted value
 */
function _mmf($amount, $currencyCode="")
{
	$currencyCode = empty($currencyCode)?MM_CurrencyUtil::getActiveCurrency():$currencyCode;
	return MM_CurrencyUtil::format("%n", $amount, $currencyCode);
}


/**
 * Stands for MemberMouse International Format - formats a currency value into an internationally appropriate string
 * i.e.. An amount of 100.00 and a currencyCode of USD will yield "100.00 USD"
 * 
 * @param float $amount The currency value to format
 * @param string $currencyCode The iso code of the currency
 * 
 * @return The formatted string value
 */
function _mmif($amount, $currencyCode="")
{
	$currencyCode = empty($currencyCode)?MM_CurrencyUtil::getActiveCurrency():$currencyCode;
	return MM_CurrencyUtil::format("%i", $amount, $currencyCode);
}


/**
 * Stands for MemberMouse Format Currency - returns the 3-character ISO code for the active currency
 *
 * @return the 3-character iso code for the active currency
 */
function _mmfc()
{
	return MM_CurrencyUtil::getActiveCurrency ();
}


/**
 * Stands for MemberMouse Override Format [of Currency] - formats a currency value into a locale appropriate string, using any
 * supplied settings to override the defaults
 *
 * @param float $amount The currency value to format
 * @param string $currencyCode (optional) The iso code of the currency
 * @param array $currencySettings (optional) The settings to use when overriding the defaults
 *        	
 * @return string The formatted value
 *        
 */
function _mmof($amount, $currencyCode="", $currencySettings="")
{
	$currencyCode = empty($currencyCode)?MM_CurrencyUtil::getActiveCurrency():$currencyCode;
	return MM_CurrencyUtil::format("%n", $amount, $currencyCode,$currencySettings);
}


/**
 * Stands for MemberMouse MySQL Driver - detects the MySQL driver being used, which is determined by WordPress' wpdb class
 *
 * @return string representing the MySQL driver being used
 *        
 */
function _mmmd()
{
	global $wpdb;
	
	switch (true)
	{
		case (is_object ( $wpdb->dbh ) && get_class ( $wpdb->dbh ) == "mysqli") :
			return MM_MYSQLI_DRIVER;
			break;
		case (is_resource ( $wpdb->dbh ) && get_resource_type ( $wpdb->dbh ) == "mysql link") :
		default :
			return MM_MYSQL_DRIVER;
			break;
	}
}


/**
 * Stands for MemberMouse MySQL Query - wraps mysql query into one simple access point that's flexible enough to handle
 * either the mysql or mysqli driver
 *
 * @param string $sql The SQL Query to be executed
 * @param string $use_mysqli_use_result
 *        	(optional - only applicable when system is utilizing mysqli driver) when true,
 *        	system will use MYSQLI_USE_RESULT result set method, else will use
 *        	default of MYSQLI_STORE_RESULT
 *        	
 * @return when MySQL driver is mysql, returns resource link to result set, when driver is mysqli, returns mysqli result set object
 *        
 */
function _mmmq($sql, $use_mysqli_use_result = false)
{
	global $wpdb;
	
	$result = null;
	
	switch (_mmmd())
	{
		case MM_MYSQL_DRIVER :
			$result = @mysql_query($sql,$wpdb->dbh);
			break;
		case MM_MYSQLI_DRIVER :
			$result = @mysqli_query ($wpdb->dbh, $sql, (($use_mysqli_use_result) ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT));
			break;
	}
	return $result;
}

?>