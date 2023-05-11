<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_CouponLogView();

if(!empty($_REQUEST["sortby"]))
{
	$dataGrid = new MM_DataGrid($_REQUEST, $_REQUEST["sortby"], "desc", 10);
}
else
{
	$dataGrid = new MM_DataGrid($_REQUEST, "o.date_added", "desc", 10);
}
$data = $view->getViewData($_REQUEST, $dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = _mmt("Entry");

$rows = array();
$headers = array();

foreach($data as $key=>$item)
{	
    // Order # 
   $orderNumber = MM_NO_DATA;
   
    // Email
    if(!empty($item->user_id))
    {
        $user = new MM_User($item->user_id);
    	
    	if($user->isValid())
    	{
    	    $email = "<a href='?page=".MM_MODULE_MANAGE_MEMBERS."&module=details_general&user_id=".$user->getId()."'>".$user->getEmail()."</a>";
    	}
    	
    	if(!empty($item->order_number))
    	{
    	    $orderNumber = "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_TRANSACTION_HISTORY)."&user_id={$item->user_id}'>{$item->order_number}</a>";
    	}
    }
	
	// Name
	$name = MM_NO_DATA;
	
	if(!empty($item->billing_last_name))
	{
	    $name = $item->billing_last_name;
	    
	    if(!empty($item->billing_first_name))
	    {
	        $name .= ", ".$item->billing_first_name." ";
	    }
	}
	
    // Coupon Code
	$couponCode = MM_NO_DATA;
	
	if(!empty($item->coupon_code))
	{
	    $couponCode = "<span style='font-family:courier;'>{$item->coupon_code}</span>";
	}
	
	$row = array();
	$row[] = array('content' => $email);
	$row[] = array('content' => $orderNumber);
	$row[] = array('content' => $couponCode);
	$row[] = array('content' => MM_Utils::dateToLocal($item->date_added));
	
	$rows[] = $row;
}

$headers['email'] = array('content' => _mmt('Member'));
$headers['o.order_number'] = array('content' => '<a onclick="mmjs.sort(\'o.order_number\');" href="#">'._mmt('Order #').'</a>');
$headers['c.coupon_code'] = array('content' => '<a onclick="mmjs.sort(\'c.coupon_code\');" href="#">'._mmt('Coupon Code').'</a>');
$headers['o.date_added'] = array('content' => '<a onclick="mmjs.sort(\'o.date_added\');" href="#">'._mmt('Date Used').'</a>');

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") 
{
	$dgHtml = "<p><i>"._mmt("No entries found.")."</i></p>";
}

echo $dgHtml;
?>