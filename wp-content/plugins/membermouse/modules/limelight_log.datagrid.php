<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_LimelightLogView();

if(!empty($_REQUEST["sortby"]))
{
	$dataGrid = new MM_DataGrid($_REQUEST, $_REQUEST["sortby"], "desc", 20);
}
else
{
	$dataGrid = new MM_DataGrid($_REQUEST, "id", "desc", 20);
}
$data = $view->getViewData($_REQUEST, $dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "IPN";

$rows = array();
$headers = array();

foreach($data as $key=>$item)
{	   
	// IPN Details
	$ipnDetails = MM_NO_DATA;
	$ipnStr = "";
	?>
		<div id="mm-view-info-<?php echo $item->id; ?>" style="display:none;" title="IPN Details" style="font-size:11px;">
			<table style="width:100%">
			<?php  
				$altRow = false;
				
				foreach($item as $key=>$value)
				{
					$ipnStr.="{$key}: {$value}\n";
					$bkgdColor = "#f9f9f9";
					
					if($altRow)
					{
						$bkgdColor = "#fff";
					} 
					echo "<tr style='background-color:{$bkgdColor}'><td style='padding:2px; padding-left:5px; font-size:10px; color:#666'>";
					echo "<strong>{$key}</strong>";
					echo "</td><td style='padding:2px; padding-left:5px;'>";
					echo "<span style='font-family:courier;'>{$value}</span>";
					echo "</td></tr>";
					
					$altRow = !$altRow;
				}
			?>
			</table>
		</div>
	<?php 
	$ipnDetails = "<a href='javascript:viewInfo({$item->id})'>View IPN Details</a>";
	
	$crntPaymentService = MM_PaymentServiceFactory::getPaymentService(MM_PaymentService::$LIMELIGHT_SERVICE_TOKEN); 
	$orderLink = "<span style='font-family:courier;'>".$crntPaymentService->getLoggingOrderUrl($item->order_id)."</span>";
	$customerLink = "<span style='font-family:courier;'>".$crntPaymentService->getLoggingCustomerUrl($item->customer_id)."</span>";

	$user = new MM_User($item->user_id);
	$memberLink = MM_NO_DATA; 
	if($user->isValid())
	{
		$memberLink = $user->getUsername();
		$memberLink = "<a href='?page=".MM_MODULE_MANAGE_MEMBERS."&module=details_general&user_id=".$item->user_id."'>".$user->getUsername()."</a>";
	}
		
	$icon = MM_Utils::getIcon('info', 'blue', '1.3em', '2px', $ipnStr, "padding-left:4px;");
	if($item->log_level==MM_LimeLightService::$LOG_LEVEL_WARNING){
		$icon = MM_Utils::getIcon('warning', 'yellow', '1.3em', '2px', $ipnStr, "padding-left:4px;");
	}
	else if($item->log_level==MM_LimeLightService::$LOG_LEVEL_ERROR){
		$icon = MM_Utils::getIcon('warning', 'red', '1.3em', '2px', $ipnStr, "padding-left:4px;");
	}
	
	$row = array();
	$row[] = array('content' => $icon);
	$row[] = array('content' => $memberLink);
	$row[] = array('content' => $customerLink);
	$row[] = array('content' => $orderLink); 
	$row[] = array('content' => MM_Utils::abbrevString($item->message, 50));
	$row[] = array('content' => $ipnDetails);
	if(!is_null($item->date_received) && !empty($item->date_received) && !preg_match("/(000)/", $item->date_received))
		$row[] = array('content' => MM_Utils::dateToLocal($item->date_received));
	else
		$row[] = array('content' => '-');
	$rows[] = $row;
}
/*
 * 'form_submission_id', 'status', 'is_test', 'total','date_received', 'order_id', 'order_number', 'user_id'
 */
$headers['icon'] = array('content' =>'',"attr" => "style='width:20px;'");
$headers['user_id'] = array('content' => '<a onclick="mmjs.sort(\'user_id\');" href="#">Member</a>', "attr" => "style='width:250px;'");
$headers['customer_id'] = array('content' => '<a onclick="mmjs.sort(\'customer_id\');" href="#">Customer ID</a>', "attr" => "style='width:80px;'");
$headers['order_id'] = array('content' => '<a onclick="mmjs.sort(\'order_id\');" href="#">Order#</a>', "attr" => "style='width:80px;'");	
$headers['message'] = array('content' => 'Message', "attr" => "style='width:250px;'");
$headers['is_test'] = array('content' => 'IPN Details', "attr" => "style='width:150px;'");
$headers['date_received'] = array('content' => '<a onclick="mmjs.sort(\'date_received\');" href="#">Date</a>', "attr" => "style='width:150px;'");

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") 
{
	$dgHtml = "<p><i>No Limelight Logs found.</i></p>";
}

echo $dgHtml;
?>