<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
 
// get data based on search criteria and datagrid settings
$view = new MM_ManageTransactionsView();
$dataGrid = new MM_DataGrid($_REQUEST, "transaction_date", "desc");
$data = $view->search($_REQUEST, $dataGrid);

$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "transaction";

$headers = $view->getGridHeaders();

$datagridRows = array();

// process data
foreach($data as $key=>$item)
{
	$fullName = "{$item->last_name}, {$item->first_name}";
	$transactionTypeIcons = "";
	
	switch ($item->transaction_type)
	{
		case MM_TransactionLog::$TRANSACTION_TYPE_PAYMENT:
			$transactionTypeIcons .= MM_Utils::getIcon('money', 'green', '1.4em', '2px', _mmt("Initial Payment or One-Time Payment"));
			break;
		case MM_TransactionLog::$TRANSACTION_TYPE_RECURRING_PAYMENT:
			$transactionTypeIcons .= MM_Utils::getIcon('refresh', 'green', '1.4em', '2px',  _mmt("Recurring Payment", "padding-left:2px;"));
			break;
		case MM_TransactionLog::$TRANSACTION_TYPE_REFUND:
			$transactionTypeIcons .= MM_Utils::getIcon('money', 'red', '1.4em', '2px',  _mmt("Refund"));
			break;
		default:
			$transactionTypeIcons .= MM_NO_DATA;
			break;
	}
	
	$affiliateId = MM_NO_DATA;
	$subAffiliateId = MM_NO_DATA;
	if(!empty($item->affiliate_id))
	{
		$affiliateId = $item->affiliate_id;
	}
	
	if(!empty($item->sub_affiliate_id))
	{
		$subAffiliateId = $item->sub_affiliate_id;
	}

	if($item->is_test)
	{
		$transactionTypeIcons .= MM_Utils::getIcon('flask', 'grey', '1.3em', '2px', "Test Transaction", "padding-left:4px;");
	}
	
	// build datagrid row
	$row = array();
    $row[] = array('content' => $transactionTypeIcons);
	$row[] = array('content' => MM_Utils::dateToLocal($item->transaction_date));
	$row[] = array('content' => "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_TRANSACTION_HISTORY)."&user_id={$item->user_id}'>{$item->order_number}</a>");
	$row[] = array('content' => _mmf($item->transaction_amount,$item->currency));
	$row[] = array('content' => $fullName);
	$row[] = array('content' => "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_GENERAL)."&user_id={$item->user_id}'>".MM_Utils::abbrevString($item->user_email, 30)."</a>");
	$row[] = array('content' => "<span style='font-family:courier; font-size:11px;'>".MM_Utils::abbrevString($affiliateId, 15)."</span>");
	$row[] = array('content' => "<span style='font-family:courier; font-size:11px;'>".MM_Utils::abbrevString($subAffiliateId, 15)."</span>");
	$row[] = array('content' => MM_Utils::abbrevString($item->description, 30));
	$row[] = array('content' => $item->payment_service);
    
	$datagridRows[] = $row;
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($datagridRows);

$dgHtml = $dataGrid->generateHtml();

if(empty($dgHtml)) 
{
	$dgHtml = "<p><i>"._mmt("No transactions found")."."."</i></p>";
}

echo $dgHtml;
?>