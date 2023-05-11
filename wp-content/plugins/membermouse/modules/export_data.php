<?php 
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

require_once(dirname(__FILE__)."/../bootstrap/barebones.php");

function redirectToErrorPage()
{
	$url = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCESS_DENIED);
	wp_redirect($url);
	exit;
}

//-------------------- Attempt to extend time limit and extend memory limit in case there is a lot of data ------------------------------//

set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('memory_limit','128M');

//-------------------- Authenticate the user ------------------------------//

global $current_user;
global $wpdb;

$userHooks = new MM_UserHooks();
if ($userHooks->checkEmployeeAccess() === false)
{
	redirectToErrorPage();
}

//make sure one of the supported pages is making the request
if (!isset($_REQUEST['module']) || ($_REQUEST['module'] != "MM_ManageTransactionsView"))
{
	redirectToErrorPage();
}


//-------------------- Setup the direct db access methods  ------------------------------//
$fetch_object_function = _mmmd()."_fetch_object";
$free_result_function = _mmmd()."_free_result";
$error_function = _mmmd()."_error";

//-------------------- Setup the filename and file type (header generation also)  ------------------------------//

//TODO: set charset to utf-8
$fileType = "application/csv";
$fileName = "export.csv";

header("Content-type: ".$fileType);
header("Content-Disposition: filename=".$fileName);
header("Pragma: no-cache");


//-------------------- Generate Data ------------------------------//
if ($_REQUEST['module'] == "MM_ManageTransactionsView")
{
	$view = new MM_ManageTransactionsView();
	$dataGrid = new MM_DataGrid($_REQUEST, "transaction_date", "desc");
	$csvHeaders = $view->getCSVHeaders(); //headers array is keyed by the db fieldname
	$handle = fopen("php://output", "w");
	fputcsv($handle, $csvHeaders);
	
	$query = $wpdb->remove_placeholder_escape($view->constructQuery($_REQUEST, $dataGrid));
	
	$result = _mmmq($query);
	
	while ($row = @$fetch_object_function($result))
	{
		$outputArray = array();
		//csv fields are type(transaction_type), date(transaction_date), order#(order_number), amount(transaction_amount), name(last_name), email(user_email), description
		
		//do some translations
		$fullName = "{$row->last_name}, {$row->first_name}";
		$transactionTypeLabel = "";
		switch ($row->transaction_type)
		{
			case MM_TransactionLog::$TRANSACTION_TYPE_PAYMENT:
				$transactionTypeLabel = "Initial Payment or One-Time Payment";
				break;
			case MM_TransactionLog::$TRANSACTION_TYPE_RECURRING_PAYMENT:
				$transactionTypeLabel = "Recurring Payment";
				break;
			case MM_TransactionLog::$TRANSACTION_TYPE_REFUND:
				$transactionTypeLabel = "Refund";
				break;
			default:
				$transactionTypeLabel = "";
				break;
		}
		
		//now populate the row
		$outputArray[] = $transactionTypeLabel;
		$outputArray[] = MM_Utils::dateToLocal($row->transaction_date);
		$outputArray[] = $row->order_number;
		$outputArray[] = $row->transaction_amount;
		$outputArray[] = $row->currency;
		$outputArray[] = $fullName;
		$outputArray[] = $row->user_email;
		$outputArray[] = $row->affiliate_id;
		$outputArray[] = $row->sub_affiliate_id;
		$outputArray[] = $row->description;
		$outputArray[] = $row->payment_service;
		
		//output to the screen (the file)
		fputcsv($handle,$outputArray);
	}
	
	if ($error_message = $error_function($wpdb->dbh))
	{
		//optionally do something here if there was an error	
	}
			
	@$free_result_function($result);
	fclose($handle);
}

//-------------------- End Processing ------------------------------//
exit;